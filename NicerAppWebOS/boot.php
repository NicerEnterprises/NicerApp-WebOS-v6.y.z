<?php
define ("SESSION_ERRORS_ID", "NicerApp_WebOS_errors_PHP");
define ("SEID", SESSION_ERRORS_ID);

require_once (dirname(__FILE__).'/functions.php');
require_once (dirname(__FILE__).'/logic.business/class.NicerAppWebOS.php');
require_once (dirname(__FILE__).'/logic.business/class.NicerAppWebOS.log.php');
require_once (dirname(__FILE__).'/logic.business/class.NicerAppWebOS.errorHandler.php');

$rootPath_na = realpath(dirname(__FILE__).'/..');
$rootPath_na_dbs = $rootPath_na.'/NicerAppWebOS/logic.databases/generalizedDatabasesAPI-1.0.0';
require_once ($rootPath_na_dbs.'/class.database_API.php');
//require_once ($rootPath_na_dbs.'/connectors/forFuture_design_coding_debugging_and_usage/class.fileSystemDB-1.0.0.php');

//require_once ($rootPath_na_dbs.'/connectors/forFuture_design_coding_debugging_and_usage/class.adodb5_1.0.0.php');
//require_once ($rootPath_na.'/NicerAppWebOS/3rd-party/adodb5/adodb.inc.php');

require_once ($rootPath_na_dbs.'/connectors/class.couchdb-3.2.2_1.0.1.php');
// Sag, the business code layer that i use towards the couchdb.apache.org database system.
require_once($rootPath_na.'/NicerAppWebOS/3rd-party/sag/src/Sag.php');
require_once ($rootPath_na.'/NicerAppWebOS/Sag-support-functions.php');


$naDebugAll = true;
global $naDebugAll;
if ($naDebugAll) {
    ini_set('display_errors', 1); // 0 == false, 1 == true
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    global $naBypassMainErrorHandler;
    //if (!isset($naBypassMainErrorHandler) || $naBypassMainErrorHandler)
        //$old_error_handler = set_error_handler ('mainErrorHandler');

    /*
    global $mainErrorLogFilepath; global $mainErrorLogLastWriteFilepath;
    $mainErrorLogFilepath = realpath(dirname(__FILE__)).'/siteLogs/error.'.date('Y-m-d_H.i.s').'.html.log';
    $mainErrorLogLastWriteFilepath = realpath(dirname(__FILE__)).'/siteLogs/error.'.date('Y-m-d_H.i.s').'.lastModified.txt';
    */
}
ini_set ('log_errors', true);

global $naIP;
if (
    function_exists('apache_request_headers')
    && array_key_exists('X-Forwarded-For',apache_request_headers())
) {
    $naIP = apache_request_headers()['X-Forwarded-For'];
} elseif (array_key_exists('REMOTE_ADDR', $_SERVER)) {
    $naIP = $_SERVER['REMOTE_ADDR'];
} else {
    $naIP = 'OS commandline probably';
}

define ("FILE_FORMATS_photos", "/^(.*\.png)|(.*\.gif)|(.*\.jpg)|(.*\.jpeg)$/");
define ("FILE_FORMATS_mp3s", "/^(.*\.mp3)$/");
define ("FILE_FORMATS_texts", "/^(.*\.txt)$/");
define ("FILE_FORMATS_html", "/^(.*\.html)$/");
define ("FILE_FORMATS_photos_texts", "/^(.*\.png)|(.*\.gif)|(.*\.jpg)|(.*\.jpeg)|(.*\.txt)$/");
define ("FILE_FORMATS_NO_thumbs", '/(?!.*thumbs).*/');


if (php_sapi_name() !== 'cli') {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
        session_start();


        /*
        if (isset($_COOKIE) && is_array($_COOKIE) && array_key_exists('cdb_loginName', $_COOKIE) && array_key_exists('cdb_authSession_cookie', $_COOKIE)) {
            $_SESSION['cdb_authSession_cookie'] = $_COOKIE['cdb_authSession_cookie'];
            $_SESSION['cdb_loginName'] = $_COOKIE['cdb_loginName'];
        } else {
            $_SESSION['cdb_authSession_cookie'] = null;
            $_SESSION['cdb_loginName'] = 'Guest';
        }*/
    } else {
        session_start();
    }

    if ($_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/index.php') {
    }
}

global $naWebOS;
$naWebOS = new NicerAppWebOS();




    //echo '<pre>t555'; var_dump ($_SERVER); exit();
    if ($_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/index.php') {
        $_SESSION['started'] = time();//microtime(true);
        $_SESSION['startedID'] = cdb_randomString(50);

        $now = DateTime::createFromFormat('U', $_SESSION['started']);
        $now->setTimezone(new DateTimeZone(exec('date +%z')));
        //$date = $now->format("Y-m-d_H:i:s.u");
        $date =
            $now->format("Y-m-d_H:i:s_")
            .str_replace(
                '+','plus',
                preg_replace('/.*\s/','',date(DATE_RFC2822))
            );
        $naBot = stripos($_SERVER['HTTP_USER_AGENT'], 'bot')!==false;
        //echo '<pre>t584'; var_dump($naBot); exit();

        if (!$naBot) {
            $_SESSION['na_error_log_filepath_html'] =
                '/var/www/'.$naWebOS->domain.'/NicerAppWebOS/siteLogs/'
                .$date.'-'.$naIP.($naBot?'-BOT':'').'.html';
            $_SESSION['na_error_log_filepath_txt'] =
                '/var/www/'.$naWebOS->domain.'/NicerAppWebOS/siteLogs/'
                .$date.'-'.$naIP.($naBot?'-BOT':'').'.txt';
        } else {
            $_SESSION['na_error_log_filepath_html'] = null;
            $_SESSION['na_error_log_filepath_txt'] = null;
        }

        $_SESSION['dbgNum'] = 0;
        $_SESSION['dbgNum2'] = 0;
        $_SESSION['hmTopIdx'] = 0;


        $_SESSION['logsInitialized'] = false;
        $_SESSION[SEID] = [];
        $_SESSION['naWebOS_errors_startup'] = [];

        // outdated?
        $_SESSION['naErrors'] = [];
        $_SESSION['naErrors_startup'] = [];
        $_SESSION['naErrors_js'] = [ 'bootup' => [] ];
    } elseif ($_SERVER['SCRIPT_NAME']=='/NicerAppWebOS/db_init.php') {
        $_SESSION['started'] = time();//microtime(true);

        $now = DateTime::createFromFormat('U', $_SESSION['started']);
        $now->setTimezone(new DateTimeZone(exec('date +%z')));
        //$date = $now->format("Y-m-d_H:i:s.u");
        $date =
            $now->format("Y-m-d_H:i:s_")
            .str_replace(
                '+','plus',
                preg_replace('/.*\s/','',date(DATE_RFC2822))
            );

        $_SESSION['na_error_log_filepath_html'] =
            '/var/www/'.$naWebOS->domain.'/NicerAppWebOS/siteLogs/'
            .$date.'-'.$naIP.'-db_init.html';
        $_SESSION['na_error_log_filepath_txt'] =
            '/var/www/'.$naWebOS->domain.'/NicerAppWebOS/siteLogs/'
            .$date.'-'.$naIP.'-db_init.txt';

        $_SESSION['dbgNum'] = 0;
        $_SESSION['dbgNum2'] = 0;
        $_SESSION['hmTopIdx'] = 0;


        $_SESSION['logsInitialized'] = false;
        $_SESSION[SEID] = [];
        $_SESSION['naWebOS_errors_startup'] = [];

        // outdated?
        $_SESSION['naErrors'] = [];
        $_SESSION['naErrors_startup'] = [];
        $_SESSION['naErrors_js'] = [ 'bootup' => [] ];
    }

    global $na_full_init;
    if (!isset($na_full_init)) $na_full_init = true;
    if ($na_full_init) {
        // MUST BE in the following order:
        $naWebOS->initializeDatabases();
        $naWebOS->initializeGlobals();
    }

$appName = '[UNKNOWN_APP]';
if (is_object($naWebOS) && property_exists($naWebOS,'view') && is_array($naWebOS->view))
foreach ($naWebOS->view as $an => $ar) {
    if ($an=='/') $appName = '-';
    else {
        $ap = explode('/', $an);
        $appName = $ap[count($ap)-1];
    }
    break;
}


    $dbg = null;
    $dbg = [
        '$_SERVER' => $_SERVER,
        '$_GET' => $_GET,
        '$_POST' => $_POST,
        '$naWebOS->view' => property_exists($naWebOS,'view') ? $naWebOS->view : [ 'app' => 'NOAPP' ]
    ];
    //$msg = 'NEW REQUEST :<br/>'.hmJSON($dbg,'$dbg');

    global $naIsBot; global $naIsDesktop; global $naIsMobile; global $naBrowserMarketSharePercentage;
    $naIsDesktop = false;
    $naIsMobile = false;
    $naBrowserMarketSharePercentage = -1;
    if (!array_key_exists('HTTP_USER_AGENT',$_SERVER)) $naIsBot = true;
    else {
        $naIsBot = preg_match('/bot/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/\+https:\/\//i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/externalhit/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/crawler/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/spider/i', $_SERVER['HTTP_USER_AGENT']) === 1;
        if (!$naIsBot)
            $naIsBot = preg_match('/scaninfo\@paloanetworks\.com/i', $_SERVER['HTTP_USER_AGENT']) === 1;

        $fn1 = dirname(__FILE__).'/apps/NicerAppWebOS/applications/2D/logs/userAgents.desktop.2023-12-02.json';
        $json1 = json_decode(file_get_contents($fn1), true);
        $fn2 = dirname(__FILE__).'/apps/NicerAppWebOS/applications/2D/logs/userAgents.mobile.2023-12-02.json';
        $json2 = json_decode(file_get_contents($fn2), true);

        //$naIsBot = true;
        foreach ($json1 as $idx => $jrec) {
            if ($jrec['ua']===$_SERVER['HTTP_USER_AGENT']) {
                //$naIsBot = false;
                $naIsDesktop = true;
                $naBrowserMarketSharePercentage = $jrec['pct'];
            }
        }
        foreach ($json2 as $idx => $jrec) {
            if ($jrec['ua']===$_SERVER['HTTP_USER_AGENT']) {
                //$naIsBot = false;
                $naIsMobile = true;
                $naBrowserMarketSharePercentage = $jrec['pct'];
            }
        }
    }
    //echo '<p style="color:purple">'; var_dump($naIsBot); echo '</p>';

    $lanConfigFilepath = realpath(dirname(__FILE__)).'/domainConfigs/'.$naWebOS->domain.'/naLAN.json';
    $lanConfigExampleFilepath = realpath(dirname(__FILE__)).'/domainConfigs/'.$naWebOS->domain.'/naLAN.EXAMPLE.json';
    if (!file_exists($lanConfigFilepath))
        trigger_error ('"'.$lanConfigFilepath.'" does not exist. See "'.$lanConfigExampleFilepath.'" for a template.', E_USER_ERROR);
    $lanConfigRaw = file_get_contents($lanConfigFilepath);
    $lanConfig = json_decode($lanConfigRaw, true);
    checkForJSONerrors($lanConfigRaw, $lanConfigFilepath, $lanConfigExampleFilepath);

    global $naLAN;
    $naLAN = (
        $naIP === '::1'
        || $naIP === '127.0.0.1'
        || in_array($naIP, $lanConfig)
    );
    if ($naLAN && array_key_exists('logsInitialized', $_SESSION) && !$_SESSION['logsInitialized']) {
        /*
        $html = $naWebOS->getSite();
            //'<html><head>'
            //.PHP_EOL.$naWebOS->getLinks($naWebOS->cssFiles)
            //.PHP_EOL.$naWebOS->getLinks($naWebOS->javascriptFiles).PHP_EOL
            //.'</head><body style="overflow:visible"><div id="siteBackground"></div>';*/
        //$html = '<script type="text/javascript" src="/NicerAppWebOS/logic.business/debug-1.0.0.source.js?c='.date('Ymd_His',filemtime(dirname(__FILE__).'/logic.business/debug-1.0.0.source.js')).'"></script>';
        //file_put_contents ($_SESSION['na_error_log_filepath_html'], $html, FILE_APPEND);

        $_SESSION['logsInitialized'] = true;
    }

    global $phpScript_startupTime;
    global $naIP;
    global $naVersionNumber;
    $time = microtime(true) - $phpScript_startupTime;
    //var_dump (dirname(__FILE__).'/errors.css');
    //date_default_timezone_set('UTC');
    $dtz = new DateTime('now');//new DateTimeZone(date_default_timezone_get());
    $dtz_offset = $dtz->getOffset();
    //$unixTimeStamp = time();//date(DATE_ATOM);//date(DATE_RFC2822);//date('Y-m-d H:i:sa');
    $timestamp = date(DATE_RFC2822);

    if (array_key_exists('na_error_log_filepath_txt',$_SESSION))   ini_set ('error_log', $_SESSION['na_error_log_filepath_txt']);

    // at the *bottom* of this file (that's for good reasons),
    // you will find : require_once(dirname(__FILE__).'/apps/nicer.app/api.paymentSystems/boot.php');

    ini_set('memory_limit','256M');
    set_time_limit(60); // 60 seconds

    //echo '<pre>'; var_dump ($_SERVER); exit();


    // make globals variable holding the version number
    $naVersionNumber = file_get_contents(dirname(__FILE__).'/VERSION.txt');
    global $naVersionNumber;

    // overrides by the site operator go here :
    // NOTE : YOU WILL LIKELY HAVE TO CHANGE global $filePerms_ownerUser, defined in this file.
    $fn = dirname(__FILE__).'/apps/siteOperator_boot.php';
    if (file_exists($fn)) require_once ($fn);
?>
