<?php
global $naWebOS;
$appContentBasePath = str_replace(
  $naWebOS->basePath,'',realpath(dirname(__FILE__).'/music2024')
);
$appCodeFile =
  '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/app.js';
$appCodeLink =
  $appCodeFile.'?m='.date('Ymd_His', filemtime($naWebOS->basePath.$appCodeFile));
?>
<script type="module" src="/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js"></script>
<script type="text/javascript" src="<?php echo $appCodeFile?>"></script>

<div id="header">
  <h1>Media Player on new.nicer.app</h1>
  <h2>This page is under construction.</h2>
</div>
<div class="na3D"></div>
<div id="site3D_label"></div>

<script type="text/javascript">
  na.mediaPlayer.init({basePath:'<?php echo $appContentBasePath;?>'});
</script>
