<?php
    $naRoot = realpath(dirname(__FILE__).'/../../../../../..');
    require_once($naRoot.'/NicerAppWebOS/boot.php');
    require_once(dirname(__FILE__).'/functions.php');

    global $naWebOS;
    $debug = false;
    global $debug;

    $fp = $naRoot.'/NicerAppWebOS/siteCache/app.3D.fileBrowser.json';
    if (false && file_exists($fp)) echo file_get_contents($fp);
    else {
        $mi = [];

        $root = $naRoot.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/music2024';
        //var_dump (file_exists($root)); die();
        $fileFormats = FILE_FORMATS_mp3s;//'/.*/';
        global $fileFormats;
        $excl = '/.*thumbs.*/';
        global $excl;
        $f = getFilePathList ($root, true, $fileFormats, $excl, ['file'], null, 1, true);
        //echo '<pre style="color:blue;">'; var_dump ($root); var_dump ($f); echo '</pre>';
        //$f = getFileDetails ($f);
        //echo '<pre style="color:red;">'; var_dump ($root); var_dump ($f); echo '</pre>';
        $mi[] = [
            'root' => str_replace($rootPath_na, '', $root),
            'thumbnails' => './thumbs',
            'filesAtRoot' => $f
        ];


        if ($debug) {
            $c = [
                'siteContent' => hmJSON($mi, '$mi')
            ];
            echo $naWebOS->getSite($c);
            echo '<script type="text/javascript">setTimeout (function() {na.site.settings.current.running_loadTheme = false; na.site.settings.current.loadingApps = false; na.hms.startProcessing()}, 1500); na.site.transformLinks()</script>';
        } else {
            $smi = json_encode($mi);
            file_put_contents ($fp, $smi);
            echo $smi;
        }
    }

    function getFileDetails (&$f) {
        $f2 = [];
        $x = walkArray ($f, 'getFileDetails_walkKey', 'getFileDetails_walkValue', false, ['f2'=>&$f2]);
        return $f2;
    }

    function getFileDetails_walkKey ($cd) {
        global $excl;
        global $fileFormats;
        global $debug;
        $k = &$cd['k'];
        $v = &$cd['v'];
       // if ($k=='folders') {echo '<pre style="color:blue;background:white;">'; var_dump ($cd); echo '</pre>';}
        if ($k=='folders') {
            $v1 = &chaseToPath (
                $cd['params']['f2'],
                str_replace('folders/', '', $cd['path']),
                true
            );
            $v1 = array_keys ($v);
        };
    }

    function getFileDetails_walkValue ($cd) {
        return $cd;
        /*
        global $excl;
        global $fileFormats;
        global $debug;
        $k = &$cd['k'];
        $v = &$cd['v'];
        //if ($k=='files') { echo '<pre style="color:green">'; var_dump ($cd); echo '</pre>'; }
        if ($k=='files') $v = array_keys ($v);
        return $cd;
        */
    }

?>
