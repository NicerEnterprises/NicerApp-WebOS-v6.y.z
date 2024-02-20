<?php

class NicerAppWebOS {
    public $cn = '.../NicerAppWebOS/businessLogic/class.NicerAppWebOS.php::class NicerAppWebOS';
    public $about = '';
    public $dbs = null;
    public $dbsAdmin = null;

    public function __construct () {
        $fncn = $this->cn.'->__construct()';
        $this->basePath = realpath(dirname(__FILE__).'/../..');

        $this->about = json_decode (file_get_contents(dirname(__FILE__).'/../version.json'));
        $this->browserDebug = false;
        $this->debugThemeLoading = false;

        $this->baseIndentLevel = 1;

        $p1 = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
        $p2 = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..');
        $this->path = $p2;
        $this->domain = str_replace($p1.DIRECTORY_SEPARATOR,'', $p2);
        //var_dump ($this->domain); exit();

        $rp_domain = $this->path.'/NicerAppWebOS/domainConfigs/'.$this->domain;
        $this->cssFiles = [
            [ 'indexFile' => $rp_domain.'/index.css.json', 'type' => 'css' ],
            [ 'files' => $this->getVividButtonCSSfiles(), 'type' => 'css' ] //already included in "indexFile".
        ];
        $this->javascriptFiles = [
            [ 'indexFile' => $rp_domain.'/index.javascripts.json', 'type' => 'javascript' ]//,
            //[ 'indexFile' => $rp_domain.'/index.customerJavascripts.json', 'type' => 'javascript' ],
            //[ 'files' => $this->getVividButtonJavascriptFiles(), 'type' => 'javascript' ] // already included in "indexFile"
        ];

        $dfd = strtolower($this->domain);
        $dfd = str_replace('.', '_', $dfd);
        if (preg_match('/^\d/', $dfd)) {
            $dfd = 'number_'.$dfd;
            //$this->browserDebug = true; // TODO : CSS CONCATENATION DOES NOT WORK YET, FOR SOME STRANGE REASON
        }
        $this->domainForDB = $dfd;

        $fnOwnerInfo = realpath(dirname(__FILE__).'/..').'/domainConfigs/'.$this->domain.'/company.owner.json';
        if (!file_exists($fnOwnerInfo)) {
            $msg = $fncn.' : file "'.$fnOwnerInfo.'" missing. please re-run .../NicerAppWebOS/scripts.install/install-NicerAppWebOS???.sh from the commandline.';
            trigger_error ($msg, E_USER_ERROR);
            exit();
        }
        if (!is_readable($fnOwnerInfo)) {
            $msg = $fncn.' : file "'.$fnOwnerInfo.'" is not readable. please re-run .../NicerAppWebOS/scripts.maintenance/setPermissions.sh from the commandline.';
            trigger_error ($msg, E_USER_ERROR);
            exit();
        }
        $this->ownerInfo = safeLoadJSONfile($fnOwnerInfo);

        $midsFile =
            $this->path.'/NicerAppWebOS/apps/'
            .'manufacturerNameForDomainName_'.$this->domain.'_val.txt';
        if (!file_exists($midsFile))
            trigger_error ($fncn.' : file "'.$midsFile.'" does not exist', E_USER_ERROR);
        if (!is_readable($midsFile))
            trigger_error ($fncn.' : file "'.$midsFile.'" is not readable. run .../NicerAppWebOS/scripts.maintenance/setPermissions.sh from the commandline please', E_USER_ERROR);
        $mfc = trim(file_get_contents ($midsFile));

        $this->viewsMIDpath =
            $this->path.'/NicerAppWebOS/apps/'
            .str_replace($this->domain, $mfc, $midsFile);
        $this->viewsMID = $mfc;
    }

    public function getSite() {
        $rp_domain = $this->path.'/NicerAppWebOS/domainConfigs/'.$this->domain;
        $templateFile = $rp_domain.'/index.template.php';
        $titleFile = $rp_domain.'/index.title.php';

        if ($this->browserDebug) {
            $cssLinks = $this->getConcatenatedLinks ($this->cssFiles);
            $javascriptLinks = $this->getConcatenatedLinks ($this->javascriptFiles);
        } else {
            $cssLinks = $this->getLinks ($this->cssFiles);
            $javascriptLinks = $this->getLinks ($this->javascriptFiles);
        }

        $desktopDefinition = file_get_contents ($rp_domain.'/index.desktopDefinition.json');

        $content = $this->getContent();
        $replacements = array (
            //'{$view}' => ( is_array($view) ? json_encode($view, JSON_PRETTY_PRINT) : '{}' ),
            '{$title}' => array_key_exists('title',$content) && is_string($content['title']) && $content['title']!==''?$content['title']:execPHP($titleFile),
            '{$domain}' => $this->domain,
            '{$cssLinks}' => $cssLinks,
            '{$javascriptLinks}' => $javascriptLinks,
            '{$desktopDefinition}' => $desktopDefinition,
            //'{$customerHTML}' => $templateCustomer,
            '{$pageSpecificCSS}' => $this->getPageCSS(),
            //'{$theme}' => $this->cssTheme,
            //'{$viewport}' => $this->getMetaTags_viewport(),
            //'{$siteMenu_avoid}' => $siteMenu_avoid
        );

        foreach ($content as $divName=>$contentForDiv) {
            //$contentForDiv = htmlentities($contentForDiv);
            //$arr = array ( '{$div_'.$divName.'}' => $contentForDiv );
            $replacements['{$div_'.$divName.'}'] = $contentForDiv;// = array_merge ($replacements, $arr);
        }
        $searches = array_keys($replacements);
        $replaces = array_values($replacements);
        $html = require_return($templateFile, false);
        //echo '<pre>'; var_dump ($searches); var_dump ($replaces); exit();
        $html = timestampJSmodule ($html);
        //var_dump($html); exit();
        $html5 = str_replace ($searches, $replaces, $html);
        echo $html5;

        //echo '<pre>'; var_dump ($this->about); var_dump($this->path); echo '</pre>';
    }






    public function initializeDatabases () {
        $fncn = $this->cn.'->initializeDatabases()';

        $this->hasDB = false;
        if ($this->dbs===null) {
            $this->dbs = 'initializing';
            // logged in as the end-user.
            //$this->db = new class_NicerAppWebOS_database_API_couchdb_3_2 (clone $this, false);
            try {
                $this->dbs = new class_NicerAppWebOS_database_API ('Guest');
                //echo '<pre>'; var_dump ($this->dbs);exit();
                setcookie('cdb_loginName', $this->dbs->findConnection('couchdb')->username, time() + 604800, '/');





            //echo '<pre>'; var_dump ($this->dbs); exit();

                if (php_sapi_name() !== 'cli') {
                    //WILL NEVER WORK; HANDLED BY logic.AJAX/ajax_testDBconnection.php! setcookie('cdb_loginName' ,$this->dbs->findConnection('couchdb')->username, time() + 604800, '/');
                    $_SESSION['cdb_loginName'] = $this->dbs->findConnection('couchdb')->username;
                }

                $this->hasDB = true;
            } catch (Throwable $error) {
                echo '<pre>';
                echo $fncn.' : Throwable $error=';//.json_encode($error,JSON_PRETTY_PRINT);;
                var_dump ($error);
                echo '</pre>';
                exit();
            } catch (Exception $error) {
                echo '<pre>';
                echo $fncn.' : Exception $error=';//.json_encode($error,JSON_PRETTY_PRINT);;
                var_dump ($error);
                echo '</pre>';
                exit();
            }
        }

        if ($this->dbsAdmin===null) {
            $this->dbsAdmin = 'initializing';
            // logged in as $cdbConfig['adminUsername']!
            //$this->dbAdmin = new class_NicerAppWebOS_database_API_couchdb_3_2 (clone $this, true);
            $this->dbsAdmin = new class_NicerAppWebOS_database_API ('admin');
            try {
                //q$this->dbsAdmin = new class_NicerAppWebOS_database_API ('admin');

                if (php_sapi_name() !== 'cli') {
                    //WILL NEVER WORK; HANDLED BY logic.AJAX/ajax_testDBconnection.php! setcookie('cdb_admin_loginName' ,$this->dbsAdmin->findConnection('couchdb')->username, time() + 604800, '/');
                    $_SESSION['cdb_admin_loginName'] = $this->dbsAdmin->findConnection('couchdb')->username;
                }

                $this->hasDB = true;
            } catch (Throwable $e) {
                echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;padding:8px;">';
                echo json_encode($e->getMessage());
                echo '</pre>';
                exit;
            } catch (Exception $e) {
                echo '<pre style="color:white;background:rgba(0,50,0,0.5);border-radius:10px;padding:8px;">';
                echo json_encode($e->getMessage());
                echo '</pre>';
                exit;
            }
        }
        $this->dbs->setGlobals($this->dbs->findConnection('couchdb')->username);
        $this->dbsAdmin->setGlobals($this->dbsAdmin->findConnection('couchdb')->username);

        $this->initialized = true;
    }

    public function initializeGlobals() {
        $fncn = $this->cn.'->initializeGlobals()';
        $view = $fncn.' : FATAL ERROR : Could not look up view settings in database.<p style="color:blue;background:rgba(255,255,255,0.65);">$_GET='.str_replace("\n", "<br/>\n", json_encode($_GET,JSON_PRETTY_PRINT)).'</p>'; // assume the worst

        $this->globals = [
            'cdbDesignDocs' => [
                'logentries_pageLoad' => '_design/b13265782ef772fafebf4ce4c02d6605f0412f73'
            ]
        ];

        //$this->comments = new class_NicerApp_WebOS_siteComments();

        global $argv;
        $this->url = array_key_exists('REQUEST_URI', $_SERVER)
            ? $_SERVER['REQUEST_URI']
            : $argv[0];
        $this->showAllErrors = !array_key_exists('sae', $_REQUEST) || $_REQUEST['sae']==='y';

        $this->view = [
            '/' => [
                'page' => 'error'
            ]
        ];
        if (array_key_exists('viewID', $_GET)) {
            if ($_GET['viewID']=='' || $_GET['viewID']=='/') {
                $view = ['/'=>['page'=>'index']];
            } else {
                $decoded = json_decode(base64_decode_url($_GET['viewID']), true);
                if (json_last_error()!==0) {
                    $this->view = $this->getView ($_GET['viewID']);
                } else {
                    $this->view = $decoded;
                }
            }
        } elseif (array_key_exists('seoValue',$_GET)) {
                $decoded = json_decode(base64_decode_url($_GET['seoValue']), true);
                if (json_last_error()!==0) {
                    $this->view = $this->getView($_GET['seoValue']);
                } else {
                    $this->view = $decoded;
                }
        }
        return is_array($this->view);
    }









    public function getContent ($divID=null, $viewID=null) {
        if (is_null($viewID)) $view = $this->view; else {
            $view = $this->getView($viewID);
        }
        if (!is_array($_GET)) {
            $msg = $fncn.' : FAILED (this was not called via a web-browser).';
            trigger_error ($msg, E_USER_ERROR);
            return $this->getContent__standardErrorMessage($msg);
        } else {
            if (
                $this->nonEmptyStringField('username',$_GET)
                && $this->nonEmptyStringField('url1',$_GET)
                && $this->nonEmptyStringField('dataID',$_GET)
            ) return $this->getContent__data_by_users ($_GET['username'], $_GET['url1'], $_GET['dataID']);

            elseif ($this->nonEmptyStringField('app-wikipedia_org', $_GET))
                return $this->getContent__view_wikipedia ($_GET['app-wikipedia_org']);

            elseif ( $this->nonEmptyStringField('viewID',$_GET) )
                return $this->getContent__view ($_GET['viewID']); // this handles the front page of a website too.

            elseif ( $this->nonEmptyStringField('seoValue',$_GET) )
                return $this->getContent__view ($_GET['seoValue']); // this handles the front page of a website too.

            else return $this->getContent__standardErrorMessage(
                $fncn.' : FAILED (this was not called with the right parameters). parameters are '.json_encode($_GET)
            );
        }
    }

    public function getContent__standardErrorMessage ($msg) {
        $ret = [];
        $file = $this->basePath.'/NicerAppWebOS/domainConfigs/'.$this->domain.'/errorMessage.default.php';
        if (!file_exists($file) || !is_readable($file)) {
            $html = $msg;
            $ret['siteContent'] = $html;
        } else {
            $fc = file_get_contents ($file);
            $html = str_replace('{$msg}', $msg, $fc);
            $ret['siteContent'] = $html;
        }
        return $ret;
    }

    public function getContent__view_wikipedia ($wiki_params=null) {
        $fncn = $this->cn.'::getContent__view_wikipedia()';
        global $naWebOS;
        // output frontpage.dialog.*.php
        $folder = $this->basePath.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.wikipedia.org/';
        $files = getFilePathList($folder, false, '/app.dialog.*\.php/', null, array('file'), 1, 1, true)['files'];
        //{ echo $folder.'<br/>'.PHP_EOL; echo json_encode($files); echo PHP_EOL.PHP_EOL; };
        $ret = [];
        foreach ($files as $idx2 => $filepath) {
            $fileRoot = $folder;
            $filename = str_replace ($fileRoot, '', $filepath);
            $dialogID = str_replace ('app.dialog.', '', $filename);
            $dialogID = str_replace ('.php', '', $dialogID);
            $arr = array ( $dialogID => execPHP($filepath) );
            //var_dump (file_exists($filepath)); echo PHP_EOL;
            //var_dump ($dialogID); echo PHP_EOL;
            //$arr = array ( $dialogID => $filepath );
            $ret = array_merge ($ret, $arr);
        }

        if (
            strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
            || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
        ) $ret = array_merge ($ret, [
            'head' => $this->getPageCSS(true,false)
        ]);
//echo '<pre>'; var_dump ($ret); exit();
        return $ret;
    }

    public function getContent__view ($viewID=null) {
        $fncn = $this->cn.'::getContent__view()';
        global $naWebOS;

        $ret = [];
        if (!is_string($viewID) || $viewID==='') {
            $msg = $fncn.' : FAILED (invalid or empty viewID parameter).';
            trigger_error ($msg, E_USER_ERROR);
            return $this->getContent_standardErrorMessage ($msg);
        } else {
            if ($viewID==='/') {
                // output frontpage.dialog.*.php
                $folder = $this->basePath.'/NicerAppWebOS/domainConfigs/'.$naWebOS->domain.'/';
                $files = getFilePathList($folder, false, '/frontpage.dialog.*\.php/', null, array('file'), 1, 1, true)['files'];
                //if ($debug) { echo $folder.'<br/>'.PHP_EOL; echo json_encode($files); echo PHP_EOL.PHP_EOL; };

                foreach ($files as $idx2 => $filepath) {
                    $fileRoot = $folder;
                    $filename = str_replace ($fileRoot, '', $filepath);
                    $dialogID = str_replace ('frontpage.dialog.', '', $filename);
                    $dialogID = str_replace ('.php', '', $dialogID);
                    $arr = array ( $dialogID => require_return($filepath) );
                    //var_dump ($filepath); echo PHP_EOL;
                    //var_dump ($dialogID); echo PHP_EOL;
                    //$arr = array ( $dialogID => $filepath );
                    $ret = array_merge ($ret, $arr);
                }

            } else {
                // request view settings from database
                $view = is_object($this->view)?(array)$this->view:$this->view;
                //echo '<pre>'; var_dump($view);exit();
                $ret = [ ];// DONT! 'siteContent' => '<pre>'.json_encode($view,JSON_PRETTY_PRINT).'</pre>'];
                if (is_array($view)) {
                    if (array_key_exists('misc', $view)) {
                        $fsid = $view['misc']['folder'];
                        foreach ($view as $k => $rec) {
                            if ($k=='misc') continue; else {
                                $fid = $k;
                                $rp = $fsid.'/'.$fid;
                            }
                        }
                        $view = [ $rp => $rec ];
                    }
                    //echo '<pre style="color:purple;background:cyan;">'; var_dump ($view); echo '</pre>'; exit();

                    foreach ($view['apps'] as $appName => $viewApp) break;
                    $viewsFolder = $viewApp['appFolder'];

                    //foreach ($view as $viewsFolder => $viewSettings) {
                        if (!file_exists($this->basePath.'/'.$viewsFolder)) {
                            $msg = 'Folder "'.$this->basePath.'/'.$viewsFolder.'" does not exist.'.PHP_EOL;
                                //.'$_SERVER='.json_encode($_SERVER,JSON_PRETTY_PRINT)
                                //.' $VIEW='.json_encode($view,JSON_PRETTY_PRINT)
                                //.' BACKTRACE='.json_encode(debug_backtrace(), JSON_PRETTY_PRINT);
                            trigger_error ($msg, E_USER_WARNING);
                        } else {
                            $dbInitPHP = $this->basePath.'/'.$viewsFolder.'/db_init.php';
                            if (file_exists($dbInitPHP)) execPHP ($dbInitPHP);

                            $files = getFilePathList ($this->basePath.'/'.$viewsFolder, true, '/app.*/', null, array('file'), 1, 1, true);
                            if (!array_key_exists('files', $files)) {
                                $msg = 'HTTP error 404 : no files matching app.* in "'.$viewsFolder.'"';
                                trigger_error ($msg, E_USER_ERROR);
                                echo $msg;
                                exit();
                            };
                            $files = $files['files'];
                            //if ($debug) { var_dump ($rootPath.'/'.$viewsFolder); echo '<pre style="color:yellow;background:red;">'; var_dump ($files); echo '</pre>'.PHP_EOL.PHP_EOL;  }; exit();

                            $titleFile = $this->basePath.'/'.$viewsFolder.'/app.title.site.php';
                            foreach ($files as $idx3 => $contentFile) {
                                if (strpos($contentFile, 'app.dialog.')!==false) {
                                    $divID = str_replace('app.dialog.', '', basename($contentFile));
                                    $divID = str_replace('.php', '', $divID);
                                    $ret[$divID] = execPHP ($contentFile);
                                }
                            }
                            //$contentFile = $rootPath.'/'.$viewsFolder.'/app.dialog.siteContent.php';
                            //$ret = [ 'siteContent' => execPHP ($contentFile) ];
                        }
                    //}
                } elseif ($view==='["NOT FOUND"]') {
                    // serve up the front page!
                    $ret = ['siteContent' => 'HTTP error 404 : "'.$viewID.'" could not be found.' ];
                }



                // render view
                // ....
            }


            if (
                strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
            ) $ret = array_merge ($ret, [
                'head' => $this->getPageCSS(true,false)
            ]);


            return $ret;

        }
    }

    public function getContent__data_by_users ($username=null, $url1=null, $dataID=null) {
        $fncn = $this->cn.'::getContent__data_by_users()';
        global $rootPath_na;
        global $naWebOS;
        $db = $naWebOS->dbs->findConnection('couchdb');
        $cdb = $db->cdb;

        $relTableName = 'data_by_users';
        $dataSetName = $db->dataSetName($relTableName);
        $cdb->setDatabase ($dataSetName, false);

        // am i called with the correct parameters, or not?
        if (
            !is_string($username)
            || $username===''
            || !is_string($url1)
            || $url1===''
            || !is_string($dataID)
            || $dataID===''
        ) {
            $dbg = [
                'username' => $username,
                'url1' => $url1,
                'dataID' => $dataID
            ];
            $msg = $fncn.' : FAILED : '.json_encode($dbg);
            trigger_error ($msg, E_USER_ERROR);
            return $this->getContent_standardErrorMessage ($msg);
        } else {

            // fetch dataRecord
            $findCommand = [
                'selector' => [ 'user' => str_replace('-',' ',$username), 'url1' => $url1, 'seo_value' => $dataID ],
                'fields' => ['_id']
            ];
            //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
            try {
                $call = $cdb->find ($findCommand);
            } catch (Exception $e) {
                $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
                trigger_error ($msg, E_USER_ERROR);
                echo $msg;
                return false;
            }

            //echo '<pre style="color:blue;background:rgba(255,255,255,0.7);border-radius:20px;">'; var_dump ($dataSetName); var_dump ($findCommand); var_dump ($call);var_dump (count($call->body->docs));exit();


            if (count($call->body->docs)===0) {
                $msg = 'Content could not be found.';
                return $this->getContent__standardErrorMessage ($msg);
            }

            $call2 = $cdb->get ($call->body->docs[0]->_id);
            //echo '<pre style="color:blue">'; var_dump ($call2);exit();
            $dataRecord = (array)$call2->body;
            //echo '<pre style="color:green">'; var_dump ($dataRecord);exit();



            if (array_key_exists('viewID', $dataRecord)) {
                // request view settings from database
                $viewID = $dataRecord['viewID'];

                $db = $this->dbs->findConnection('couchdb');
                $cdb = $db->cdb;
                $dataSetName = $db->dataSetName('views'); // i know, couchdb calls a 'table' a 'database'. and that sux.

                $findCommand = [
                    'selector' => [ 'viewID' => $viewID ],
                    'use_index' => 'primaryIndex',
                    'fields' => '_id'
                ];
                try {
                    $call3 = $cdb->find ($findCommand);
                } catch (Exception $e) {
                    $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
                    trigger_error ($msg, E_USER_ERROR);
                    echo $msg;
                    return false;
                }

                $call = $cdb->get ($call->body[0]['_id']);
                $view = $call->body[0];

                // overlay view settings, data from {$myDomain_tld}___views with
                //  data from {$myDomain_tld}___data_by_user::appParameters
                if (is_array($view['view'])) {
                    foreach ($view['view'] as $viewKey => $viewRecord) {
                        foreach ($dataRecord['appParameters'] as $drAppIdx => $drAppRecord) {
                            foreach ($drAppRecord as $viewKey2 => $viewRecord2) {
                                if ($viewKey === $viewKey2) {
                                    $view['view'][$viewKey2] = array_merge (
                                        $view['view'][$viewKey2],
                                        $viewRecord2
                                    );
                                }

                            }
                        }
                    }
                }
            } else {
                global $toArray;
                $view = $toArray($dataRecord['viewSettings']);
            }
            echo '<pre style="color:green">'; var_dump ($view);exit();
            $this->view = $view;

            // render the view
            if (is_array($view)) {
                if (array_key_exists('misc', $view)) {
                    $fsid = $view['misc']['folder'];
                    foreach ($view as $k => $rec) {
                        if ($k=='misc') continue; else {
                            $fid = $k;
                            $rp = $fsid.'/'.$fid;
                        }
                    }
                    $view = [ $rp => $rec ];
                }
                //echo '<pre style="color:purple;background:cyan;">'; var_dump ($view); echo '</pre>'; exit();

                foreach ($view as $viewsFolder => $viewSettings) {
                    $rootPath = str_replace('/NicerAppWebOS','',$rootPath_na);
                    if (file_exists($rootPath.'/'.$viewsFolder)) {
                        $files = getFilePathList ($rootPath.'/'.$viewsFolder, false, '/app.*/', null, array('file'), 1, 1, true)['files'];
                        //if ($debug)
                        //{ var_dump ($rootPath.'/'.$viewsFolder); echo '<pre style="color:yellow;background:red;">'; var_dump ($files); echo '</pre>'.PHP_EOL.PHP_EOL;  };

                        $titleFile = $rootPath.'/'.$viewsFolder.'/app.title.site.php';
                        $ret = [
                            'title' => execPHP($titleFile)
                        ];
                        //echo '<pre style="color:blue">'; var_dump ($ret); exit();
                        foreach ($files as $idx3 => $contentFile) {
                            if (strpos($contentFile, 'app.dialog.')!==false) {
                                $divID = str_replace('app.dialog.', '', basename($contentFile));
                                $divID = str_replace('.php', '', $divID);
                                $ret[$divID] = execPHP ($contentFile);
                            }
                        }
                        //$contentFile = $rootPath.'/'.$viewsFolder.'/app.dialog.siteContent.php';
                        //$ret = [ 'siteContent' => execPHP ($contentFile) ];
                    }
                }
            }


            if (
                strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
            ) $ret = array_merge ($ret, [
                'head' => $this->getPageCSS(true,false)
            ]);
            //echo '<pre style="color:blue">'; var_dump ($ret); exit();
            return $ret;
            //echo json_encode($ret);

        }
    }

    public function getView ($viewID, $getKeyName='seoValue') {
        $fncn = 'class NicerAppWebOS->getView()';
        $view = false;

        $db = $this->dbs->findConnection('couchdb');
        $cdb = $db->cdb;
        $dataSetName = $db->dataSetName('viewsIDs'); // i know, couchdb calls a 'table' a 'database'. and that sux.

        $cdb->setDatabase ($dataSetName, false);
        $findCommand = [
            'selector' => [
                'seoValue' => substr($_GET[$getKeyName],0,1)=='/'
                    ? substr($_GET[$getKeyName],1)
                    : $_GET[$getKeyName]
            ],
            'use_index' => 'primaryIndex',
            'fields' => ['_id', 'viewID' ]
        ];
        $view = [
            'findCommand' => $findCommand
        ];
        try {
            $call = $cdb->find ($findCommand);
        } catch (Exception $e) {
            $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : $e->getMessage()='.$e->getMessage().', $findCommand='.$findCommand;
            $this->view = $msg;
            trigger_error ($msg, E_USER_NOTICE);
            return false;
        }
        $view = [
            'findCommand' => $findCommand,
            'call' => $call
        ];//'<pre style="color:blue">$findCommand='.json_encode($findCommand, JSON_PRETTY_PRINT).'</pre><pre style="color:green">$call='.json_encode($call, JSON_PRETTY_PRINT).'</pre>';
        //return true;

        if (
            is_object($call)
            && is_object($call->body)
            && is_array($call->body->docs)
        ) {
            if (count($call->body->docs)===1) {
                $cdb->setDatabase ($db->dataSetName('views'));
                try {
                    //echo $getKeyName; echo '<pre>'; var_dump ($call->body); echo '</pre>'; echo $call->body->docs[0]->viewID;
                    $call2 = $cdb->get ($call->body->docs[0]->viewID);
                } catch (Exception $e) {
                    $msg = $fncn.' FAILED while trying to find view settings in \''.$db->dataSetName('views').'\' : $e->getMessage()='.$e->getMessage();
                    trigger_error ($msg, E_USER_NOTICE);
                    echo $msg;
                    $view = $msg;
                    return $view;
                }
                //echo '<pre>'; var_dump($call);exit();

                $view = json_decode(json_encode($call2->body->view), true);
            } else {
                $msg = $fncn.' : views count incorrect ("docs" count should be exactly 1) for seoValue='.$_GET['seoValue'].'.<br/>'."\n".'<pre>$call->headers->_HTTP->status='.$call->headers->_HTTP->status.', $call->body='.json_encode($call->body,JSON_PRETTY_PRINT).'</pre>';
                trigger_error($msg, E_USER_WARNING);
                error_log($msg);
                $view = $msg;
                return $view;
            }
        }

        return $view;
    }










    public function nonEmptyStringField ($fieldName, $arr) {
        if (
            array_key_exists($fieldName, $arr)
            && is_string ($arr[$fieldName])
            && $arr[$fieldName]!==''
        ) return true;
        return false;
    }




    public function getLinks ($files) {
        $lines = '';
        foreach ($files as $idx => $fileRec) {
            if (array_key_exists('indexFile', $fileRec)) {
                $indexFilepath = $fileRec['indexFile'];
                $filesRaw = file_get_contents($indexFilepath);
                $files2 = json_decode ($filesRaw);
                checkForJSONerrors ($filesRaw, $indexFilepath, '"null"');
            } else if (array_key_exists('files', $fileRec)) {
                $files2 = $fileRec['files'];
            }
            $indexType = $fileRec['type'];

            switch ($indexType) {
                case 'css': $lineSrc = "\t".'<link type="text/css" rel="StyleSheet" href="{$src}?c={$changed}">'."\r\n"; break;
                case 'javascript': $lineSrc = "\t".'<script type="text/javascript" src="{$src}?c={$changed}"></script>'."\r\n"; break;
            };

            //echo '<pre>'; var_dump ($files2); var_dump ($this->path); echo '</pre>'; exit();

            foreach ($files2 as $idx => $file) {
                //trigger_error ($file.' (1)', E_USER_NOTICE);
                $oFile = $file;
                $file = str_replace ('apps/{$domain}', 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('apps/'.$this->domain, 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('{$domain}', $this->domain, $file);
                //echo $file; echo '<br/>';
                //trigger_error ($file.' (2)', E_USER_NOTICE);
                if (file_exists($this->path.$file)) {
                    $url = str_replace ($this->path,'',$file);
                    $search = array ('{$src}', '{$changed}');
                    $replace = array ($url, date('Ymd_His', filemtime($this->path.'/'.$file)));
                    $lines .= str_replace ($search, $replace, $lineSrc);
                } else {
                    trigger_error ('file "'.$this->path.'/'.$file.'" is missing (oFile='.$oFile.'), referenced from <span class="naCMS_getLinksFileRec">'.json_encode($fileRec).'</span>.', E_USER_NOTICE);
                }
            }
        }
        return $lines;
    }

    public function getConcatenatedLinks ($files) {
        $lines = '';
        $latestChange = 0;
        $indexType = '';
        foreach ($files as $idx => $fileRec) {
            if (array_key_exists('indexFile', $fileRec)) {
                $indexFilepath = $fileRec['indexFile'];
                $filesRaw = file_get_contents($indexFilepath);
                $files = json_decode ($filesRaw);
                checkForJSONerrors ($filesRaw, $indexFilepath, '"null"');
            } else if (array_key_exists('files', $fileRec)) {
                $files = $fileRec['files'];
            }

            if (
                $indexType!==''
                && $indexType!==$fileRec['type']
            ) trigger_error ($this->cn.'::getConcatenatedLinks() : "type" must be identical for all $files.', E_USER_ERROR);
            $indexType = $fileRec['type'];

            foreach ($files as $idx => $file) {
                //trigger_error ($file.' (1)', E_USER_NOTICE);
                $oFile = $file;
                $file = str_replace ('apps/{$domain}', 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('apps/'.$this->domain, 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('{$domain}', $this->domain, $file);
                //trigger_error ($file.' (2)', E_USER_NOTICE);
                if (file_exists($this->path.'/'.$file)) {
                    $url = str_replace ($this->path,'',$file);
                    $fileLastChanged = filemtime($this->path.'/'.$file);
                    if ($fileLastChanged > $latestChange) $latestChange = $fileLastChanged;
                    //$search = array ('{$src}', '{$changed}');
                    //$replace = array ($url, date('Ymd_His', $fileLastChanged));
                    //$lines .= str_replace ($search, $replace, $lineSrc);
                } else {
                    trigger_error ('file "'.$this->path.'/'.$file.'" is missing (oFile='.$oFile.'), referenced from <span class="naCMS_getLinksFileRec">'.json_encode($fileRec).'</span>.', E_USER_ERROR);
                }
            }
        }

        switch ($indexType) {
            case 'css': $lineSrc = "\t".'<link defer type="text/css" rel="StyleSheet" href="{$src}">'."\r\n"; break;
            case 'javascript': $lineSrc = "\t".'<script defer type="text/javascript" src="{$src}"></script>'."\r\n"; break;
        };
        $url = '/NicerAppWebOS/ajax_getConcatenatedFiles.php?indexType='.$indexType.'&c='.date('Ymd-His',$latestChange);
        $lines .= str_replace ('{$src}', $url, $lineSrc);

        return $lines;
    }

    public function getConcatenatedLinksContent ($files) {
        $r = '';
        foreach ($files as $idx => $fileRec) {
            if (array_key_exists('indexFile', $fileRec)) {
                $indexFilepath = $fileRec['indexFile'];
                $filesRaw = file_get_contents($indexFilepath);
                $files = json_decode ($filesRaw);
                checkForJSONerrors ($filesRaw, $indexFilepath, '"null"');
            } else if (array_key_exists('files', $fileRec)) {
                $files = $fileRec['files'];
            }
            $indexType = $fileRec['type'];

            $cacheFile = $this->path.'/NicerAppWebOS/domainConfigs/'.$this->domain.'/cache.'.$indexType.'.'.$_GET['c'].'.txt';
            if (file_exists($cacheFile)) return file_get_contents($cacheFile);

            foreach ($files as $idx => $file) {
                //trigger_error ($file.' (1)', E_USER_NOTICE);
                $oFile = $file;
                $file = str_replace ('apps/{$domain}', 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('apps/'.$this->domain, 'apps/'.$this->viewsMID, $file);
                $file = str_replace ('{$domain}', $this->domain, $file);
                //trigger_error ($file.' (2)', E_USER_NOTICE);
                if (file_exists($this->path.'/'.$file)) {
                    $r .= file_get_contents($this->path.DIRECTORY_SEPARATOR.$file).PHP_EOL.PHP_EOL;
                } else {
                    trigger_error ('file "'.$this->path.'/'.$file.'" is missing (oFile='.$oFile.'), referenced from <span class="naCMS_getLinksFileRec">'.json_encode($fileRec).'</span>.', E_USER_ERROR);
                }
            }
        }


        $url = 'https://www.toptal.com/developers/javascript-minifier/api/raw';
        if ($indexType=='javascript') {
            // init the request, set various options, and send it
            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ["Content-Type: application/x-www-form-urlencoded"],
                CURLOPT_POSTFIELDS => http_build_query([ "input" => $r ])
            ]);

            $r = curl_exec($ch);

            // finally, close the request
            curl_close($ch);
        }
        file_put_contents ($cacheFile, $r);

        return $r;
    }

    public function getVividButtonCSSfiles () {
        $path = $this->path.'/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/button-4.2.0';
        if (!file_exists($path)) {
            $msg = $this->cn.'->getVividButtonCSSfiles() : $path='.$path.' does not exist.';
            trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            exit();
        }

        $files = getFilePathList ($path, false, '/btn_.*\.css/', null, array('file'), 1, 1, true)['files'];
        foreach ($files as $idx => $file) {
            $files[$idx] = str_replace($this->path, '', $file);
        }
        sort($files);
        return array_merge ([ '/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/button-4.2.0/themes.css' ], $files);
    }

    public function getVividButtonJavascriptFiles () {
        $path = $this->path.'/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/button-4.2.0';
        if (!file_exists($path)) {
            $msg = $this->cn.'->getVividButtonCSSfiles() : $path='.$path.' does not exist.';
            trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            exit();
        }

        $files = getFilePathList ($path, false, '/btn_.*\.source\.js/', null, array('file'), 1, 1, true)['files'];
        foreach ($files as $idx => $file) {
            $files[$idx] = str_replace($this->path, '', $file);
        }
        sort ($files);
        return $files;
    }



    public function html($relativeIndentLevel, $html) {
        $indent = '';
        for ($i=0; $i < $this->baseIndentLevel + $relativeIndentLevel; $i++) $indent .= "\t";
        return $indent.$html.PHP_EOL;
    }

    public function html_vividButton (
        $relativeIndentLevel, $containerStyle,

        $id,
        $class, $subClassSuffix, $iconComponents_subClassSuffix,
        $buttonStyle,
        $button_event_onclick,
        $button_event_onmouseover,
        $button_event_onmouseout,

        $buttonTabIndex, $buttonTitleAlt,

        $borderImgSrc,
        $tileImgSrc,
        $buttonBGimgSrc,
        $buttonImgSrc,

        $buttonOverlayHTML,

        $buttonText,
        $buttonText_class,
        $buttonText_style
    ) {
        $il = $relativeIndentLevel;
        if ($buttonTitleAlt!== $buttonText) $buttonTitleAlt2 = $buttonTitleAlt; else $buttonTitleAlt2 = '';
        if (is_string($buttonText) && $buttonText!=='') $containerStyle = 'display:flex;'.$containerStyle;
        $r  = $this->html($il, '<div id="'.$id.'_container" class="'.str_replace('vividButton_icon','vividButton_container',$class).'" tabindex="'.$buttonTabIndex.'" style="'.$containerStyle.'" onclick="'.$button_event_onclick.'" onmouseover="'.$button_event_onmouseover.'" onmouseout="'.$button_event_onmouseout.'" title="'.$buttonTitleAlt2.'" alt="'.$buttonTitleAlt.'">');
        $r .= $this->html($il+1, '<div id="'.$id.'" class="'.$class.' tooltip" title="'.$buttonTitleAlt2.'" tabindex="'.$buttonTabIndex.'" style="'.$buttonStyle.'">');
        $r .= $this->html($il+2,    '<div class="vividButton_icon_borderCSS'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'"></div>');
        if (!is_null($borderImgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgBorder'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/NicerAppWebOS/siteMedia/'.$borderImgSrc.'"/>');
        if (!is_null($tileImgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgTile'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/NicerAppWebOS/siteMedia/'.$tileImgSrc.'"/>');
        if (!is_null($buttonBGimgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgButtonIconBG'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/NicerAppWebOS/siteMedia/'.$buttonBGimgSrc.'"/>');
        if (!is_null($buttonImgSrc)) $r .= $this->html($il+2,    '<img class="vividButton_icon_imgButtonIcon'.$subClassSuffix.' '.$iconComponents_subClassSuffix.'" srcPreload="/NicerAppWebOS/siteMedia/'.$buttonImgSrc.'"/>');
        if (!is_null($buttonOverlayHTML)) $r .= $this->html($il+2,    $buttonOverlayHTML);
        $r .= $this->html($il, '</div>');
        if (is_string($buttonText) && $buttonText!=='') {
            $textPartSuffix = '_text';
            $r .= $this->html($il+1, '<div id="'.$id.$textPartSuffix.'" class="vividButton_icon'.$subClassSuffix.'_text '.$buttonText_class.'" style="'.$buttonText_style.'" tabindex="'.$buttonTabIndex.'" title="'.$buttonTitleAlt2.'" alt="'.$buttonTitleAlt.'">');
            $r .= $this->html($il+2,    '<div>'.$buttonText.'</div>');
            $r .= $this->html($il+1, '</div>');
        }
        $r .= $this->html($il, '</div>');
        return $r;
    }

    public function html_vividTabPage (
        $relativeIndentLevel, $containerStyle,

        $id,
        $container_class, $container_style, $container_title, $container_alt,
        $container_event_onclick, $container_event_onmouseover, $container_event_onmouseout,

        $tabPages_title, $tabPages_title_style,
        $header_class, $header_style, $header_title, $header_alt,
        $header_event_onclick, $header_event_onmouseover, $header_event_onmouseout,
        $header_buttons,

        $content_class, $content_style, $content_title, $content_alt,
        $content_event_onclick, $content_event_onmouseover, $content_event_onmouseout,
        $tabPages_content
    ) {
        $il = $relativeIndentLevel;
        $r = $this->html ($il,
            '<div id="'.$id.'" class="vividTabPage '.$container_class.'" style="display:flex;'.$container_style.'" '
            .'onclick="'.$container_event_onclick.'" onmouseover="'.$container_event_onmouseover.'" onmouseout="'.$container_event_onmouseout.'" '
            .'title="'.$container_title.'" alt="'.$container_alt.'">'
        );
            $r .= $this->html ($il+1,
                '<div id="'.$id.'_header" class="vividTabPage_header '.$header_class.'" style="'.$header_style.'" '
                .'onclick="'.$header_event_onclick.'" onmouseover="'.$header_event_onmouseover.'" onmouseout="'.$header_event_onmouseout.'" '
                .'title="'.$header_title.'" alt="'.$header_alt.'">'
            );
                $r .= $this->html ($il+2, '<div style="order:-1;'.$tabPages_title_style.'">'.$tabPages_title.'</div>');
                $r .= $this->html ($il+2, $header_buttons);
            $r .= $this->html ($il+1, '</div>');
            $r .= $this->html ($il+1,
                '<div id="'.$id.'_content" class="vividTabPage_content vividScrollpane '.$content_class.'" style="'.$content_style.'" '
                .'onclick="'.$content_event_onclick.'" onmouseover="'.$content_event_onmouseover.'" onmouseout="'.$content_event_onmouseout.'" '
                .'title="'.$content_title,'" alt="'.$content_alt.'">'
            );
                $r .= $this->html ($il+2, $content);
            $r .= $this->html ($il+1, '</div>');
        $r .= $this->html ($il, '</div>');
        return $r;
    }

    public function getSiteMenu() {
        $contentFile = dirname(__FILE__).'/domainConfigs/'.$this->domain.'/mainmenu.php';
        //var_dump ($contentFile); exit();
        $content = require_return ($contentFile, false);
        //var_dump ($content); echo '-.0.-'; exit();
        return $content;
    }

    public function getPageCSS_checkPermissions($d) {
        global $naDebugAll;
        global $naLAN;
        $debug = false;
        $db = $this->dbs->findConnection('couchdb');
        $viewFolder = '[UNKNOWN VIEW]';

        $selectors2 = $d['selectors'];
        //$selectorNames = $d['selectorNames'];
        //$debug = true;
        foreach ($selectors2 as $idx => $selector) {
            if ($debug) { echo $idx.'<br/>'.PHP_EOL; };

            $permissions = $selector['permissions'];
            //echo '<pre style="color:cyan;background:navy;">'; var_dump ($permissions); echo '</pre>';

            foreach (['read','write'] as $idx3=>$pt) {
            $hasPermission = false;
            foreach ($permissions as $permissionType => $permissionsRec) {
                if ($permissionType==$pt) {
                    foreach ($permissionsRec as $accountType => $accountsList) {
                        //echo '<pre style="color:lime;background:navy;">'; var_dump ($permissionsRec); echo '</pre>';
                        foreach ($accountsList as $idx2 => $userOrGroupID) {
                            if ($accountType == 'users') {
                                $adjustedUserOrGroupID = $db->translate_plainUserName_to_couchdbUserName($userOrGroupID);
                            } else {
                                $adjustedUserOrGroupID = $db->translate_plainGroupName_to_couchdbGroupName($userOrGroupID);
                            }
                            //$adjustedUserOrGroupID = $userOrGroupID; // TODO : check if this is necessary

                            //if ($debug)
                            //{ echo 't666='; var_dump($accountType); var_dump ($this->dbs->username); echo PHP_EOL; var_dump ($userOrGroupID); echo PHP_EOL; var_dump ($adjustedUserOrGroupID); echo '<br/>';}
                            if ($accountType == 'roles') {
                                //if ($debug) { echo '$this->dbs->roles='; var_dump($this->dbs->roles); };
                                if (is_string($this->dbs)) {
                                    echo $fncn.' : WARNING : invalid database connection ($this->dbs='.json_encode($this->dbs).').';
                                    exit(); // or exit();
                                }
                                foreach ( $this->dbs->findConnection('couchdb')->roles
                                    as $roleIdx => $groupID
                                ) {
                                    if ($debug) { echo 't667='; var_dump($groupID); };
                                    if ($adjustedUserOrGroupID==$groupID) {
                                        $hasPermission = true;
                                    }
                                }
                            }
                            if (
                                $accountType == 'users'
                                && $this->dbs->findConnection('couchdb')->username
                                    == $adjustedUserOrGroupID
                            ) {
                                $hasPermission = true;
                                if ($debug) { echo 't777 $username='.$this->dbs->findConnection('couchdb')->username.'<br/>'.PHP_EOL; }
                            }
                        }
                    }

                    if ($hasPermission)
                        $selectors2[$idx]['has_'.$pt.'_permission'] = true;
                    else
                        $selectors2[$idx]['has_'.$pt.'_permission'] = false;
                }
            }

            }
            //echo '<pre style="background:purple;color:white;font-weight:bold;">'.$idx.'</pre>'.PHP_EOL;
        }

        if ($debug) {echo '<pre style="color:lime;background:blue;border-radius:10px;">'; var_dump ($selectors2); echo '</pre>'; }

        return [
            'selectors' => $selectors2//,
            //'selectorNames' => $selectorNames,
            //'preferredSelectorName' => $d['preferredSelectorName']
        ];
    }

    public function getPageCSS($js=true, $doSetSpecificity=true, $doIncludeClientOnlyThemes=true, $stickToCurrentSpecificity=false, $specificityName=null) {
        global $naDebugAll;
        global $naLAN;
        $debug = false;

        $viewFolder = '[UNKNOWN VIEW]';
        $db = $this->dbs->findConnection('couchdb');

        $d1 = $this->getPageCSS_permissionsList($js);
        $d2 = $this->getPageCSS_checkPermissions($d1);
        $selectors = $d2['selectors'];

        $selectorsCachedFN = 'getPageCSS_'.randomString(50);
        $id = base64_encode_url(json_encode($selectors));
        $id2 = base64_encode_url(gzencode(json_encode($selectors)));
        $selectorsCachedFilepath = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS/siteCache/'.$selectorsCachedFN;

        $c = $this->getPageCSS_cached($id2);
        if (is_string($c)) return $c;

        //echo '<pre style="color:lime;background:green">'; var_dump ($selectors); echo '</pre>'; exit();
        //$selectorNames = &$d['selectorNames'];
        //$specificityName = 'current page for user '.$db->username.' at the client';

        $_SESSION['selectors'] = json_encode($selectors);
        //$_SESSION['selectorNames'] = json_encode($selectorNames);
        //echo '<pre>'; var_dump ($_SESSION);

        $selectors2 = array_reverse($selectors, true);
        //echo '<pre>'; var_dump ($selectors2); echo '</pre>'; exit();
        //$selectorNames2 = array_reverse($selectorNames, true);

        $ret = '';
        $hasJS = false;
        $hasCSS = false;
        //if ($debug)
        //echo '<pre>';var_dump ($selectors); exit();



        foreach ($selectors2 as $idx => $selector) {
            //echo '<pre>'; var_dump ($selector); exit();
            if (
                !array_key_exists('has_read_permission',$selector)
                || !$selector['has_read_permission']
            ) continue;

            if (
                !$doIncludeClientOnlyThemes
                && strpos($selector['specificityName'], ' client')!==false
            ) continue;

            if (
                $stickToCurrentSpecificity
                && $selector['specificityName']!==$specificityName
            ) continue;

            $css = $this->getPageCSS_specific($selector);
            //echo '<pre>'; var_dump($css); echo '</pre>';
            if (is_array($css)) {
                foreach ($css['themes'] as $themeName => $theme) { break; };
                $specificityName = (
                    array_key_exists($themeName, $css['themes'])
                    && array_key_exists('specificityName', $css['themes'][$themeName])
                    ? $css['themes'][$themeName]['specificityName']
                    : $selector['specificityName']
                );
            } else {
                $specificityName = $selector['specificityName'];
            }
            //{ echo '<pre>667 : $idx='.$idx; echo '$selector='; var_dump($selector); var_dump($css); echo '</pre>'; };
            if ($debug) {
                echo '<h1>'.$specificityName.'</h1>'; echo PHP_EOL;
                if (
                    is_array($css)
                    && array_key_exists ($themeName, $css['themes'])
                    //&& array_key_exists ('specificityName', $css['themes'][$themeName])
                ) var_dump ($css['themes'][$themeName]);
                echo '<pre style="color:yellow;background:navy;">'; var_dump ($selector); echo '</pre>';
            }

            //if (is_array($css)) $css = json_encode($css, JSON_PRETTY_PRINT);
            if ($debug && is_array($css)) { echo '<pre>$selector='; var_dump($selector); var_dump($css); echo '</pre>'; };
            if (false && is_string($css) && $debug) {
                echo '$idx = '; var_dump ($idx); echo PHP_EOL.PHP_EOL;
                echo '$css = '; var_dump ($css); echo PHP_EOL.PHP_EOL;
                echo '$hasJS = '; var_dump ($hasJS); echo PHP_EOL.PHP_EOL;
                echo '$hasCSS = '; var_dump ($hasCSS); echo PHP_EOL.PHP_EOL;
                echo '$selectorL1 = '; var_dump ($selector); echo PHP_EOL.PHP_EOL;
                exit();
            };
            //$_SESSION['selectorName'] = $selectorNames[$idx];
            //$_SESSION['preferredSelectorName'] = &$d['preferredSelectorName'];
            //if ($debug) { echo '$selector=';var_dump($selector); echo '<br/>$_SESSION='; var_dump ($_SESSION); echo PHP_EOL.PHP_EOL; }
            if (
                !$hasJS
                && $js === true
                && is_array($css)
                && array_key_exists('display',$selector)
                && $selector['display']!==false
            ) {
                $hasJS = true;
                $r = '<script id="jsPageSpecific" type="text/javascript">'.PHP_EOL;
                $r .= '// debug1'.PHP_EOL;
                $useVividTexts = !array_key_exists('uvt',$_GET) || $_GET['uvt']=='y' ? 'true' : 'false';
                $useLoadContent = !array_key_exists('lc',$_GET) || $_GET['lc']=='y' ? 'true' : 'false';

                //echo '<pre style="color:green">'; var_dump ($css); echo '</pre>'; exit();

                $_SESSION['themeName'] = $themeName;
                $_SESSION['specificityName'] = $selector['specificityName'];
                if ($debug) { echo '<pre style="color:green">'; var_dump ($css); echo '</pre>'; exit(); }
                $r .= 'var naGlobals = {'.PHP_EOL;
                    //$r .= "\tdebug : ".json_encode($dbg).",".PHP_EOL;
                    $r .= "\tuseVividTexts : ".$useVividTexts.",".PHP_EOL;
                    $r .= "\tuseLoadContent : ".$useLoadContent.",".PHP_EOL;
                    $r .= "\tbackground : '".$theme['background']."',".PHP_EOL;
                    $r .= "\tbackgroundSearchKey : '".$theme['backgroundSearchKey']."',".PHP_EOL;
                    $r .= "\tthemes : ".json_encode($css['themes'], JSON_PRETTY_PRINT).",".PHP_EOL;
                    $r .= "\tthemeName : '".$themeName."',".PHP_EOL;
                    //$r .= "\tspecificityName : \"".$specificityName."\",".PHP_EOL;
                    //$r .= "\tspecificityName_revert : \"".$specificityName."\",".PHP_EOL;
                    //echo '<pre style="background:navy;color:lime;border-radius:10px;">'; var_dump ($css); echo '</pre>';
                    $r .= "\tspecificityName : \"".$specificityName."\",".PHP_EOL;
                    //$r .= "\tspecificityNames : ".json_encode($selectorNames).",".PHP_EOL;
                    $r .= "\tthemesDBkeys : ".json_encode($selectors2, JSON_PRETTY_PRINT).",".PHP_EOL;
                    $r .= "\tnaLAN : ".($naLAN ? 'true' : 'false').','.PHP_EOL;
                    $r .= "\tnaHasErrors : ".((array_key_exists('naErrors',$_SESSION) && is_string ($_SESSION['naErrors']) && $_SESSION['naErrors']!=='') ? 'true' : 'false').','.PHP_EOL;
                    $r .= "\thasDB : ".($this->hasDB ? 'true' : 'false').PHP_EOL;
                $r .= '};'.PHP_EOL;
                  //  $r .= 'debugger;'.PHP_EOL;
                if (
                    strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                    || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
                ) {
                    $r .= 'naGlobals = $.extend(naGlobals, {'.PHP_EOL;
                        $r .= "\tapp : ".json_encode($this->view).','.PHP_EOL;
                        if (array_key_exists('apps',$_GET)) $r .= "\tapps : ".json_encode($_GET['apps']).PHP_EOL;
                    $r .= '});'.PHP_EOL;
                    //$r .= 'if (!na.site.settings.current.url) na.site.settings.current = $.extend(na.site.settings.current, {'.PHP_EOL;
                      //  $r .= "\turl : ".json_encode($this->url).PHP_EOL;
                    //$r .= '});'.PHP_EOL;
                };


                if ($doSetSpecificity) {
                    $r .= '$(document).ready(function() {'.PHP_EOL;
                    //$r .= "\tna.m.waitForCondition('HTML BODY : document.ready -> na.site.setSpecificity', na.m.HTMLidle, na.site.setSpecificity, 50);".PHP_EOL;
                    //$r .= "\tna.setSpecificity();".PHP_EOL;
                    $r .= "});".PHP_EOL;
                }
                $r .= '</script>'.PHP_EOL;
                $ret = $r.$ret;
            };

            if (is_array($css) && !$hasCSS) {
                //echo '<pre>'; var_dump ($css); exit();
                $hasCSS = true;
                foreach ($css['themes'] as $themeName => $theme) { break; };
                //echo '<pre style="color:blue;">'; var_dump ($selector); exit();
                //$r = '<script type="text/javascript">'.PHP_EOL;
                //$r .= "\tna.site.globals = $.extend(na.site.globals, {".PHP_EOL;
                //$r .= "\t\tthemeName : '".$css['theme']."'".PHP_EOL;
                //$r .= "\t});".PHP_EOL;
                //$r .= '</script>'.PHP_EOL;
                $r = '<style id="cssPageSpecific" type="text/css" theme="'.$theme['theme'].'" sel=\''.(json_encode($css['sel'])).'\' csn="'.$selector['specificityName'].'" dbID="'.$theme['dbID'].'">'.PHP_EOL;
                //echo '<pre style="color:green">'; var_dump ($theme); echo '</pre>'; exit();
                $r .= css_array_to_css2($theme['themeSettings']).PHP_EOL;


                //echo css_array_to_css2($theme['themeSettings']); exit();

                $this->theme = $theme;
                //$r .= 'h1::before, h2::before, h3::before {'."\r\n".PHP_EOL;
                    //$r .= "\t".'content : \'\''."\r\n".PHP_EOL;
                //$r .= '}'."\r\n".PHP_EOL;
                $r .= '#divFor_neCompanyLogo, #headerSiteDiv:not(.backdropped, .vividTextCSS), li span:not(.backdropped, .vividTextCSS), p:not(.backdropped, .vividTextCSS), h1:not(.backdropped, .vividTextCSS)::before, h2:not(.backdropped, .vividTextCSS)::before, h3:not(.backdropped, .vividTextCSS)::before {'."\r\n".PHP_EOL;
                    $r .= "\t".'background : rgba(0,0,0,'.$theme['textBackgroundOpacity'].');'."\r\n".PHP_EOL;
                    $r .= "\t".'border-radius : 10px !important;'."\r\n".PHP_EOL;
                $r .= '}'."\r\n".PHP_EOL;
                /*
                $r .= '.mce-content-body h1, .mce-content-body h2, .mce-content-body h3, .mce-content-body p {';
                $r .= "\t".'background : inherit !important;';
                $r .= '}'."\r\n".PHP_EOL;
                */

                $fn = dirname(__FILE__).'/../themes/nicerapp_default_animations__'.$themeName.'.css';
                if (file_exists($fn) && is_readable($fn)) {
                    $theme['animations'] = css_keyframes_to_array( file_get_contents($fn) );
                    //echo '<pre class="css_keyframes_to_array">'; var_dump ($css['animations']); echo '</pre>';//exit();

                    $a1 = css_animation_template_to_animation (
                        $themeName, $theme['animations'],
                        [
                            'naHS_l0_in', 'naHS_l1_in', 'naHS_l2_in', 'naHS_json_in',
                            'naHS_releaseDate_l0_in', 'naHS_releaseDate_l1_in', 'naHS_releaseDate_l2_in'
                        ],
                        [ '0%' => [
                            'background' => [
                                [
                                    'search' => '/rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*([\d\.])+\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ],
                                [
                                    'search' => '/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ]
                            ]
                        ]]
                    );
                    //echo '<pre style="background:darkred; color:black;">'; var_dump ($a1); echo '</pre>'; exit();
                    $a2 = css_animation_template_to_animation (
                        $themeName, $theme['animations'],
                        [
                            'naHS_l0_out', 'naHS_l1_out', 'naHS_l2_out', 'naHS_json_out',
                            'naHS_releaseDate_l0_out', 'naHS_releaseDate_l1_out', 'naHS_releaseDate_l2_out'
                        ],
                        [ '100%' => [
                            'background' => [
                                [
                                    'search' => '/rgba\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3}),\s*([\d\.])+\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ],
                                [
                                    'search' => '/rgb\((\d{1,3}),\s*(\d{1,3}),\s*(\d{1,3})\)/',
                                    'replace' => 'rgba($1, $2, $3, '.$theme['textBackgroundOpacity'].')'
                                ]
                            ]
                        ]
                    ]);

                    $r .= css_animation_array_to_css (array_merge($a1,$a2));
                    //echo '<pre style="background:darkred; color:white;">'; var_dump (htmlentities($r)); echo '</pre>'; exit();

                    $r .= css_animation_keys_to_css (
                        $themeName,
                        [
                            'li.todoList > div.active' => 'naHS_l0_in',
                                // the above key-value pair with a $css['theme']=='default', results in the following CSS rule :
                                /*
                                * li.todoList > div.active {
                                *      animation : naHS_l0_in__default 1s forwards;
                                * }
                                */

                            'li.todoList > div' => 'naHS_l0_out',

                            '.contentSectionTitle3.active' => 'naHS_l0_in',
                            '.contentSectionTitle3.in-active' => 'naHS_l0_out',
                            'p.todoList.active' => 'naHS_l0_in',
                            'p.todoList.in-active' => 'naHS_l0_out',
                            'h1.todoList.active' => 'naHS_l0_in',
                            'h1.todoList.in-active' => 'naHS_l0_out',
                            'h2.todoList.active' => 'naHS_l0_in',
                            'h2.todoList.in-active' => 'naHS_l0_out',
                            'h3.todoList.active' => 'naHS_l0_in',
                            'h3.todoList.in-active' => 'naHS_l0_out',
                            '.todoList_l1 > li > div.active' => 'naHS_l1_in',
                            '.todoList_l1 > li > div' => 'naHS_l1_out',
                            '.todoList_l2 > li > div.active' => 'naHS_l2_in',
                            '.todoList_l2 > li > div' => 'naHS_l2_out',

                            '.todoList_l1 > li > pre.json.active' => 'naHS_l1_in',
                            '.todoList_l1 > li > pre.json' => 'naHS_l1_out',

                            '.todoList_l2 > li > pre.json.active' => 'naHS_l2_in',
                            '.todoList_l2 > li > pre.json' => 'naHS_l2_out',

                            'li.todoList.releaseDate > div.active' => 'naHS_releaseDate_l0_in',
                            'li.todoList.releaseDate > div' => 'naHS_releaseDate_l0_out',
                            '.todoList_l1.releaseDate > li > div.active' => 'naHS_releaseDate_l1_in',
                            '.todoList_l1.releaseDate > li > div' => 'naHS_releaseDate_l1_out',
                            '.todoList_l2.releaseDate > li > div.active' => 'naHS_releaseDate_l2_in',
                            '.todoList_l2.releaseDate > li > div' => 'naHS_releaseDate_l2_out'

                        ]
                    );
                }

                $r .= '</style>'.PHP_EOL;
                $ret .= $r;
            }
            if (is_array($css)) break;
        };

        //echo '<pre>'; var_dump ($d); exit();
        //exit();
        if (is_array($css) && !$hasJS && $js===true) {
                $hasJS = true;
                foreach ($selectors2 as $idx => $selector) {
                    if (
                        array_key_exists('specificityName', $selector)
                        && array_key_exists ('specificityName', $css)
                        && $selector['specificityName']!==$css['specificityName']
                    ) continue;
                    $r = '<script id="jsPageSpecific" type="text/javascript">'.PHP_EOL;
                    $r .= '// debug4'.PHP_EOL;
                    $useVividTexts = !array_key_exists('uvt',$_GET) || $_GET['uvt']=='y' ? 'true' : 'false';
                    $dbg = [
                        'na.site.loadContent' => !array_key_exists('lc',$_GET) || $_GET['lc']=='y' ? 'true' : 'false'
                    ];
                    foreach ($css['themes'] as $themeName => $theme) { break; };
                    $r .= 'na.site.globals = $.extend(na.site.globals, {'.PHP_EOL;
                        $r .= "\tdebug : ".json_encode($dbg).",".PHP_EOL;
                        $r .= "\tuseVividTexts : ".$useVividTexts.",".PHP_EOL;
                        $r .= "\tbackground : '".$theme['background']."',".PHP_EOL;
                        $r .= "\tbackgroundSearchKey : '".$theme['backgroundSearchKey']."',".PHP_EOL;
                        $r .= "\tthemes : ".json_encode($css['themes'], JSON_PRETTY_PRINT).",".PHP_EOL;
                        $r .= "\tthemeName : '".$themeName."',".PHP_EOL;
                        $r .= "\tspecificityName : \"".$specificityName."\",".PHP_EOL;
                        //$r .= "\tspecificityName : \"".$specificityName."\",".PHP_EOL;
                        //$r .= "\tspecificityName_revert : \"".$specificityName."\",".PHP_EOL;
                        //$r .= "\tspecificityNames : ".json_encode($selectorNames).",".PHP_EOL;
                        $r .= "\tthemesDBkeys : ".json_encode($selectors2, JSON_PRETTY_PRINT).",".PHP_EOL;
                        $r .= "\tnaLAN : ".($naLAN ? 'true' : 'false').','.PHP_EOL;
                        $r .= "\tnaHasErrors : ".((array_key_exists('naErrors',$_SESSION) && is_string ($_SESSION['naErrors']) && $_SESSION['naErrors']!=='') ? 'true' : 'false').','.PHP_EOL;
                        $r .= "\thasDB : ".($this->hasDB ? 'true' : 'false').PHP_EOL;
                    $r .= '});'.PHP_EOL;
                    //var_dump (strpos($_SERVER['SCRIPT_NAME'], '/index.php')); var_dump (strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')); var_dump ($_SERVER);
                    //var_dump ($_GET); exit();
                    if (
                        strpos($_SERVER['SCRIPT_NAME'], '/index.php')!==false
                        || strpos($_SERVER['SCRIPT_NAME'], '/ajax_get_content.php')!==false
                    ) {
                        $r .= 'na.site.globals = $.extend(na.site.globals, {'.PHP_EOL;
                            $r .= "\tapp : ".json_encode($this->view).','.PHP_EOL;
                        $r .= '});'.PHP_EOL;
                        $r .= 'if (!na.site.settings.current.url) na.site.settings.current = $.extend(na.site.settings.current, {'.PHP_EOL;
                            $r .= "\turl : ".json_encode($this->url).PHP_EOL;
                        $r .= '});'.PHP_EOL;
                    };
                    $r .= '$(document).ready(function() {'.PHP_EOL;
                        $r .= "\t//setTimeout(function() {".PHP_EOL;
                        $r .= "\t\tna.site.setSpecificity();".PHP_EOL;
                        $r .= "\t//}, 10);".PHP_EOL;
                    $r .= "});".PHP_EOL;
                    $r .= '</script>'.PHP_EOL;
                    $ret = $r.$ret;
                }
        };

        if ($debug) { echo '$ret='; var_dump(htmlentities($ret)); echo '</pre>'.PHP_EOL.PHP_EOL; exit(); };

        file_put_contents($selectorsCachedFilepath.'.idx', $id2);
        file_put_contents($selectorsCachedFilepath.'.txt', $ret);

        if ($this->debugThemeLoading) exit();

        return $ret;
    }

    public function getPageCSS_cached ($id2) {
        $path = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS/siteCache';
        $files = getFilePathList ($path, false, '/getPageCSS_.*\.idx/', null, array('file'), 1, 1, true)['files'];
        foreach ($files as $idx => $f) {
            $id2a = file_get_contents($f);
            $dbg = [
                'id2' => $id2,
                'id2a' => $id2a
            ];
            //var_dump($dbg);
            if (strlen($id2a)===strlen($id2) && $id2a==$id2) return file_get_contents(str_replace('.idx','.txt',$f));
        }
        return false;
    }

    public function themeSettings_UL_list ($theme, $root=true) {
        $css = '';
        if ($root) $css .= '<ul class="vividMenu_mainUL" style="display:none;" itemsLevel1="1" menuStructure="vertical">'; else $css .= '<ul>';
        foreach ($theme as $key => $value) {
            if ($key=='css') continue;

            $css .= '<li><a href="#" class="nomod noPushState">'.$key.'</a>';
            if (is_array($value)) {
                $css .= $this->themeSettings_UL_list ($value, false);
            }
            $css .= '</li>';
        }
        $css .= '</ul>';
        return $css;
    }

    public function getPageCSS_permissionsList($js=true) {
        global $naDebugAll;
        global $naLAN;
        $debug = $this->debugThemeLoading;
        $db = $this->dbs->findConnection('couchdb');
        $cdb = $db;

        $viewFolder = '[UNKNOWN VIEW]';

        //echo '<pre>$this->view='; var_dump ($this->view); exit();
        if (is_array($this->view)) {
            foreach ($this->view as $viewFolder => $viewSettings) break;
            //$viewFolder = preg_replace('/.*\//','', $viewFolder);
            //var_dump ($viewFolder); exit();
            $url = '/view/'.base64_encode_url(json_encode($this->view));
        } /*else if (array_key_exists('REQUEST_URI',$_SERVER)) {
            // use defaults if not in proper format (when URL uses HTTP URL parameters for instance)..
            $viewName = '[front page]';
            $url = '/';

            // check if SEO url exists in proper format
            $uri = $_SERVER['REQUEST_URI'];
            if ($uri!=='' && strpos('?', $uri)===false) {
                $viewName = '[app page]';
                $url = $uri;
            }
        } */else {
            $viewFolder = '[front page]';
            $url = '/';

        }
        if (
            (
                !array_key_exists('url', $_SESSION)
                || $_SERVER['REQUEST_URI'] !== $_SESSION['url']
            ) && (
                strpos($_SERVER['REQUEST_URI'], 'pageSpecificSettings.php')===false
            )
        ) {
            if (is_array($this->view)) {
                foreach ($this->view as $viewFolder => $viewSettings) break;
                //$viewFolder = preg_replace('/.*\//','', $viewFolder);
                //var_dump ($viewFolder); exit();
                $url = '/view/'.base64_encode_url(json_encode($this->view));
            } /*else if (array_key_exists('REQUEST_URI',$_SERVER)) {
                // use defaults if not in proper format (when URL uses HTTP URL parameters for instance)..
                $viewName = '[front page]';
                $url = '/';

                // check if SEO url exists in proper format
                $uri = $_SERVER['REQUEST_URI'];
                if ($uri!=='' && strpos('?', $uri)===false) {
                    $viewName = '[app page]';
                    $url = $uri;
                }
            } */else {
                $viewFolder = '[front page]';
                $url = '/';

            }
            //$url = $_SERVER['REQUEST_URI'];
            $_SESSION['url'] = $url;
        } else {
            $url = $_SESSION['url'] = $url;
        }

        $appName = preg_replace('/.*\//','',$viewFolder);
        //if ($debug) { echo '<pre>'; var_dump ($url); echo PHP_EOL; var_dump ($this->view); echo '</pre>'.PHP_EOL; }
        /*if ($viewFolder!=='') {
            $appName = preg_replace('/.*\//','',$viewFolder);
            $selectors = array (
                0 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers')
                        ]
                    ],
                    'specificityName' => 'site (for all viewers)',
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true,
                    'worksWithoutDatabase' => true
                ),

                1 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers')
                            ]
                        ]
                    ],
                    'specificityName' => 'view \''.$viewFolder.'\' (for all viewers)',
                    'view' => $viewFolder,
                    //'url' => $url,
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true
                ),

                2 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers')
                            ]
                        ]
                    ],
                    'specificityName' => 'current page (for all viewers)',
                    'url' => $url,
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true
                )
            );

            $selectorNames = array (
                0 => 'site (for all viewers)',
                1 => 'app \''.$viewFolder.'\' (for all viewers)',
                2 => 'current page (for all viewers)'
            );
            //$preferredSelectorName = 'site (for all viewers)';

        } else {*/
            $selectors = array (
                0 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ],
                    'specificityName' => 'site (for all viewers)',
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true,
                    'worksWithoutDatabase' => true
                ),

                1 => array (
                    'permissions' => [
                        'read' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Guests')
                            ]
                        ],
                        'write' => [
                            'roles' => [
                                $db->translate_plainGroupName_to_couchdbGroupName('Owners'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Chief Officers'),
                                $db->translate_plainGroupName_to_couchdbGroupName('Moderators')
                            ]
                        ]
                    ],
                    'specificityName' => 'current page (for all viewers)',
                    'url' => $url,
                    'role' => $db->translate_plainGroupName_to_couchdbGroupName('Guests'),
                    'display' => true
                )
            );

            $selectorNames = array (
                0 => 'site (for all viewers)',
                1 => 'current page (for all viewers)'
            );
            //$preferredSelectorName = 'site (for all viewers)';so
        //}

        //echo '<pre>';var_dump ($_SESSION); var_dump ($_COOKIE); echo '</pre>';
        if (
            false
            || (
                !isset($_COOKIE)
                || !is_array($_COOKIE)
                || !array_key_exists('cdb_loginName',$_COOKIE)
                || $_COOKIE['cdb_loginName']===''
                //|| !$_SESSION['cdb_userIsAdministrator']
            )
        ) {
            $selectors[0]['display'] = false;
            $selectors[1]['display'] = false;
            if (array_key_exists(2,$selectors)) $selectors[2]['display'] = false;
        };
        //echo '<pre>'; var_dump ($selectors);

        global $naIP;
        $username100 = (
            array_key_exists('cdb_loginName', $_COOKIE)
            ? $_COOKIE['cdb_loginName']
            : ''
        );
        $username101 = $db->translate_couchdbUserName_to_plainUserName ($username100);



        if (
            (
                is_object($this->dbs)
                && is_string($this->dbs->findConnection('couchdb')->username)
                && $this->dbs->findConnection('couchdb')->username!==''
                //&& $this->dbs->findConnection('couchdb')->username!==$this->domainForDB.'___Guest'
                && $username100!==''
            )
        ) {
            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username100 ] ],
                    'write' => [ 'users' => [ $username100 ] ]
                ],
                'specificityName' => 'site for user '.$username101,
                'user' => $username100,
                'display' => true
            );
            $selectorNames[] = 'site for user '.$username101;
            //$preferredSelectorName = 'site user '.$username100;

            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username100 ] ],
                    'write' => [ 'users' => [ $username100 ] ]
                ],
                'specificityName' => 'site for user '.$username101.' at the client',
                'user' => $username100,
                'ip' => $naIP,
                'display' => true
            );
            $selectorNames[] = 'site for user '.$username101.' at the client';





            foreach ($this->dbs->findConnection('couchdb')->roles as $roleIdx => $role) {
                $role2 = $this->dbs->findConnection('couchdb')->translate_couchdbGroupName_to_plainGroupName($role);
                if ($debug) { echo '<pre style="color:white;background:navy">'; var_dump ($role2); echo '</pre>'; }
                $selectors[] = array (
                    'permissions' => array (
                        'read' => [
                            'users' => [ $username100 ],
                            'roles' => [ $db->translate_plainGroupName_to_couchdbGroupName('Guests'), $role ]
                        ],
                        'write' => [
                            'roles' => [ $role ]
                        ]
                    ),
                    'specificityName' => 'site for group '.$role2,
                    'role' => $role,
                    'display' => true
                );
                $selectorNames[] = 'site for group '.$role2;
                //$preferredSelectorName = 'site for group '.$role;

                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'users' => [ $username100 ] ],
                        'write' => [ 'roles' => [ $role ] ]
                    ],
                    'specificityName' => 'site for group '.$role2.' at the client',
                    'role' => $role,
                    'ip' => $naIP,
                    'display' => true
                );
                $selectorNames[] = 'site for group '.$role2.' at the client';
                //$preferredSelectorName = 'site for group '.$role.' at the client';
                if ($viewFolder!==''
                    && $viewFolder!=='/'
                    && $appName!==''
                ) {
                    $selectors[] = array (
                        'permissions' => [
                            'read' => [ 'roles' => [ $role ] ],
                            'write' => [ 'roles' => [ $role ] ]
                        ],
                        'specificityName' => 'app \''.$appName.'\' for group '.$role2,
                        'app' => $viewFolder,
                        'role' => $role,
                        'display' => true
                    );
                    $selectorNames[] = 'app \''.$appName.'\' for group '.$role2;
                    $selectors[] = array (
                        'permissions' => [
                            'read' => [ 'roles' => [ $role ] ],
                            'write' => [ 'roles' => [ $role ] ]
                        ],
                        'specificityName' => 'app \''.$appName.'\' for group '.$role2.' at the client',
                        'app' => $viewFolder,
                        'role' => $role,
                        'ip' => $naIP,
                        'display' => true
                    );
                    $selectorNames[] = 'app \''.$appName.'\' for group '.$role2.' at the client';
                }



                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'roles' => [ $role ] ],
                        'write' => [ 'roles' => [ $role ] ]
                    ],
                    'specificityName' => 'current page for group '.$role2,
                    'url' => $url,
                    //'app' => $viewFolder,
                    'role' => $role,
                    'display' => true
                );
                //$selectorNames[] = 'current page for group '.$role;

                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'roles' => [ $role ] ],
                        'write' => [ 'roles' => [ $role ] ]
                    ],
                    'specificityName' => 'current page for group '.$role2.' at the client',
                    'url' => $url,
                    //'app' => $viewFolder,
                    'role' => $role,
                    'ip' => $naIP,
                    'display' => true
                );
                //$preferredSelectorName = $selectorNames[] = 'current page for group '.$role.' at the client';
            }


            if ($viewFolder!=='' && $appName!=='') {
                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'users' => [ $username100 ] ],
                        'write' => [ 'users' => [ $username100 ] ]
                    ],
                    'specificityName' => 'app \''.$appName.'\' for user '.$username101,
                    'app' => $viewFolder,
                    'user' => $username100,
                    'display' => true
                );
                $selectorNames[] = 'app \''.$appName.'\' for user '.$username101;
                $selectors[] = array (
                    'permissions' => [
                        'read' => [ 'users' => [ $username100 ] ],
                        'write' => [ 'users' => [ $username100 ] ]
                    ],
                    'specificityName' => 'app \''.$appName.'\' for user '.$username101.' at the client',
                    'app' => $viewFolder,
                    'user' => $username100,
                    'ip' => $naIP,
                    'display' => true
                );
                $selectorNames[] = 'app \''.$appName.'\' for user '.$username101.' at the client';
            }




            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username100 ] ],
                    'write' => [ 'users' => [ $username100 ] ]
                ],
                'specificityName' => 'current page for user '.$username101,
                'url' => $url,
                'user' => $username100,
                'display' => true
            );
            $selectorNames[] = 'current page for user '.$username101;
            //$preferredSelectorName = 'current page for user '.$username100;

            $selectors[] = array (
                'permissions' => [
                    'read' => [ 'users' => [ $username100 ] ],
                    'write' => [ 'users' => [ $username100 ] ]
                ],
                'specificityName' => 'current page for user '.$username101.' at the client',
                'url' => $url,
                'user' => $username100,
                'ip' => $naIP,
                'display' => true
            );
            $selectorNames[] = 'current page for user '.$username101.' at the client';
            //$preferredSelectorName = 'current page for user '.$username100.' at the client';
        };

        //$preferredSelectorName = 'current page for user '.$username100.' at the client';
            //if (session_status() === PHP_SESSION_NONE) {
            //ini_set('session.gc_maxlifetime', 3600);
            //session_start();
        //};

        // TODO : filter $_SESSION['selectors'] here or in the code that exports it to js?
        $_SESSION['selectors'] = json_encode($selectors);
        //$_SESSION['selectorNames'] = json_encode($selectorNames);
        //echo '<pre>'; var_dump ($_SESSION);

        //$selectors2 = array_reverse($selectors, true);
        //$selectorNames2 = array_reverse($selectorNames, true);

        $ret = '';
        $hasJS = false;
        $hasCSS = false;

        //if ($debug)
        //echo '<pre>';var_dump ($selectors); exit();
        return [
            //'selectorNames' => $selectorNames,
            'selectors' => $selectors//,
            //'preferredSelectorName' => $preferredSelectorName
        ];
    }

    public function getPageCSS_specific($selector) {
        $debug = $this->debugThemeLoading;
        $cdbFunctional1a = true;
        $fncn = $this->cn.'::getPageCSS_specific()';
        //$fncn = $this->cn.'::getPageCSS_specific("'.json_encode($selector).'")';

        $db = $this->dbs->findConnection('couchdb');

        //if ($debug)
        //{ echo '$selector='; var_dump ($selector); echo '<br/><br/>'.PHP_EOL.PHP_EOL; exit(); };

        $permissions = $selector['permissions'];
        if (false && $debug) {
            echo '<pre style="color:purple">$selector='; var_dump ($selector); echo '</pre><br/>'.PHP_EOL.PHP_EOL;
        }

        unset ($selector['display']);
        //unset ($selector['has_read_permission']);
        //unset ($selector['has_write_permission']);
        unset ($selector['permissions']);

        /*
        if (array_key_exists('worksWithoutDatabase',$selector) && $selector['worksWithoutDatabase']===true) {
                $rec = [
                    'default' => [
                        '_id' => cdb_randomString(20),
                        'role' => 'guests',
                        'theme' => 'default',
                        'specificityName' => 'site',
                        'textBackgroundOpacity' => 0.4,
                        'background' => '/NicerAppWebOS/siteMedia/backgrounds/tiled/grey/abstract_ice.jpg',
                        'backgroundSearchKey' => 'landscape',
                        'dialogs' => css_to_array (file_get_contents(
                            realpath(dirname(__FILE__).'/../..')
                            .'/NicerAppWebOS/themes/nicerapp_default.css'
                        ))
                    ]
                ];
                return $rec;
        */
        //} else {
            if ($cdbFunctional1a) {
                // check permissions
                $hasPermission = false;
                foreach ($permissions as $permissionType => $permissionsRec) {
                    if ($permissionType=='read') {
                        foreach ($permissionsRec as $accountType => $accountsList) {
                            foreach ($accountsList as $idx => $userOrGroupID) {
                                if ($accountType == 'users') {
                                    $adjustedUserOrGroupID = $db->translate_plainUserName_to_couchdbUserName($userOrGroupID);
                                } else {
                                    $adjustedUserOrGroupID = $db->translate_plainGroupName_to_couchdbGroupName($userOrGroupID);
                                }
                                $adjustedUserOrGroupID = $userOrGroupID;


                                //if ($debug) { echo 't666='; var_dump($accountType); var_dump ($this->dbs->findConnection('couchdb')->username); echo '<br/>'.PHP_EOL; var_dump ($userOrGroupID); echo '<br/>$adjustedUserOrGroupID='; var_dump ($adjustedUserOrGroupID); }

                                if ($accountType == 'roles') {
                                    //$adjustedUserOrGroupID = $db->translate_plainGroupName_to_couchdbGroupName($userOrGroupID);
                                    //if ($debug) { echo '$this->dbs->roles='; var_dump($this->dbs->roles); };
                                    if (is_string($this->dbs)) {
                                        echo $fncn.' : WARNING : invalid database connection ($this->dbs="'.json_encode($this->dbs).'")- this database server, or even the entire webserver, has been hacked by hostiles.';
                                        exit(); // or exit();
                                    }
                                    foreach ( $this->dbs->findConnection('couchdb')->roles
                                        as $roleIdx => $groupID
                                    ) {
                                        if ($debug) { echo 't667A='; var_dump($groupID); var_dump ($adjustedUserOrGroupID);};
                                        if ($adjustedUserOrGroupID==$groupID) {
                                            $hasPermission = true;
                                        }
                                    }
                                }
                                if ($accountType == 'users' && $this->dbs->findConnection('couchdb')->username == $adjustedUserOrGroupID) {
                                    $hasPermission = true;
                                    if ($debug) { echo 't777 $username='.$this->dbs->findConnection('couchdb')->username.PHP_EOL; }
                                }
                            }
                        }
                    }
                }
            } else {
                return false;
                //$hasPermission = true;
                //if ($debug) { echo  '<h1 style="color:green;font-size:bold;">$hasPermission = true, because $cdbFunctional1a = false.</h1>'.PHP_EOL; }
            }

            if (!$hasPermission) {
                $msg = 'class.naContentManagementSystem.php::getPageCSS_specific() : !$hasPermission for username='.$this->dbs->findConnection('couchdb')->username.' - aborting';
                //if ($debug) trigger_error ($msg, E_USER_NOTICE);
                if ($debug) echo $msg.'<br/>'.PHP_EOL;

                if ($debug) echo '</pre>';
                return false;
            }

            if (false) {
                echo '<pre style="color:green">';
                var_dump ($this->dbs->findConnection('couchdb'));
                echo '</pre>';
            }

            // try to fetch the requested cosmetics data
            $dbName = $this->dbs->findConnection('couchdb')->dataSetName('themes');
            try {
                $this->dbs->findConnection('couchdb')->cdb->setDatabase($dbName, false);
            } catch (Exception $e) {
                if ($debug) { echo 'status : Failed : could not open database '.$dbName.'<br/>'.PHP_EOL; exit(); }
            }

            $sel = [];
            if (array_key_exists('user', $selector)) $sel['user'] = $selector['user']; else $sel['user'] = [ '$exists' => false ];
            if (array_key_exists('role', $selector)) $sel['role'] = $selector['role']; else $sel['role'] = [ '$exists' => false ];
            if (array_key_exists('view', $selector)) $sel['view'] = $selector['view']; else $sel['view'] = [ '$exists' => false ];
            if (array_key_exists('app', $selector)) $sel['app'] = $selector['app']; else $sel['app'] = [ '$exists' => false ];
            if (array_key_exists('url', $selector)) $sel['url'] = $selector['url']; else $sel['url'] = [ '$exists' => false ];
            if (array_key_exists('specificityName', $selector)) $sel['specificityName'] = $selector['specificityName'];
            if (array_key_exists('theme', $selector)) $sel['theme'] = $selector['theme']; //else $sel['theme'] = [ '$exists' => false ];
            if (array_key_exists('ip', $selector)) $sel['ip'] = $selector['ip']; else $sel['ip'] = [ '$exists' => false ];
            global $naIP;
            if (strpos($selector['specificityName'], 'on the client')!==false) {
                $selector['ip'] = $naIP;
                //$selector['ua'] = $_SERVER['HTTP_USER_AGENT'];
            }
            $selector['lastUsed'] = [
                '$exists' => true
            ];
            if ($debug) { echo '<pre style="color:blue">$sel = '.json_encode ($sel, JSON_PRETTY_PRINT); echo '</pre>'; };
            //array( 'url'=>$selector['url'], 'role'=>$selector['role'] ),//$selector,

            $findCommand = array (
                'selector' => $sel,
                'fields' => [ '_id', 'ip', 'user', 'view', 'role', 'lastUsed', 'theme', 'url', 'themeSettings', 'apps', 'background', 'backgroundSearchKey', 'textBackgroundOpacity', 'changeBackgroundsAutomatically', 'backgroundChange_hours', 'backgroundChange_minutes' ],
                'sort' => [['lastUsed'=>'desc']],
                'use_index' => '_design/5a5ca56d9824edad32284bf01bc7fb3838fa049c'
            );
            /*
            $findCommand = array (
                'selector' => $sel,
                'fields' => [ '_id', 'lastUsed' ],
                'sort' => [['lastUsed'=>'asc']],
                'use_index' => 'sortIndex_lastUsed'
               // 'use_index' => 'primaryIndex'
            );
            $findCommand = array (
                'selector' => $sel,
                'fields' => [ '_id', 'user', 'role', 'view', 'app', 'url', 'specificityName', 'ip', 'lastUsed' ],
                'sort' => [['lastUsed'=>'asc']],
            /'use_index' => 'sortIndex_lastUsed'
               // 'use_index' => 'primaryIndex'
            );*/
            try {
                $call = $this->dbs->findConnection('couchdb')->cdb->find ($findCommand);
            } catch (Exception $e) {
                $debug = true;
                if ($debug)     {
                    echo '<pre>info : $findCommand2='; var_dump ($findCommand); echo '.<br/>'.PHP_EOL;
                    echo 'info : $call='; var_dump ($call); echo '.</pre>'.PHP_EOL;
                };

                $msg = 'NicerAppWebOS FATAL ERROR : while trying to find in \''.$dbName.'\' : '.$e->getMessage();
                echo $msg;
                exit();
            }
            if ($debug) echo 'HTTP status==='.$call->headers->_HTTP->status.', count($call->body->docs)==='.count($call->body->docs).'!<br/>';

            $hasRecord = false;
            $rets = [];
            if ($call->headers->_HTTP->status==='200') {

                foreach ($call->body->docs as $idx => $d) {
                    $hasRecord = true;
                    if ($debug) { echo '$d='; var_dump ($d); }
                    $tn = ( isset($d->theme) ? $d->theme : 'default' );
                    //$d2 = $this->dbs->findConnection('couchdb')->cdb->get($d->_id)->body;
                    $d2 = &$d;
                    $ret = [
                        $tn => [
                            'dbID' => $d2->_id,
                            'themeSettings' => json_decode(json_encode($d2->themeSettings), true),
                            'apps' => json_decode(json_encode((property_exists($d2,'apps')?$d2->apps:[])), true),
                            'background' => ( isset($d2->background) ? $d2->background : '' ),
                            'backgroundSearchKey' => ( isset($d2->backgroundSearchKey) ? $d2->backgroundSearchKey : '' ),
                            'textBackgroundOpacity' => ( isset($d2->textBackgroundOpacity) ? $d2->textBackgroundOpacity : ''),
                            'changeBackgroundsAutomatically' => ( isset($d2->changeBackgroundsAutomatically)
                                ? ($d2->changeBackgroundsAutomatically ? 'true' : 'false')
                                : 'false'
                            ),
                            'backgroundChange_hours' => ( isset($d2->backgroundChange_hours) ? $d2->backgroundChange_hours : ''),
                            'backgroundChange_minutes' => ( isset($d2->backgroundChange_minutes) ? $d2->backgroundChange_minutes : ''),
                            'theme' => $tn
                        ]
                    ];
                    if (isset($d2->user)) $ret[$tn]['user'] = $d2->user;
                    if (isset($d2->role)) $ret[$tn]['role'] = $d2->role;
                    if (isset($d2->url)) $ret[$tn]['url'] = $d2->url;
                    if (isset($d2->view)) $ret[$tn]['view'] = $d2->view;
                    if (isset($d2->app)) $ret[$tn]['app'] = $d2->app;
                    if (isset($d2->specificityName)) $ret[$tn]['specificityName'] = $d2->specificityName;
                    if (isset($tn)) $ret[$tn]['theme'] = $tn;

                    if ($debug) echo '</pre>';

                    $rets = array_merge ($rets, $ret);
                    //break;
                    //return json_decode(json_encode($ret),true);
                }
                /* RETURN ALL THEMES, NOT JUST 1
                if (count($rets)>0) {
                    if ($debug) {
                        echo '<pre>info : $findCommand2='; var_dump ($findCommand); echo '.<br/>'.PHP_EOL;
                        echo 'info : $rets='; var_dump ($rets); echo '.</pre>'.PHP_EOL;
                        exit();
                    }
                    return [
                        'sel' => $sel,
                        'themes' => $rets
                    ];
                }*/
            }
            if ($debug) echo '</pre>';
        //}
        if ($hasRecord) {
            return [
                'sel' => $selector, //sel, // doesn't get used. for logging purposes only - and probably not set correctly.
                'themes' => $rets
            ];
        }

        return false;
    }

}

?>
