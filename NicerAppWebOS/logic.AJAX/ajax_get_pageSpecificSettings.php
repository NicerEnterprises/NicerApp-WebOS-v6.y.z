<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');
echo '<!--'; var_dump ($_GET); echo '-->';
    global $naWebOS;
    echo $naWebOS->getPageCSS(true, false, $_GET['includeClientOnlyThemes']==='true'?true:false, $_GET['stickToCurrentSpecificity']==='true'?true:false, $_GET['specificityName']);
?>
