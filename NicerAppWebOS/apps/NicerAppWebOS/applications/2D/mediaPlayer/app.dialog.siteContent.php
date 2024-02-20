<?php
global $naWebOS;
?>
<script type="module" src="/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js"></script>

<div id="header">
  <h1>Media Player on new.nicer.app</h1>
  <h2>This page is under construction.</h2>
</div>
<div class="na3D"></div>
<div id="site3D_label"></div>

<script type="text/javascript">
  import ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/app.js?m='+na.m.changedDateTime_current()).then((module) => {
    var nmp = new module.naApp_mediaPlayer();
  });
</script>
