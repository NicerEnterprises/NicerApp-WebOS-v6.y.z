<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$debug = false;
global $naWebOS;
$cdb = $naWebOS->dbs->findConnection('couchdb')->cdb;


if (!array_key_exists('database',$_POST)) {
    $dbg = [
        '$_REQUEST' => $_REQUEST
    ];
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database with '.json_encode($dbg).'). Event logged.');
}
if (strpos($_POST['database'], '_documents_')===false)
    cdb_error (403, null, 'Hacking attempt detected (attempt to access database '.$_POST['database'].'). Event logged.');


$cdb->setDatabase(str_replace('_documents','_tree',$_POST['database']),false);
$call = $cdb->get ($_POST['id']);
$call->body->text = $_POST['text'];

try { $call = $cdb->post($call->body); } catch (Exception $e) {
    cdb_error (500, $e, 'Could not add record'); exit();
}
if ($debug) { echo '$call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

$oldPath = realpath(dirname(__FILE__).'/../../../../..').'/siteData/'.$naWebOS->domain.'/'.$_POST['relFilePath'];
$newPath = realpath(dirname(__FILE__).'/../../../../..').'/siteData/'.$naWebOS->domain.'/'.$_POST['newRelFilePath'];
$xec = 'mv "'.$oldPath.'" "'.$newPath.'"';
exec ($xec, $output, $result);
$dbg = array (
    'xec' => $xec,
    'output' => $output,
    'result' => $result
);
if ($debug) { echo '<pre style="color:green">'; var_dump ($dbg); echo '</pre>'; }

echo 'status : Success'; 
?>
