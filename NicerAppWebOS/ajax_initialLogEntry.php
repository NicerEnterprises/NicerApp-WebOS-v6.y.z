<?php
require_once (dirname(__FILE__).'/boot.php');


    if (php_sapi_name() == "cli") { $bd = '[COMMANDLINE]'; $headers_list = $bd; $result_json = []; } else {
      $headers_list = [];
      foreach (getallheaders() as $name => $value) {
          array_push($headers_list, array("name" => $name, "value" => $value));
      }

      $fn = dirname(__FILE__).'/domainConfigs/'.$naWebOS->domain.'/apiKey.whatismybrowser.txt';
      $api_key = file_get_contents($fn);
      $headers = [
          'X-API-KEY: '.$api_key,
      ];

      # -- Create the JSON Structure to POST
      $post_data = array(
          "headers" => $headers_list,
      );

      # -- Create a CURL handle containing your API Key and the data to send
      $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL, "https://api.whatismybrowser.com/api/v3/detect");
      curl_setopt($ch,CURLOPT_POST, true);
      curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($post_data));
      curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      # -- Make the request
      $result = curl_exec($ch);
      $curl_info = curl_getinfo($ch);
      curl_close($ch);

      # -- Decode the api response as json
      $result_json = json_decode($result);

      # -- Echo the Simple Software String
      if (is_object($result_json) && property_exists($result_json, 'detection')) $bd = $result_json->detection->simple_software_string; else $bd = '';
      //echo '<pre>'; var_dump ($result_json); echo '</pre>';
    }


    if (property_exists($naWebOS,'dbs') && is_object($naWebOS->dbs)) {
      $db = $naWebOS->dbs->findConnection('couchdb');
      $cdb = $db->cdb;

      $debug = false;
      $dbName = $db->dataSetName('ip_info');
      try {
          $cdb->setDatabase($dbName);
      } catch (Throwable $e) {
      } catch (Exception $e) {
      }

      // fetch dataRecord
      $findCommand = [
          'selector' => [
              'ip' => $naIP
          ],
          'fields' => ['_id'],
          'use_index' => $naWebOS->globals['cdbDesignDocs']['logentries_pageLoad']
      ];
      //echo '<pre style="padding:8px;border-radius:10px;background:rgba(255,255,255,0.5);color:green;">'; var_dump ($findCommand); echo '</pre>';
      try {
          $call = $cdb->find ($findCommand);
      } catch (Exception $e) {
          //$msg = ' FAILED (boot.php) while trying to find in \''.$dbName.'\' : '.$e->getMessage();
          //trigger_error ($msg, E_USER_ERROR);
          //echo $msg;
          //return false;
          //die();
      }

      $ipInfo = null;
      if (isset($call) && property_exists($call, 'body'))
      foreach ($call->body->docs as $docIdx => $doc) {
          $call2 = $cdb->get($doc->_id);
          //echo '<pre>'; var_dump($call2); echo '</pre>';
          if (property_exists($call2, 'body') && property_exists($call2->body, 'ipInfo'))
              $ipInfo = $call2->body->ipInfo;
          break;
      }

      if (is_null($ipInfo)) {
          $apiKey = trim(file_get_contents(dirname(__FILE__).'/apps/NicerAppWebOS/applications/2D/analytics/apiKey.ipinfo.io.txt'));
          $xec = 'curl -H "X-Forwarded-For: nicer.app" ipinfo.io/'.$naIP.'?token='.$apiKey;
          exec ($xec, $output, $result);
          $ipInfo = json_decode(join('',$output), true);

          $rec = [
              'ip' => $naIP,
              'ipInfo' => $ipInfo
          ];
          if (isset($call)) $cdb->post($rec);
      }

      $err = [
          'type' => 'New request',
          's1' => (
              session_status() === PHP_SESSION_NONE
              ? time()
              : (array_key_exists('started', $_SESSION)
                ? $_SESSION['started']
                : time()
              )//microtime(true)

          ),
          's2' => time(),//microtime(true),
          'i' => (
              session_status() === PHP_SESSION_NONE
              ? false
              : $_SESSION['startedID']
          ),
          'isIndex' => $_SERVER['SCRIPT_NAME']==='/NicerAppWebOS/index.php',
          'isBot' => $naIsBot,
          'isLAN' => $naLAN,
          'isDesktop' => $naIsDesktop,
          'isMobile' => $naIsMobile,
          'headers' => $headers_list,
          'headersResult' => $result_json,
          'bd' => $bd,
          'bdDetails' => json_decode(json_encode($result_json), true),
          'ipInfo' => $ipInfo,
          'browserMarketSharePercentage' => $naBrowserMarketSharePercentage,
          'to' => $dtz_offset,
          'ts' => $timestamp,
          'ip' => $naIP,
          'sid' => session_id(),
          'nav' => $naVersionNumber,
          'request' => $dbg
      ];
      global $naLog;
      $naLog->add ( [ $err ] );
      //trigger_error ($msg, E_USER_NOTICE);
      //echo '<pre>'; var_dump ($_SERVER); die();
    }

?>
