sa.apps.loaded.search_youtube  = {
	settings : { 
		ready : true,
		loadedIn : {
			'#siteVideoSearch' : {
				settings : {
					initialized : false,
                    onresizeTimer : undefined
				},
				onload : function (settings) {
					var 
					pw = window.parent.window,
					pd = window.parent.document,
					psc = pw.sa.s.c,
					psa = pw.sa;
					
					//sa.apps.loaded.search_youtube = sa.search.youtube.siteCode;
					
					/* done from sa.desktop already!
					jQuery(window).resize(function(){
						siteVideoSearch_resize();
					});
					sa.vcc.settings['siteVideoSearch'].afterResize = sa.m.traceFunction(function () {
						siteVideoSearch_resize();
					});
					*/
					
					/*
					sa.m.fireAppEvent ({
						divName : '#siteVideoSearch',
						eventName : 'onresize'
					});*/
					//siteVideoSearch_resize();
					
					if (!jQuery('#siteVideoSearch__dialog')[0]) return false;
					
					
					jQuery('#siteVideoSearch__dialog')[0].appendChild (jQuery('#siteVideoSearch__header')[0].parentNode.removeChild(jQuery('#siteVideoSearch__header')[0]));
					//jQuery('#siteVideoSearch')[0].appendChild (jQuery('#siteVideoSearch__footer')[0].parentNode.removeChild(jQuery('#siteVideoSearch__footer')[0]));
					jQuery('#siteVideoSearch')[0].appendChild (jQuery('#siteVideoSearch__content')[0].parentNode.removeChild(jQuery('#siteVideoSearch__content')[0]));
					
					
					
					sa.apps.loaded.search_youtube.settings.appContentHTMLelement = $('#saApp_search_youtube_thumbViewer__scrollpane')[0];
					//sa.apps.loaded.search_youtube.settings.appContentCode = sa.apps.loaded.search_youtube;
					//debugger;
					
					
					//sa.apps.loaded.search_youtube.onPage('frontpage');
					//debugger;
					//if (sa.s.c.settings.current.visible['#siteVideoSearch']) {
						
						
						/* RE-ENABLE LATER (disabled 2017-04-16) sa.apps.loaded.search_youtube(jQuery('#siteVideoSearch_input').val());*/
						
						
						
					//};
					//psa.apps.loaded.search_youtube.onResize();
                    sa.s.c.divertEnterKey(jQuery('#siteVideoSearch__input')[0], function (evt) {
                        sa.apps.search.search_youtube(jQuery('#siteVideoSearch__input').val());
                    });

                    sa.apps.search.search_youtube(jQuery('#siteVideoSearch__input').val());

				},
				onresize : function (settings) {
                    sa.apps.loaded.search_youtube.onresize();
                    
                    /*
					if (sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer) {
                        sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer = setTimeout(function () {
                            clearTimeout (sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer);
                        }, 500);
                        
                    } else {
                        sa.vcc.applyTheme ('siteVideoSearch');
                        
                        setTimeout (function() {
                                / *
                                jQuery('#siteVideoSearch__content, #siteVideoSearch__searchresults__table2').css ({
                                    height : 
                                        jQuery('#siteVideoSearch').height()
                                        - jQuery('#siteVideoSearch__header__tr').height()
                                        - jQuery('#siteVideoSearch__searchresults__header')[0].offsetTop
                                });
                                * /

                                jQuery('#saApp_search_youtube_thumbViewer__scrollpane, #saApp_search_youtube_thumbViewer__scrollpane__container').css({
                                    height : 
                                        jQuery('#siteVideoSearch__content').height() 
                                        - jQuery('#siteVideoSearch__content')[0].offsetTop
                                        - jQuery('#siteVideoSearch__searchResult_details__td').height() 
                                        - 20
                                        //- jQuery('#siteVideoSearchIframeContainer').height() 
                                        //- jQuery('#siteVideoSearch_header_thanks').height() 
                                        //- jQuery('#musicSearchMenu').height() 
                                });
                                //debugger;
                                setTimeout (function() {
                                    sa.sp.containerSizeChanged(jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0], true);
                                    //sa.sp.containerSizeChanged (jQuery('#saApp_search_youtube_thumbViewer')[0], true);
                                }, 200);
                        }, 200);
                        
                        sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer = setTimeout(function () {
                            clearTimeout (sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer);
                        }, 500);
                        
                    };
                    */
				}
				
			}				
		},
		resizing : {}			
	},
	
	
	
	jsonViewer : function (data) {
		var opt = { 
			htmlID : 'saApp_search_youtube_jsonViewer', 
			opacity : 0.65,
			fastInit : true
		};
		hm (data, 'Youtube Results', opt );
	},
	//y1.ytimg.com/vi/54momizl0TM/mqdefault.jpg
	thumbViewer : function (data) {
		var 
		pw = window.parent.window,
		pd = window.parent.document,
		psc = pw.sa.s.c,
		psa = pw.sa,
		htmlID = 'saApp_search_youtube_thumbViewer__scrollpane',
		d = jQuery('#'+htmlID),
		dw = d.width(),
		dh = d.height(),
		ts1 = psa.site.themes.site.calculate.stages[1],
		toUse = ts1.backgrounds.toUse,
		oci = toUse.orderCurrentIdx,
		sbg = toUse['#siteBackground'],
		hor = 1,
		html = '';//'<div style="width:100%;height:auto;">';

		
		//debugger;
		for (var i=0; i<data.videoIDs.length; i++) {
			var 
			vid = data.videoIDs[i],
			tw = 100;
			
			for (var vidID in vid) { break; }
			//debugger;
			
			html +='<img id="saThumb_'+i+'" class="saThumb" src="//i1.ytimg.com/vi/'+vidID+'/mqdefault.jpg" style="z-index:99999997; opacity:0.0001; width:'+tw+'px;height:56px;" onclick="sa.apps.loaded.search_youtube.thumbView(\''+vidID+'\');" titleYoutube="'+vid[vidID].title+'"/>'
		};
		
		//html += '</div>';
        
		//jQuery('#'+htmlID).html (html).css({overflow:'visible'}).parents().css({overflow:'visible'});
		jQuery('#'+htmlID).html (html).css({display:'none'}).fadeIn('slow', function () {
			//debugger;
			sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.initialized = true;
			sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.ready = true;
            
            sa.sp.containerSizeChanged (jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0], true);
			
			//jQuery('#saApp_search_youtube_thumbViewer__scrollpane').css ({ left : 0 });
            
            if (sa.m.settings.initialized.site === true)
                sa.m.fireAppEvent ({
                    divName : '#siteVideoSearch',
                    eventName : 'onresize'
                });
		});
                
	},
	
	onresize : function() {
                //return false;
		///sa.apps.loaded.search_youtube.settings.resizing = {};
		//jQuery('#siteVideoSearch_searchresults').css({height:'100%'}); // BAD IDEA
		//sa.sp.containerSizeChanged(jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0], true);
		//
		//
        
        sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer2 = 'go';
        if (sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer2) {
            clearTimeout (sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer);
            sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer2 = setTimeout(function () {
        
                jQuery('#siteVideoSearch').animate({opacity:1}, 100, 'swing', function () {
                sa.vcc.applyTheme ('siteVideoSearch');
                
                setTimeout (function() {
                    //debugger;
                    jQuery('.saThumb').stop().css ({position:'relative',opacity:1,width:100,height:52,left:'',top:''}).unbind('mouseenter mouseleave');
                    //debugger;

                            
                    //if (sa.apps.loaded.search_youtube.settings.resizeTimeout) clearTimeout (sa.apps.loaded.search_youtube.settings.resizeTimeout);
                    
                    //sa.apps.loaded.search_youtube.settings.resizeTimeout = setTimeout (function () {
                        jQuery('#siteVideoSearch_header_td, #siteVideoSearch_header_td2, #siteVideoSearch_header_tr').css({height:'1.1em'});
                        jQuery('#siteVideoSearch_searchResult_details, #siteVideoSearch_searchResult_details_td, #siteVideoSearch_searchResult_details_tr').css({height:'3em'});
                        //jQuery('#saApp_search_youtube_thumbViewer__scrollpane, #saApp_search_youtube_thumbViewer__scrollpane__container, #saApp_search_youtube_thumbViewer__scrollpane_td, #saApp_search_youtube_thumbViewer__scrollpane_tr').css({height:'',left:0});
                        //jQuery('#saApp_search_youtube_thumbViewer__scrollpane').css({height:'auto'});
                        
                        //setTimeout(function() {
                            /*
                            jQuery('#siteVideoSearch__content').css ({
                                height : 
                                    jQuery('#siteVideoSearch__contentDimensions').height()
                            });
                            */

                            setTimeout (function () {
                                var
                                nh = 
                                    jQuery('#siteVideoSearch__content').height(),
                                th = jQuery('#siteVideoSearch__contentDimensions').height();

                                
                                
                                jQuery('#saApp_search_youtube_thumbViewer__scrollpane, #saApp_search_youtube_thumbViewer__scrollpane__container, #saApp_search_youtube_thumbViewer__scrollpane_td, #saApp_search_youtube_thumbViewer__scrollpane_tr, #siteVideoSearch_searchresults, #saApp_search_youtube_thumbviewer_tr, #saApp_search_youtube_thumbviewer_td, #saApp_search_youtube_thumbViewer__contentDimensions').css ({
                                    left : 0,
                                    width : jQuery('#siteVideoSearch__content').width(), 
                                    //width : '100%',                                                                                                                                                                                                        
                                    height : nh
                                });
                                
                                
                                jQuery('#saApp_search_youtube_thumbViewer__scrollpane__container').css({
                                    height : 
                                        jQuery('#siteVideoSearch__content').height() 
                                        - jQuery('#siteVideoSearch__content')[0].offsetTop
                                        - jQuery('#siteVideoSearch__searchResult_details__td').height() 
                                        - 20
                                        //- jQuery('#siteVideoSearchIframeContainer').height() 
                                        //- jQuery('#siteVideoSearch_header_thanks').height() 
                                        //- jQuery('#musicSearchMenu').height() 
                                });
                                
                                
                                sa.sp.containerSizeChanged (jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0]);
                                
                                jQuery('#saApp_search_youtube_thumbViewer').css ({
                                    height : nh,
                                    overflow : 'hidden'
                                });
                                

                                sa.apps.loaded.search_youtube.settings.resizing = {};
                                
                                
                                //if (!sa.apps.loaded.search_youtube.settings.resizeTimer) 
                                //sa.apps.loaded.search_youtube.settings.resizeTimer = setTimeout (function () {
                                
                                
                                setTimeout(function () {
                                        delete sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.onresizeTimer;
                                        

                                            if (jQuery('.saThumb').length==0) return false;
                                    
                                            var r = {};
                                            var divOffset = false;
                                            jQuery('.saThumb').each (function(idx) {
                                                if (!divOffset) divOffset = jQuery(this).offset();
                                                r[this.id] = jQuery(this).offset();
                                                r[this.id].top -= divOffset.top;
                                                r[this.id].left -= divOffset.left;
                                            });
                                            
                                            
                                            jQuery('.saThumb').each (function(idx) {
                                                jQuery(this).css ({
                                                    position : 'absolute',
                                                    //float : 'left',
                                                    left : r[this.id].left,
                                                    top : r[this.id].top
                                                });
                                            });

                                            /*
                                            jQuery('#saApp_search_youtube_thumbViewer__scrollpane__container').css ({
                                                height : th
                                            });
                                            
                                            jQuery('#saApp_search_youtube_thumbViewer__scrollpane').css ({ 
                                                left : 0,
                                                height : jQuery('.saThumb').last()[0].offsetTop + jQuery('.saThumb').last()[0].offsetHeight + 10 - 100
                                            });
                                            
                                            
                                            setTimeout (function () {
                                                sa.sp.containerSizeChanged(jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0], true);
                                                //sa.sp.containerSizeChanged (jQuery('#saApp_search_youtube_thumbViewer')[0], true);
                                            }, 100);
                                            */
                                            
                                            //debugger;
                                            /*
                                            setTimeout (function() {
                                                sa.sp.containerSizeChanged(jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0], true);
                                                //sa.sp.containerSizeChanged (jQuery('#saApp_search_youtube_thumbViewer')[0], true);
                                            }, 50);
                                            */

                                            setTimeout (function() {
                                                
                                                setTimeout (function() {
                                                    //jQuery('#siteVideoSearch').animate({opacity:1},{duration:400});
                                                    sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.resized = true;

                                                }, 700);
                                            
                                                var loaderIcon = sa.apps.loaded.search_youtube.settings.loadedIn['#siteVideoSearch'].settings.loaderIcon;
                                                jQuery(loaderIcon).fadeOut('slow', function() {
                                                    jQuery(loaderIcon).remove();
                                                });
                                            }, 10);

                                            
                                            //jQuery('#saApp_search_youtube_thumbViewer__scrollpane').css({height:'auto'});
                                            //sa.sp.containerSizeChanged(jQuery('#saApp_search_youtube_thumbViewer__scrollpane')[0], true);
                                            
                                            //setTimeout (function () { 
                                                jQuery('.saThumb').hover (function () {
                                                    if (r[this.id]) {
                                                        sa.apps.loaded.search_youtube.settings.resizing[this.id+'__mouseenter'] = true;
                                                        clearTimeout (sa.apps.loaded.search_youtube.settings.timeoutForDetails);
                                                        jQuery('#siteVideoSearch__searchResult__details').html(jQuery(this).attr('titleYoutube')).fadeIn(50);
                                                        
                                                        var l = this.offsetLeft;
                                                        if (l == 0) {                                            
                                                            jQuery(this).css({zIndex:99999999}).animate ({width:200,height:112}, {
                                                                done : function () {
                                                                    delete sa.apps.loaded.search_youtube.settings.resizing[this.id+'__mouseenter'];
                                                                }
                                                            });
                                                        } else if (l == 120) {
                                                            jQuery(this).css({zIndex:99999999}).animate ({left:jQuery(this).offset().left-jQuery('#siteVideoSearch').offset().left-50, width:200,height:112}, {
                                                                done : function () {
                                                                    delete sa.apps.loaded.search_youtube.settings.resizing[this.id+'__mouseenter'];
                                                                }
                                                            });
                                                        } else if (l == 240) { // 240
                                                            jQuery(this).css({zIndex:99999999}).animate ({left:jQuery(this).offset().left-jQuery('#siteVideoSearch').offset().left-100, width:200,height:112}, {
                                                                done : function () {
                                                                    delete sa.apps.loaded.search_youtube.settings.resizing[this.id+'__mouseenter'];
                                                                }
                                                            });
                                                        }
                                                    }
                                                }, function () {
                                                    if (r[this.id]) {
                                                        sa.apps.loaded.search_youtube.settings.resizing[this.id+'__mouseleave'] = true;
                                                        jQuery(this).css({zIndex:99999997}).animate ({left:r[this.id].left, width:100,height:56}, {
                                                            done : function () { 
                                                                delete sa.apps.loaded.search_youtube.settings.resizing[this.id+'__mouseleave'];
                                                            //	sa.apps.loaded.search_youtube.onresize();
                                                            }
                                                        });
                                                        sa.apps.loaded.search_youtube.settings.timeoutForDetails = setTimeout (function () {
                                                        jQuery('#siteVideoSearch__searchResult__details').fadeOut (400, function () {
                                                            jQuery('#siteVideoSearch__searchResult__details').html('');
                                                        });
                                                        }, 200);
                                                    }
                                                });
                                            //}, 200);
                                    }, 10);
                                }, 10);
                            }, 10);    
                        });
                    //}, 50);
                }, 10);
        }

	},
	
	
	thumbView : function (vid) {
		var 
		pw = window.parent.window,
		pd = window.parent.document,
		psc = pw.sa.s.c,
		psa = pw.sa,
		o = {
			layers : [
				{
					selectionEngine : 'url',
					url : 'https://www.youtube.com/embed/'+vid+'?autoplay=1&wmode=transparent&enablejsapi=1&html5=1&origin='+document.location.href
				}
			]
		};
		//sa.bg.next_do_step2 ('#siteBackground', o, sa.bg.settings.toUse);
		sa.site.code.selectMusic(o.layers[0].url, '#siteVideoIframe');
	}
};
