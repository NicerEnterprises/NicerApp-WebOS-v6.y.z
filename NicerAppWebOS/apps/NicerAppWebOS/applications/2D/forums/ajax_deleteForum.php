<?php 
    require_once (realpath(dirname(__FILE__).'/../../../..').'/NicerAppWebOS/boot.php');
    require_once (dirname(__FILE__).'/boot.php');
    global $naWebOS;
    global $naForums;
    
    $naForums->deleteForum ($_REQUEST['categoryName'], $_REQUEST['forumName']);
?>
