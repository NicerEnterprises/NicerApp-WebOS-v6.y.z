<?php
header('Content-type: text/javascript');
$rt = realpath(dirname(__FILE__).'/..');

if (!array_key_exists('f', $_GET)) die (500);
if (strpos($_GET['f'], '..')!==false) die (403);
if (!file_exists($rt.$_GET['f']) || !is_readable($rt.$_GET['f'])) die (403);

$js = file_get_contents($rt.$_GET['f']);
echo timestampJSmodule1($js);

function timestampJSmodule1 ($js) {
  $rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/from\s+[\'"](.*?)[\'"]/', $js, $matches);
  $matches[2] = [];
  $matches[3] = [];

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if (strpos($rp,'&')===false) {
        $m = filemtime($rt.$rp);
        $matches[2][] = $rt.$rp;
        $matches[3][] = $m;
    } else {
        $matches[2][] = $rt.$rp;
        $matches[3][] = false;
    }
  }

  foreach ($matches[1] as $idx => $relPath) {
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace($relPath, $relPath.'&c='.date('Ymd-His',$m), $js);
    }
  }
  //echo '<pre style="background:red;color:yellow;">'; var_dump($matches); echo '</pre>';

  return $js;
}
?>
