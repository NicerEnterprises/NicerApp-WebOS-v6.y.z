if (!na) debugger;
na.mediaPlayer = {
  init : function (settings) {

    na.mediaPlayer.settings = settings;

    $('#siteContent > .vividDialogContent h1')
      .addClass('animatedText_orangeYellow')
      .css({
        display : 'none'
      });
    $('#siteContent > .vividDialogContent h2')
      .addClass('animatedText_ivory')
      .css({
        display : 'none'
      });

    //debugger;
    setTimeout (function() {
      $('.na3D').css({
        width : $('#siteContent > .vividDialogContent').width(),
        height :
          $('#siteContent > .vividDialogContent').height()
          - $('#header').offset().top - $('#header').outerHeight()
      });
      $(window).resize(function() {
        $('.na3D').css({
          width : $('#siteContent > .vividDialogContent').width(),
          height :
            $('#siteContent > .vividDialogContent').height()
            - $('#header').offset().top - $('#header').outerHeight()
        });
      });

      $('.na3D').each(function(idx,el){
          var ac = {
              type : 'GET',
              url : '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/mediaPlayer/ajax_getFileSystemIndex.php',
              success : function (data, ts) {
                  var parameters = { views : [ JSON.parse(data) ] };
                  //debugger;

                  import ('/NicerAppWebOS/logic.vividUserInterface/v6.y.z/3D/na3D.js?m='+na.m.changedDateTime_current()).then((module) => {
                    na.site.settings.na3D = {
                      '.na3D' : new module.na3D_fileBrowser(el, $(el).parent()[0], parameters)
                    }
                  });
              }
          };
          $.ajax(ac);
      });

    }, 20);
  },

  onDoubleClick : function (evt) {
    $('.naAudioPlayerPlaylist').append($(evt.currentTarget).clone(true,true));
    var
    el2 = $('.naAudioPlayerPlaylist .vividButton').last(),
    attributes = $(evt.currentTarget).prop("attributes");

    $.each(attributes, function() {
      if (
        this.name!=='style'
        && this.name!=='class'
      ) el2.attr(this.name, this.value);
    });

    na.mediaPlayer.bindPlaylistClickHandlers();
  },

  bindPlaylistClickHandlers : function() {
    $('.naAudioPlayerPlaylist_explanation').remove();
    $('.naAudioPlayerPlaylist .vividButton').each(function(idx,el) {
      if (!el.hasClickHandler) {
        el.hasClickHandler = true;
        $(el).on('click', function() {
          var fp = na.mediaPlayer.settings.basePath+$(event.currentTarget).attr('filepath');
          $(el).removeClass('vividButtonSelected');
          $(event.currentTarget).removeClass('vividButton').addClass('vividButtonSelected');
          na.mediaPlayer.play(fp);
        })
      }
    });
  },

  play : function (relPath) {
    $('#audioTag')[0].src = relPath;
    $('#audioTag')[0].play();
    $('.audioVolumeBarLabel').html ( 'Volume : '+ Math.round($('#audioTag')[0].volume*100) );
    if (
        $('#btnPlayPause').is('.disabled')
    ) {
        $('#btnPlayPause').removeClass('disabled');
        na.ui.vividButton.onclick({currentTarget:$('#btnPlayPause')[0]});
    }

    setTimeout(na.mediaPlayer.setTimeDisplayInterval,1000);
    na.mediaPlayer.settings.stopped = false;
  },

  setTimeDisplayInterval : function () {
    clearInterval (na.mediaPlayer.settings.timeDisplayInterval);
    //if (!na.mediaPlayer.settings.timeDisplayInterval)
    na.mediaPlayer.settings.timeDisplayInterval = setInterval (function() {
      var
      length = $('#audioTag')[0].duration, // in seconds
      strLength = na.mediaPlayer.convertSecondsToTimeString(length),
      currentTime = $('#audioTag')[0].currentTime, // in seconds
      strCurrentTime = na.mediaPlayer.convertSecondsToTimeString(currentTime);

      if (currentTime==length) {
        clearInterval (na.mediaPlayer.settings.timeDisplayInterval);
        na.mediaPlayer.next();
      } else {

        $('.audioSeekBarLabel_length').html(strLength);
        $('.audioSeekBarLabel_currentTime').html(strCurrentTime);

        var
        widthSeekBar = $('.audioSeekBar').width(),
        widthPlayBar = Math.floor((widthSeekBar * currentTime)/length);

        if (!na.mediaPlayer.settings.maxPlayBarWidth) na.mediaPlayer.settings.maxPlayBarWidth = widthSeekBar;
        $('.audioSeekBar_setting')[0].style.width = (widthPlayBar <= na.mediaPlayer.settings.maxPlayBarWidth ? widthPlayBar : na.mediaPlayer.settings.maxPlayBarWidth)+'px';
      }

    }, 1000);
  },

  convertSecondsToTimeString : function (seconds) {
    var
    hours = Math.floor(seconds/3600),
    minutes = Math.floor( (seconds-(hours * 3600)) / 60 ),
    secs = Math.floor(seconds - (hours * 3600) - (minutes * 60));

    if (hours<10) hours = '0'+hours;
    if (minutes<10) minutes = '0'+minutes;
    if (secs<10) secs = '0'+secs;

    return hours+':'+minutes+':'+secs;
  },

  setVolume : function (evt) {
    var
    widthVolumeBar = $('.audioVolumeBar').width();
    $('#audioTag')[0].volume = evt.offsetX / widthVolumeBar;
    $('.audioVolumeBarLabel').html ( 'Volume : ' + Math.round($('#audioTag')[0].volume * 100) );
    $('.audioVolumeBar_setting').css ({ width : evt.offsetX });
  },


}
