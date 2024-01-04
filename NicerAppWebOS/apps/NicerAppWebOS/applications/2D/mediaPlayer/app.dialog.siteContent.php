<?php
global $naWebOS;
?>
<script type="module" src="/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js"></script>
<script type="text/javascript">
  setTimeout (function() {
    na.loadModule (
      '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/app.js&c=<?php echo date('Ymd_His', filemtime(dirname(__FILE__).'/app.js'))?>',
      'mediaPlayer',
      'naApp_mediaPlayer',
      ''
    );
  }, 200);
</script>
<div id="header">
  <h1>Media Player on new.nicer.app</h1>
  <h2>This page is under construction.</h2>
</div>
<div class="na3D"></div>
