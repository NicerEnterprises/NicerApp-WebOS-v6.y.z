<?php
global $naWebOS;
?>
  <link href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/index.css"/>
  <script type="text/javascript">
    na.m.waitForCondition ('mediaPlayer load', function() {
        return na.d && na.d.s && na.d.s.negotiateOptions;
    }, function () {
        if (!na.d.s.visibleDivs.includes('#siteToolbarRight')) {
            na.d.s.visibleDivs.push('#siteToolbarRight');
            na.d.resize();
        }
    }, 20);
  </script>
	<div class="naAudioPlayerUI">
    <div id="btnRearrange" class="vividButton" style="position:relative;width:calc(100% - 20px)" onclick=" $('.na3D > canvas').remove(); import ('/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/app.js?m='+na.m.changedDateTime_current()).then((module) => { var nmp = new module.naApp_mediaPlayer(); });">Re-arrange Layout</div>
            <audio id="audioTag"></audio>

            <div class="audioPlayerButtons">
                <div id="btnPlayPause" class="vividButton4" buttonType="btn_audioVideo_playPause" onclick="na.mediaPlayer.playpause()"></div>
                <div id="btnMuteUnmute" class="vividButton4" buttonType="btn_audioVideo_muteUnmute" onclick="na.mediaPlayer.mute()"></div>
                <div id="btnShuffle" class="vividButton4" buttonType="btn_audioVideo_shuffle" onclick="na.mediaPlayer.toggleShuffle()"></div>
                <div id="btnRepeat" class="vividButton4" buttonType="btn_audioVideo_repeat" onclick="na.mediaPlayer.toggleRepeat()"></div>
            </div>
            <div class="flexBreak"></div>
            <div class="audioPlayerControls">
                <div class="audioVolumeBar" onclick="na.mediaPlayer.setVolume(event);">
                    <div class="audioVolumeBar_setting" style="width:calc(100% - 4px);"></div>
                </div>
                <div style="width:100%;">&nbsp;</div>
                <div class="audioSeekBar" onclick="na.mediaPlayer.seek(event);">
                    <div class="audioSeekBar_setting" style="width:0px;"></div>
                </div>
            </div>
            <div class="audioPlayerControlsLabels">
                <div class="audioVolumeBarLabel" style="text-align:center">Volume : 100</div>
                <div class="audioSeekBarLabel">
                    <div class="audioSeekBarLabel_currentTime">0:00</div>
                    <div class="audioSeekBarLabel_length">1:15:00</div>
                </div>
            </div>
		</div>
		<div class="naAudioPlayerPlaylist vividScrollpane">
        <div class="naAudioPlayerPlaylist_explanation">
            <p>Click on a cube (which represents a file) in the 3D field to bring up it's sibling files.<br/>
            Double click the button for a file to include it in this playlist.</p>

            <p>Or, double-click on a cube to put that file in this playlist straight away.</p>
        </div>
		</div>
		<div class="naFolderFilesList vividScrollpane"></div>
	</div>
