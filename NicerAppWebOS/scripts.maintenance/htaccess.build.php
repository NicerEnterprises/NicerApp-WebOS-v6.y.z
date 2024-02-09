<?php
$rootPath2 = realpath(dirname(__FILE__).'/../..');
require_once ($rootPath2.'/NicerAppWebOS/boot.php');
global $naWebOS;
$fncn =
    realpath ( dirname (__FILE__).'/../..' )
    .'/NicerAppWebOS/scripts.maintenance/htaccess.build.php';

echo $fncn.' : STARTING'.PHP_EOL;

$fc = file_get_contents (dirname(__FILE__).'/.htaccess.build.part1.txt');
$fc .= PHP_EOL.PHP_EOL;

$fc .= PHP_EOL.PHP_EOL;
$fc .= file_get_contents (dirname(__FILE__).'/.htaccess.build.partLast.txt');
$fc .= PHP_EOL.PHP_EOL;

$bytes = file_put_contents (realpath(dirname(__FILE__).'/../..').'/.htaccess', $fc);
echo $bytes.' bytes written to .../.htaccess and .../.htaccess-reverse'.PHP_EOL;

$db = $naWebOS->dbsAdmin->findConnection('couchdb');
//echo '<pre>t3334 : '; var_dump ($db->security_guest); exit();
$cdb = $db->cdb;
$dataSetName1 = $db->dataSetName('views');
$dataSetName2 = $db->dataSetName('viewsIDs');

try {
    $call = $cdb->deleteDatabase($dataSetName1);
    echo 'Deleted database '.$dataSetName1.PHP_EOL;
} catch (Exception $e) {
    echo
        'While deleting database '
        .'"'.$dataSetName1.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
    if (strpos($e->getMessage(), 'Database does not exist')===false) exit();
}
$cdb->setDatabase ($dataSetName1, true);
if (is_null($db->security_guest)) { trigger_error ('FATAL ERROR : $db->security_guest is null. see $db->setGlobals()', E_USER_ERROR); exit(); }
try {
    $call = $cdb->setSecurity ($db->security_guest);
} catch (Exception $e) {
    echo
        'While setting security to "Guest" for '
        .'"'.$dataSetName1.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
    exit();
}

try {
    $call = $cdb->deleteDatabase($dataSetName2);
    echo 'Deleted database '.$dataSetName2.PHP_EOL;
} catch (Exception $e) {
    echo
        'While deleting database '
        .'"'.$dataSetName1.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
    if (strpos($e->getMessage(), 'Database does not exist')===false) exit();
}
$cdb->setDatabase ($dataSetName2, true);
if (is_null($db->security_guest)) { trigger_error ('FATAL ERROR : $db->security_guest is null. see $db->setGlobals()', E_USER_ERROR); exit(); }
try {
    $call = $cdb->setSecurity ($db->security_guest);
} catch (Exception $e) {
    echo
        'While setting security to "Guest" for '
        .'"'.$dataSetName1.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
    exit();
}

$cdb->setDatabase ($dataSetName1, true);



$fc2fn =
    realpath(dirname(__FILE__).'/../..')
    .'/NicerAppWebOS/domainConfigs/'.$naWebOS->domain.'/databases.views.json';
if (!file_exists($fc2fn)) {
    $msg = $fncn.' : !file_exists("'.$fc2fn.'")';
    trigger_error ($msg, E_USER_ERROR);
    echo $msg;
    exit();
}
if (!is_readable($fc2fn)) {
    $msg = $fncn.' : !is_readable("'.$fc2fn.'")';
    trigger_error ($msg, E_USER_ERROR);
    echo $msg;
    exit();
}
$fc2 = safeLoadJSONfile($fc2fn);

$id1 = randomString(50);
$id2 = randomString(50);
$viewIdx = 0;
$viewIDsIdx = 0;
foreach ($fc2 as $appName => $appSettings) {
    $viewIdx++;
    $appParams = $appSettings['viewSettings'];
    $rec1 = [
        '_id' => $id1,
        'view' => $appParams
    ];

    try {
        $cdb->setDatabase($dataSetName1, false);
        $cdb->post($rec1);
    } catch (Throwable $e) {
        echo $fncn.' : FATAL ERROR : Could not add document to dataSet "'.$dataSetName1.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
        exit();
    } catch (Exception $e) {
        echo $fncn.' : FATAL ERROR : Could not add document to dataSet "'.$dataSetName1.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
        exit();
    }

    foreach ($appSettings['viewSettings']['seo'] as $idx => $seoValue) {
        $viewIDsIdx++;
        $id2 = randomString(40);
        $rec2 = [
            '_id' => $id2,
            'seoValue' => $seoValue,
            'viewID' => $id1
        ];
        try {
            $cdb->setDatabase($dataSetName2, true);
            $cdb->post($rec2);
        } catch (Throwable $e) {
            echo $fncn.' : FATAL ERROR : Could not add document to dataSet "'.$dataSetName2.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
            exit();
        } catch (Exception $e) {
            echo $fncn.' : FATAL ERROR : Could not add document to dataSet "'.$dataSetName2.'" : $e->getMessage()='.$e->getMessage().PHP_EOL;
            exit();
        }
    }
}
echo $viewIdx.' documents written into the "'.$dataSetName1.'" database'.PHP_EOL;
echo $viewIDsIdx.' documents written into the "'.$dataSetName2.'" database'.PHP_EOL;
echo $fncn.' : DONE (SUCCESS)'.PHP_EOL;
?>
