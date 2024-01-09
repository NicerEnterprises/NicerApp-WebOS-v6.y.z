import { na3D_fileBrowser } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/3D/na3D.js';

export class naApp_mediaPlayer {
  constructor (settings) {
    $('#siteContent > .vividDialogContent h1')
      .addClass('animatedText_orangeYellow')
      .css({
        fontFamily : 'Arial'
      });
    $('#siteContent > .vividDialogContent h2')
      .addClass('animatedText_ivory')
      .css({
        fontFamily : 'Arial'
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

                  na.settings.na3D= {
                    '#na3D' :
                      new na3D_fileBrowser(el, $(el).parent()[0], parameters)
                  };
              }
          };
          $.ajax(ac);
      });

    }, 500);
  }
}
