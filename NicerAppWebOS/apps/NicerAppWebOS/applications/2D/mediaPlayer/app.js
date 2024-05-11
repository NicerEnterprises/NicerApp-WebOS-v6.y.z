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
    evt.preventDefault();
  },

  bindPlaylistClickHandlers : function() {
    $('.naAudioPlayerPlaylist_explanation').remove();
    $('.naAudioPlayerPlaylist .vividButton').each(function(idx,el) {
      if (!el.hasClickHandler) {
        el.hasClickHandler = true;
        el.id = 'playlist_'+idx;
        $(el).on('click', function() {
          var fp = na.mediaPlayer.settings.basePath+$(event.currentTarget).attr('filepath');

          $('.naAudioPlayerPlaylist .vividButtonSelected').removeClass('vividButtonSelected').addClass('vividButton');

          $(event.currentTarget).removeClass('vividButton').addClass('vividButtonSelected');
          na.mediaPlayer.play(el.id, fp);
        })
      }
    });
  },

  play : function (id, relPath) {
    $('#audioTag')[0].src = relPath;
    $('#audioTag')[0].play();
    $('.audioVolumeBarLabel').html ( 'Volume : '+ Math.round($('#audioTag')[0].volume*100) );
    if (
        $('#btnPlayPause').is('.disabled')
    ) {
        $('#btnPlayPause').removeClass('disabled');
        na.ui.vividButton.onclick({currentTarget:$('#btnPlayPause')[0]});
    }

		var pl = $('.naAudioPlayerPlaylist')[0];
		for (var i=0; i<pl.children.length; i++) {
            if (pl.children[i].id==id || (pl.children[i].children[0] && pl.children[i].children[0].id==id)) na.mediaPlayer.settings.playingIndex = i;
		};

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
        debugger;
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

	playpause : function () {
		if (na.mediaPlayer.settings.stopped || na.mediaPlayer.settings.paused) {
			$('#audioTag')[0].play();
			na.mediaPlayer.settings.paused = false;
			na.mediaPlayer.settings.stopped = false;
            //$('#from_play_to_pause')[0].beginElement();
            //$('#btnPlayPause').addClass('selected');
            //na.ui.vb.hoverOver(na.ui.vb.settings.buttons['btnPlayPause']);
		} else {
			$('#audioTag')[0].pause();
			na.mediaPlayer.settings.paused = true;
			na.mediaPlayer.settings.stopped = false;
            //$('#from_pause_to_play')[0].beginElement();
            //$('#btnPlayPause').removeClass('selected');
            //na.ui.vb.hoverOut(na.ui.vb.settings.buttons['btnPlayPause']);
		}
	},

	stop : function () {
    na.ui.vividButton.onclick({currentTarget:$('#btnPlayPause')[0]});
    $('#btnPlayPause').addClass('disabled');
		$('#audioTag')[0].pause();
		na.mediaPlayer.settings.stopped = true;

        $('.mp3').each (function (index,element) {
			if (this.id=='') return false;
		});
		$('#mp3descText').fadeOut (1000);
		setTimeout (function () {
			$('#siteIntroText').fadeIn (1000);
		}, 1010);
	},

  next : function () {
    if (na.mediaPlayer.settings.playingIndex===false) {
      return false;
    } else if (na.mediaPlayer.settings.shuffle) {
      var found2 = true;
      while (found2) {
        var pl = $('#playlist')[0];
        if (!na.mediaPlayer.settings.shuffleList) {
          na.mediaPlayer.settings.shuffleList = pl.children;
          for (var i=0; i<pl.children.length; i++) {
            pl.children[i].shufflePlayed = i === na.mediaPlayer.settings.playingIndex;
          }
        }
        var found = false, i = 0;
        while (!found && i < 100) {
          var newIndex = Math.floor(Math.random() * (pl.children.length - 1));
          found = pl.children[newIndex] && !pl.children[newIndex].shufflePlayed;
          i++;
          if (found) {
              i = 0;
              found2 = false;
          };
        }
        if (i == 99) {
          found2 = !found && na.mediaPlayer.settings.repeating;
          if (found2) delete na.mediaPlayer.settings.shuffleList;
        }
      }
      for (var i=0; i<pl.children.length; i++) {
        var newIndex2 = 'playlist_' + newIndex;
        if (pl.children[i].id == newIndex2) {
          na.mediaPlayer.selectMP3 (newIndex2, $(pl.children[i]).attr('file'), false);
          return true;
        }
        if (pl.children[i].children[0] && pl.children[i].children[0].id== newIndex2) {
          na.mediaPlayer.selectMP3 (newIndex2, $(pl.children[i].children[0]).attr('file'), false);
          return true;
        }
    }

    } else {
        var pl = $('.naAudioPlayerPlaylist')[0];
        debugger;
        for (var i=0; i<pl.children.length; i++) {
            var newIndex = 'playlist_' + (na.mediaPlayer.settings.playingIndex + 1);
            if (i == na.mediaPlayer.settings.playingIndex + 1) {
                na.mediaPlayer.selectMP3 (pl.children[i].id, $(pl.children[i]).attr('file'), false);
                return true;
            }
            if (pl.children[i].children[0] && pl.children[i].children[0].id== newIndex) {
                na.mediaPlayer.selectMP3 (newIndex, $(pl.children[i].children[0]).attr('file'), false);
                return true;
            }
        }
        if (na.mediaPlayer.settings.repeating) {
            debugger;
            var
            newIndex = 'playlist_0',
            i = 0;

            if (pl.children[i].id == newIndex) {
                na.mediaPlayer.selectMP3 (newIndex, $(pl.children[i]).attr('filepath'), false);
                return true;
            }
            if (pl.children[i].children[0] && pl.children[i].children[0].id== newIndex) {
                na.mediaPlayer.selectMP3 (newIndex, $(pl.children[i].children[0]).attr('filepath'), false);
                return true;
            }
        }
    };
  },
	selectMP3 : function (id, file) {
    if (na.mp.settings.ignoreClick) { na.mp.settings.ignoreClick = false; return false; }

    clearInterval (na.mp.settings.timeDisplayInterval);

    var firstRun = na.mp.settings.firstRun;
    if (firstRun) na.mp.settings.firstRun = false;

		na.mp.settings.activeID = id;

    na.mp.settings.playingIndex = false;
    delete na.mp.settings.stopped;

    var fp = na.mediaPlayer.settings.basePath+$('#'+id).attr('filepath');
    na.mp.play (id, fp);

    $('.naAudioPlayerPlaylist .vividButtonSelected').removeClass('selected').removeClass('vividButtonSelected').addClass('vividButton');
    $('#'+id).addClass('selected').removeClass('vividButton').addClass('vividButtonSelected');
    $('#btnPlayPause').addClass('selected');
    $('#line2').addClass('atPlay');
    $('#from_pause_to_play')[0].beginElement();


  },

	mute : function () {
		if (na.mediaPlayer.settings.muted) {
			$('#audioTag')[0].muted = false;
			na.mediaPlayer.settings.muted = false;
		} else {
			$('#audioTag')[0].muted = true;
			na.mediaPlayer.settings.muted = true;
		}
	},

	toggleShuffle : function () {
		na.mediaPlayer.settings.shuffle = !na.mediaPlayer.settings.shuffle;
	},

	toggleRepeat : function () {
		na.mediaPlayer.settings.repeating = !na.mediaPlayer.settings.repeating;
	},

  seek : function (evt) {
    var
    length = $('#audioTag')[0].duration, // in seconds
    strLength = na.mediaPlayer.convertSecondsToTimeString(length),
    currentTime = $('#audioTag')[0].currentTime;

    $('.audioSeekBarLabel_length').html(strLength);
    $('.audioSeekBarLabel_currentTime').html(currentTime);

    var
    widthSeekBar = $('.audioSeekBar').width(),
    widthPlayBar = evt.offsetX;//Math.round((widthSeekBar * evt.offsetX)/length),
    newCurrentTime = Math.round((widthPlayBar * length)/widthSeekBar);

    $('.audioSeekBar_setting')[0].style.width = widthPlayBar+'px';
    $('#audioTag')[0].currentTime = newCurrentTime;
  }

}
na.mp = na.mediaPlayer;
