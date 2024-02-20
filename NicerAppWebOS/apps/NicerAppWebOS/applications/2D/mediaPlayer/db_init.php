<?php
$myRoot = realpath(dirname(__FILE__));
$naRoot = realpath(dirname(__FILE__).'/../../../../../..');
require_once ($naRoot.'/NicerAppWebOS/boot.php');
global $naWebOS;

$db = $naWebOS->dbsAdmin->findConnection('couchdb');
try {
  $dataSetName = $db->dataSetName('desktopDefinitions');
  $db->cdb->setDatabase($dataSetName, false);
} catch (Exception $e) {
  $db->createDataSet_desktopDefinitions($myRoot);
}
?>
