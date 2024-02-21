export  class naApp_mediaPlayer {
  constructor (settings) {
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
                    na.site.settings.na3D= {
                      '.na3D' : new module.na3D_fileBrowser(el, $(el).parent()[0], parameters)
                    }
                  });
              }
          };
          $.ajax(ac);
      });

    }, 20);
  }
}
