<?php
require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
$fncn = __FILE__;

global $naWebOS;
$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$adb = $naWebOS->dbsAdmin->findConnection('couchdb');
$acdb = $adb->cdb;

$dataSetName = $db->dataSetName('data_by_users'); // i know, couchdb calls a 'table' a 'database'. and that sux.
$cdb->setDatabase ($dataSetName, false);

if (!array_key_exists('seoValue', $_REQUEST)) {
    //$dataID = cdb_randomString(50);
    $newID = '';
    $done = false;
    $tokenLength = 2;
    $doneCounter = 0;
    while (!$done) {
        $newID = randomString($tokenLength);

        $findCommand = [
            'selector' => [ 'seoValue' => $newID ],
            'fields' => [ '_id' ]
        ];
        $call = $cdb->find ($findCommand);
        $done = $call->headers->_HTTP->status!==200;
        if (!$done && $doneCounter>10) {
            $tokenLength++;
            $doneCounter = 0;
        }
        $doneCounter++;
    }

    $SEO_value = $newID;
    echo $SEO_value;
} else {

    $findCommand = [
        'selector' => [
            'seoValue' => $_REQUEST['seoValue']
        ],
        'fields' => [ '_id' ]
    ];

    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        $msg = $fncn.' FAILED while trying to find in \''.$dataSetName.'\' : '.$e->getMessage();
        trigger_error ($msg, E_USER_NOTICE);
        echo $msg;
        return false;
    }

    //echo '<pre>'; var_dump ($findCommand); var_dump ($call);
    $r = '';
    if (
        is_object($call)
        && is_object($call->body)
        && is_array($call->body->docs)
    ) {
        if (count($call->body->docs)===0) {
            $r = '';
        } elseif (count($call->body->docs)===1) {
            //echo '<pre>';
            //var_dump ($call->body->docs); //die();
            $call2 = $cdb->get ($call->body->docs[0]->_id);
            //var_dump ($call2->body); //die();
            /*
            $r = null;
            if (property_exists($call2->body, 'viewSettings')) {
                foreach ($call2->body->viewSettings as $fp => $vs) {
                    if (property_exists($vs,'SEO_value')) $r = $vs->SEO_value;
                }
                //$vs = $call2->body->viewSettings;
                //if (property_exists($vs,'SEO_value')) $r = $vs->SEO_value;
            }*/
            if (property_exists($call2->body,'seoValue')) $r = $call2->body->seoValue;
        } elseif (count($call->body->docs)>1) {
            $call2 = $cdb->get ($call->body->docs[0]->_id);
            if (property_exists($call2->body,'seoValue')) $r = $call2->body->seoValue;
        }
    }

    echo $r;
}
?>
