<?php
function namp_getAlbumsTree() {
  $fncn = __FILE__.'::namp_getAlbumsTree()';
  $path = dirname(__FILE__).'/music2024';
  //echo $path.'<br/>';

  $dirs = getFilePathList ($path, true, FILE_FORMATS_mp3s, null, ['file'], null,null,true);

  $dirs2 = [];
  $cp = [
    'dirs2' => &$dirs2
  ];
  walkArray ($dirs, 'namp_getAlbumsTree_walkKey', null, false, $cp);

  /*
  if (!array_key_exists('files',$dirs)) {
    $msg = $fncn.' : FATAL ERROR : could not find any $dirs to display';
    trigger_error ($msg, E_USER_ERROR);
    echo $msg;
    exit();
  }
  $dirs = $dirs['folders'];
  */
  echo '<pre class="preFilesList">'; var_dump ($dirs); echo '</pre>'; exit();
  return $dirs  ;
}

function namp_getAlbumsTree_walkKey ($cd) {
  $cp = &$cd['params'];
  $dirs2 = &$cp['dirs2'];
  //echo '<pre class="preFilesList">'; var_dump ($cd); echo '</pre>';
  //if ($cd['k']!=='folders') $dirs2[$cd['k']] = $cd['v'][$cd['k']][0];
  if (array_key_exists(0,$cd['v'])) {
    //echo '<pre class="preFilesList">'; var_dump ($cd); echo '</pre>';
    $dirs2[$cd['k']] = $cd['v'][0];
    //$cd['params']['dirs2'] = &$dirs2[$cd['k']];
    //echo '<pre class="preFilesList">'; var_dump ($dirs2); echo '</pre>';
  }
}
?>
