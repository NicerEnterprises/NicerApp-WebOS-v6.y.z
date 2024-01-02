import { naMisc, arrayRemove } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/na.miscellaneous.js';
import { naThemeEditor } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/na.themeEditor-2.y.z.js';
import { naLogo } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/na.canvasLogo-4.y.z.js';

import { vividUserInterface_2D_background } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/background.js';
import { vividUserInterface_2D_desktop } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/desktop.js';
//import { vividUserInterface_3D_button_startMenu_planet } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/3D/button_startMenu-planet.js';
import { vividUserInterface_2D_dialog } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/dialog.js';
import { vividUserInterface_2D_button } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/button-5.y.z.js';
import { vividUserInterface_2D_button_v4 } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/button-4.1.0.js';
import { vividUserInterface_2D_menu } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/menu.js';
import { naVividMenu__behavior_rainbowPanels as naVividMenu } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/2D/vividMenu-5.y.z--behavior-rainbowPanels-1.1.0.js';

export class naSite {
    about = {
        firstCreated : '10 January 2002',
        copyright : '<table style="height:100%;"><tr><td>Copyright (C) 2002-2023 by <a href="mailto:rene.veerman.netherlands@gmail.com" class ="contentSectionTitle3_a"><span class="contentSectionTitle3_span">Rene A.J.M. Veerman</span></a></td><td style="width:40px;"><div class="vividButton" theme="dark" style="position:relative;color:white;height:20px;width:40px;" onclick="na.dismissCopyrightMessage();">Ok</div></td></table>',
        easterEggs : {
            '2023-12(Dec)-13(Tue) 11:34CET (Amsterdam.NL\'s timezone)' : '<p>at a certain point in a soul\'s career,<br/>that soul (learns to) trancend(s) judgement of IQ and EQ of others.<br/>this is usually only once enough kung-fu has been practiced though.<br/><a class="directLink" href="https://youtube.com/@cheetahKungFu" target="ckf">https://youtube.com/@cheetahKungFu</a></p>'
        }
    };

    globals = { domain : '192.168.178.29' };
    settings = { heldUp : {}, menus : {}, buttons : {} };
    apps = {};
    m = new naMisc();
    d = { g : { margin : 8  }};

    constructor (desktopDefinition) {
        var t = this;

        t.s = t.settings;

        t.components = t.c = { dialogs : {}, buttons : {}, menus : {} };
        var c = t.components;

        c.background = new vividUserInterface_2D_background({naSite:t});
        t.backgrounds = c.background;



        $('#btnShowStartMenu').mouseenter (function(evt) {
            var evt2 = $.extend( {}, evt);
            evt2.currentTarget = $('#siteMenu__0')[0];
            na.settings.menus['#siteMenu'].currentEl = evt2.currentTarget;
            na.settings.menus['#siteMenu'].onmouseover (evt2);
        });


        /*
        c.taskbar = new vividUserInterface_2D_dialog({
            naSite : t,
            el : $('#siteTaskbar')
        });*/
        $('.vividButton4, .vividButton_icon_50x50').each(function(idx,el) {
            c.buttons['#'+el.id] = new vividUserInterface_2D_button ({ naSite : t, el : el });
        });
        $('.vividDialog').each(function(idx,el) {
            var d = new vividUserInterface_2D_dialog({ naSite : t, el : $(el) });
            switch (el.id) {
                case 'siteTaskbar' : c.taskbar = d; break;
                case 'siteSettingsMenu' : c.settingsMenu = d; break;
                default : c.dialogs['#'+el.id] = d; break;
            }
        });
        $('#btnSettings').hover (function() {
            var menu = na.settings.menus['#siteMenu'];
            menu.hideAll( menu, $('#siteMenu__panel__0')[0], 50 );
            $('#siteSettingsMenu').removeClass('hidden').addClass('shown');
        }, function () {
            c.settingsMenu.hide({ naSite : t, checkHeldUp : '#siteSettingsMenu' });
            //$('#siteSettingsMenu').removeClass('shown').addClass('hidden');
        });
        $('#siteSettingsMenu').hover(function() {
            t.settings.heldUp['#siteSettingsMenu'] = true;
        }, function(evt) {
            t.settings.heldUp['#siteSettingsMenu'] = false;
            c.settingsMenu.hide({ naSite : t, checkHeldUp : '#siteSettingsMenu' });
        });

        document.addEventListener ('keyup', function (e) {
            e.preventDefault();

            if (e.altKey && e.code=='KeyB') {
                na.backgrounds.next('#siteBackground');
                //debugger;
            };

        });

        setInterval (t.backgrounds.next, 60 * 1000);
        setInterval (t.updateSiteDatetime, 1000);
        setTimeout (function() {
            c.desktop = c.d = new vividUserInterface_2D_desktop(desktopDefinition);
            na.globals = $.extend (na.globals, naGlobals);
            t.te = new naThemeEditor();
            t.reloadMenu();
            t.updateSiteDatetime();
            t.ui = {
                vb : new vividUserInterface_2D_button_v4()
            };
            t.te.onload('siteContent');
            t.setSpecificity();
            t.loadTheme();
            t.startTooltips();
            $('.vividDialog > .vividDialogContent').fadeIn('normal');
            //debugger;
        }, 10);


        $('.lds-facebook').fadeOut('normal');

        console.log (this);
        return this;
    }

    loadModule(url, appName, jsClassName, jsVarName) {
        if (!appName.match(/[\w]+/)) {
            na.m.log (1, 'ERROR : na.loadModule() : appName parameter incorrect.');
            debugger;
            return false;
        }
        if (!jsClassName.match(/[\w_]+/)) {
            na.m.log (1, 'ERROR : na.loadModule() : jsClassName parameter incorrect.');
            debugger;
            return false;
        }
        if (!jsVarName.match(/[\w\.]*/)) {
            na.m.log (1, 'ERROR : na.loadModule() : jsVarName parameter incorrect.');
            debugger;
            return false;
        }
        if (
            !url.match ('https://'+na.globals.domain)
            && !url.match (/^\//)
        ) {
            na.m.log (1, 'ERROR : na.loadModule() : url parameter incorrect, for security reasons. url='+url);
            debugger;
            return false;
        };
        import (url).then((module) => {
            var js =
                'na.apps["'+appName+'"] = {'
                +'settings : new module.'+jsClassName+'('+jsVarName+')'
                +'}';
            eval (js);
        });
    }

    updateSiteDatetime () {
        var
        d = new Date(),
        r =
        d.getFullYear() + '-' + na.m.padNumber((d.getMonth()+1),2,'0') + '-' + na.m.padNumber(d.getDate(), 2, '0')
        + '(' + Date.locale.en.day_names_short[d.getDay()] + ')'
        + ' ' + na.m.padNumber(d.getHours(), 2, '0') + ':' + na.m.padNumber(d.getMinutes(), 2, '0')
        + ':' + na.m.padNumber(d.getSeconds(), 2, '0'), // + '+' + na.m.padNumber(d.getMilliseconds(), 3, 0);
        html =
            '<div class="datetime time animatedText-orangeYellow">'+na.m.padNumber(d.getHours(), 2, '0')
            + ':' + na.m.padNumber(d.getMinutes(), 2, '0')
            + ':' + na.m.padNumber(d.getSeconds(), 2, '0')+'</div>'
            + '<div class="datetime date animatedText-blue">'
            + d.getFullYear()
            + '-' + na.m.padNumber((d.getMonth()+1),2,'0')
            + '-' + na.m.padNumber(d.getDate(), 2, '0')
            + '(' + Date.locale.en.day_names_short[d.getDay()] + ')'
            +'</div>';
        $('#siteDatetime').html(html);
    }

    reloadMenu (settings) {
        // only drastically slows things down
        //na.desktop.resize(null, false);
        //na.onresize ({ reloadMenu:false });


        var t = this;


        //na.m.waitForCondition ('na.reloadMenu() : na.m.HTMLidle() && !na.settings.startingApps?', function() {
        na.m.waitForCondition ('na.reloadMenu() : na.m.HTMLidle()?', function() {
            var r =
                na.m.HTMLidle()
                //&& !na.settings.startingApps;
            return r;
        }, function() {
            var
            callback3x = (settings ? settings.callback : null),
            callback2b = function () {
                na.m.log (210, '<UL> & <LI> DATA LOADED FOR #siteMenu', false);
                na.m.log (210, 'STARTING TO RE-INITIALIZE #siteMenu', false);

                setTimeout (function() {
                    $('#siteMenu').css({zIndex:800*1000});
                    na.settings.menus['#siteMenu'] = new naVividMenu($('#siteMenu')[0], function(menu) {
                        $('#siteMenu').css ({
                            top : $(window).height()+100,
                            left : 10,
                        });
                        $('#siteMenu__0').css ({
                            opacity : 0.00001,
                            zIndex : -1,
                            bottom : -100
                        });

                        na.m.log (210, 'DONE RE-INITIALIZING #siteMenu', false);
                        var topLevelItemCount = $('.vividMenu_mainUL > li', menu).length;
                        //debugger;

                        if (settings) settings.naVividMenu_menuInitialized = menu;
                        if (typeof callback3x=='function') callback3x (settings);
                    });
                }, 50);
            };

            na.reloadMenu_reOrganise (callback2b);
        }, 50);
    }

    reloadMenu_reOrganise (callback4a) {

        if (!$('#siteMenu_vbChecker')[0]) $('#siteMenu').append('<div id="siteMenu_vbChecker" class="vividButton vividButton_text vividMenu_item" theme="'+$('#siteMenu').attr('theme')+'" style="opacity:0.0001;position:absolute;">abc XYZ</div>');

        var
        fncn = 'na.reloadMenu_forTheFirstTime(callback)',
        menuItemWidth = $('#siteMenu_vbChecker').outerWidth(),
        numRootItems = $('#siteMenu').width() / menuItemWidth,
        nri = Math.floor(numRootItems) > 2 ? Math.floor(numRootItems) : 1,
        mlp = '<li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>',
        contentMenu = $('#app_mainmenu li')[0] ? '<li class="contentMenu_populated">'+$('#app_mainmenu li')[0].innerHTML+'</li>' : '';

        var
        widest = { rootItems : 0, layout : null },
        hit = { rootItems : 0, layout : null };

        $('.vividMenu_layout').each (function(idx,layout) {
            var
            iw = parseInt($(layout).attr('itemsLevel1'));

            if (iw > widest.rootItems) widest = { rootItems : iw, layout : layout };
            if (iw === nri) hit = { rootItems : iw, layout : layout };
        });
        if (!hit.layout) hit = widest;

        var
        menu = $('#siteMenu'),
        items = $('.vividMenu_mainUL', menu),
        segs = $('.vividMenu_segments', menu);
        $('.vividMenu_item', menu).remove();

        items.html(hit.layout.innerHTML);
        $('.subMenu, .vividMenu_subMenuPanel', items).each(function (idx, subMenu) {
            var
            smID = '#subMenu__'+$(subMenu).attr('subMenuID');
            //smID = '.subMenu[submenuid="'+$(subMenu).attr('subMenuID')+'"]'; // only to be used when experiencing DNS problems
            items.html (
                items[0].innerHTML.replace( subMenu.outerHTML, $(smID)[0].outerHTML )
            );
        });

        var
        menu = items[0].innerHTML,
        p1 = menu.indexOf(mlp),
        mt = menu.substr(0,p1) + contentMenu + menu.substr(p1+mlp.length);

        items[0].innerHTML = mt;
        var il1 = parseInt($('#siteMenu ul').attr('itemslevel1'));
        //if (mt.indexOf('-contentMenu-')===-1) $('#siteMenu ul').attr('itemslevel1', ''+(il1-1));
        //debugger;

        na.transformLinks ($('#siteMenu_items')[0]);
        if (typeof callback4a=='function') callback4a ( menu );
    }

    transformLinks (rootElement) {
        //if (!na.globals.useLoadContent) return false;
        $('a', rootElement).not('.contentMenu, .noPushState, .hmNavE').each(function(idx, el){

            let x = el.href, y = el.target;

            if (
                el.href.match(document.location.origin)
                && !el.href.match(document.location.origin+'\/#')
            ) {
                let h = "javascript:na.loadContent(event,'"+el.href.replace(document.location.origin,'').replace('/view/','')+"');";
                //el.href = '#';
                $(el).attr('onclick', h);
                $(el).attr('targetDisabled',$(el).attr('target'));
                $(el).attr('target','');

            }
        });
    }

    startTooltips (evt, rootEl) {
        if (!rootEl) rootEl = document;
        $('.tooltip', rootEl).each (function(idx,el) {
            var theme = $(el).attr('tooltipTheme');
            if (!theme) theme = 'mainTooltipTheme';
            /*if (el.id=='btnLoginLogout' && parseInt($.cookie('haveShownTutorial'))<3) {
                na.settings.btnLoginLogout = this;
                var ptSettings = {
                    theme : theme,
                    contentAsHTML : true,
                    content : $(el).attr('title'),
                    animation : 'grow',
                    alignTo : 'target',
                    alignX : 'inner-left',
                    offsetX : 10,
                    offsetY : 10,
                    fade : !na.m.userDevice.isPhone,
                    slide : !na.m.userDevice.isPhone,
                    slideOffset : 25
                };
                if (na.m.userDevice.isPhone) ptSettings.showOn = 'none';
                if (ptSettings.content!=='') {
                    $(el).tooltipster(ptSettings);
                    $(el).tooltipster('show');
                    $(el).tooltipster('hide');
                    $(el).addClass('started');
                    setTimeout (function() {
                        $(na.settings.btnLoginLogout).tooltipster('show');
                        setTimeout(function() {
                            $(na.settings.btnLoginLogout).tooltipster('hide');
                        }, 2000);
                    }, 500);
                }

            } else */

            if (el.id=='btnChangeBackground' /*&& parseInt($.cookie('haveShownTutorial'))<3*/) {
                na.settings.btnChangeBackground = el;
                try {
                    var html = $($(el).attr('title'));
                } catch (error) {
                    var html = $(el).attr('title');
                }
                debugger;
                var
                ptSettings = {
                    theme : theme,
                    contentAsHTML : true,
                    content : html,
                    animation : 'grow',
                    alignTo : 'target',
                    alignX : 'inner-right',
                    offsetX : -20,
                    offsetY : 10,
                    fade : !na.m.userDevice.isPhone,
                    slide : !na.m.userDevice.isPhone,
                    slideOffset : 25
                };
                if (na.m.userDevice.isPhone) ptSettings.showOn = 'none';
                if (ptSettings.content!=='') {
                    $(el).tooltipster(ptSettings);
                    setTimeout (function() {
                        $(el).tooltipster('show');
                        $('.mainTooltipTheme').css ({opacity:0.001});
                        setTimeout (function() {
                            $(el).tooltipster('hide');
                        }, 200);
                    }, 600);
                    $(el).addClass('started');
                    setTimeout (function() {
                        $(na.settings.btnChangeBackground).tooltipster('show');
                        setTimeout(function() {
                            $(na.settings.btnChangeBackground).tooltipster('hide');
                        }, 2000);
                        if (na.m.userDevice.isPhone) $('.mainTooltipTheme').css({left:$('.mainTooltipTheme').offset().left-20});
                        $('.tip-arrow').css({left:$(el).offset().left-$('.mainTooltipTheme').offset().left});
                    }, 1000);
                };
            } else /*if (
                el.id!=='btnChangeBackground'
                && el.id!=='btnLoginLogout'
            ) */{
                try {
                    var html = $($(el).attr('title'));
                } catch (error) {
                    var html = $(el).attr('title');
                }
                var
                ptSettings = {
                    theme : theme,
                    contentAsHTML : true,
                    content : html
                };
                if (na.m.userDevice.isPhone) ptSettings.showOn = 'none';
                if (ptSettings.content!=='') $(el).tooltipster(ptSettings);
            }
            //console.log ('startTooltips : el.id=='+el.id+', cookie::haveShownTutorial='+$.cookie('haveShownTutorial'));
            $(el).attr('title','');
        });
        $.cookie('haveShownTutorial', parseInt($.cookie('haveShownTutorial'))+1, na.m.cookieOptions());
    }

    setSpecificity (simple) {
        $('.na_themes_dropdown').html('<div class="vividDropDownBox_selected vividScrollpane" style="white-space:normal;"></div><div class="vividDropDownBox_selector"><div class="vividScrollpane" style="padding:0px;"></div></div>').delay(50);
        $('.vividDropDownBox_selected, .vividDropDownBox_selector').each(function(idx,el) {
            var w = 0;
            $('.vividButton4, .vividButton, .vividButton_icon_50x50', $(el).parent().parent() ).each(function(idx2, el2) {
                w += $(el2).width();
                $(el2).css ({display : 'inline-block', position:'relative'});
            });
            var w3 = $(this).parent().parent().width();
            var w2 = w3
                    - $('.siteToolbarThemeEditor__label__specificity, .siteToolbarThemeEditor__label__themes, .btnOptions_menu__label__specificity_dropdown, .btnOptions_menu__label__themes_dropdown',
                        $(this).parent().parent()
                    ).width()
                    - w
                    - 20;

            $(this).css({
                width: w2,
                height : 'auto'
            });

        });
        for (var i in na.globals.themesDBkeys) {
            if (
                na.globals.themesDBkeys[i].display===false
                || !na.globals.themesDBkeys[i].has_write_permission
            ) continue;
            var l = i;
        }
        for (var i in na.globals.themesDBkeys) {
            if (
                na.globals.themesDBkeys[i].display===false
                || !na.globals.themesDBkeys[i].has_write_permission
            ) continue;

            var
            divEl = document.createElement('div');
            //l = Object.keys(na.globals.themesDBkeys).length - 1;

            $(divEl).html(na.globals.themesDBkeys[i].specificityName).attr('value',i);
            //if (!na.globals.themesDBkeys[i].hasWritePermission) $(divEl).addClass('disabled');

            var
            b = na.ui.vb.settings.buttons['#btnLockSpecificity'],
            selectMe = (
                simple
                    ? (
                        na.globals.themeSpecificityName === na.globals.themesDBkeys[i].specificityName
                        || na.globals.specificityName === na.globals.themesDBkeys[i].specificityName
                    )
                    : b && b.state == b.btnCode.selectedState
                        ? (
                            na.globals.themeSpecificityName === na.globals.themesDBkeys[i].specificityName
                            || na.globals.specificityName === na.globals.themesDBkeys[i].specificityName
                        )
                        : i == l
            );

            if (selectMe) {
                //debugger;
                $(divEl).addClass('selected');
                //$('.na_themes_dropdown__specificity > .vividDropDownBox_selected').html (na.globals.specificityName);
                na.globals.themeDBkeys = na.globals.themesDBkeys[i];
                //na.loadTheme_applySettings (na.globals.themes[na.globals.themeName]);
                $('.na_themes_dropdown__specificity > .vividDropDownBox_selected').html (na.globals.themeDBkeys.specificityName);
                na.te.settings.specificity = na.globals.themeDBkeys;
            };

            //debugger;
            $('.na_themes_dropdown__specificity > .vividDropDownBox_selector > .vividScrollpane').append($(divEl).clone(true,true));
        };

        na.te.s.selectedThemeName = na.globals.themeName;
        for (var themeName in na.globals.themes) {
            var theme = na.globals.themes[themeName];
            for (var i in na.globals.themesDBkeys) {
                var it = na.globals.themesDBkeys[i];
                if (
                    it.user === theme.user
                    || it.role === theme.role
                    || it.url === theme.url
                    || it.view === theme.view
                    || it.specificityName === theme.specificityName
                ) {
                    var divEl2 = document.createElement('div');
                    $(divEl2).html(themeName).attr('value',i);

                    if (themeName==na.globals.themeName) {
                        $(divEl2).addClass('selected');
                        $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html(themeName);
                    }
                    $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane').append($(divEl2).clone(true,true));
                    break;
                }
            };
        }
        $('.na_themes_dropdown__specificity').hover(function() {
            clearTimeout(na.settings.timeout_onmouseover_specificity);
            na.settings.timeout_onmouseover_specificity = setTimeout(function() {
                $('.na_themes_dropdown__specificity > .vividDropDownBox_selector').fadeIn('normal');
                $('#btnDeleteSpecificity').css({alignSelf:'start'});
            }, 700);
        }, function() {
            clearTimeout(na.settings.timeout_onmouseover_specificity);
            clearTimeout(na.settings.timeout_onmouseout_specificity);
            na.settings.timeout_onmouseout_specificity = setTimeout(function() {
                $('.na_themes_dropdown__specificity > .vividDropDownBox_selector').fadeOut('normal');
                $('#btnDeleteSpecificity').css({display:'block'});
            }, 700);

        });
        $('.na_themes_dropdown__specificity > .vividDropDownBox_selector').mouseover(function() {
            clearTimeout(na.settings.timeout_onmouseout_specificity);
        });

        $('.na_themes_dropdown__specificity > .vividDropDownBox_selector > .vividScrollpane > div').click(function(evt) {
            na.globals.specificityName = $(this).html();
            //debugger;
            $('.na_themes_dropdown__specificity > .vividDropDownBox_selected').html($(this).html());
            $('.na_themes_dropdown__specificity > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
            $(this).addClass('selected');
            na.te.specificitySelected(evt);
        });

        //$('#siteToolbarThemeEditor .vividScrollpane').not('.vividDropDownBox_selected').css({ overflow : 'visible' });

        //if (!na.m.desktopIdle()) {
            na.te.s.c.selectedThemeName = na.globals.themeName;
            //$('.themeItem').each(function(idx,ti) {
            $('.themeItem').removeClass('onfocus');

/*
                $(ti).removeClass('onfocus');
                if ($(ti).val()==na.te.s.c.selectedThemeName) {
                    $(ti).addClass('onfocus');
                }
*/

            $('.na_themes_dropdown__themes').hover(function() {
                clearTimeout(na.settings.timeout_onmouseout_themes);
                $('.na_themes_dropdown__themes > .vividDropDownBox_selector').fadeIn('normal');
            }, function() {
                clearTimeout(na.settings.timeout_onmouseout_themes);
                na.settings.timeout_onmouseout_themes = setTimeout (function() {
                    $('.na_themes_dropdown__themes > .vividDropDownBox_selector').fadeOut('normal');
                }, 500);
            });
            $('.na_themes_dropdown__themes > .vividDropDownBox_selector').mouseover(function() {
                clearTimeout(na.settings.timeout_onmouseout_themes);
            });
            $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane > div').click(function(evt) {
                $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html($(this).html());
                $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane > div').removeClass('selected');
                $(this).addClass('selected');
                na.te.themeSelected(evt);
            });





            $('#nb_url1_dropdown').html('<div id="nb_url1_dropdown_selected" class="vividDropDownBox_selected"></div><div id="nb_url1_dropdown_selector" class="vividDropDownBox_selector"></div>');
            $('#url1_dropdown').html('<div id="url1_dropdown_selected" class="vividDropDownBox_selected"></div><div id="url1_dropdown_selector" class="vividDropDownBox_selector"></div>');

            var optEls = $('#nb_url1_select option');
            optEls.each(function(idx,el) {
                var optEl2 = document.createElement('option');
                optEl2.value = JSON.stringify(it);
                optEl2.innerHTML = el.innerHTML;
                if (optEl2.innerHTML==$('.themeItem.onfocus').val()) {
                    $(optEl2)[0].selected = true;
                    $(optEl2).addClass('onfocus');
                };

                var divEl2 = document.createElement('div');
                $(divEl2).html(el.innerHTML);

                //$('#url1_select')[0].appendChild(optEl2);
                $('#nb_url1_dropdown_selector, url1_dropdown_selector').append(divEl2);
                if ($(optEl2)[0].selected) $('#nb_url1_dropdown_selected, #url1_dropdown_selected').html(el.innerHTML);
            });

            $('#url0').html('/'+$.cookie('cdb_loginName').replace(/.*___/g,'').replace(/__/g,'-')+'/');

            $('#nb_url1_dropdown, #url1_dropdown').hover(function() {
                $('#nb_url1_dropdown_selector, #url1_dropdown_selector').fadeIn('normal');
            }, function() {
                $('#nb_url1_dropdown_selector, #url1_dropdown_selector').fadeOut('normal');
            });

            $('#nb_url1_dropdown_selector > div, #url1_dropdown_selector > div').click(function(evt) {
                $('#nb_url1_dropdown_selected, #url1_dropdown_selected').html($(this).html());
                $('#nb_url1_dropdown_selector > div, #url1_dropdown_selector > div').removeClass('selected');
                $(this).add($('#'+$(this).parent()[0].id.replace('nb_',''))).addClass('selected');
                $('#nb_url2_value').css({width:120});
                na.blog.onchange_documentHeaders(evt,na.blog.onresize);
            });






            //});
        /*} /*else {
            $('#themes option').each(function(idx,optEl){
                $('#themeChange_themeName').html('');
                for (var i=0; i<na.globals.themesDBkeys.length; i++) {
                    var optEl2 = document.createElement('option');
                    //debugger;
                    optEl2.value = JSON.stringify(na.globals.themesDBkeys[i]);
                    optEl2.innerHTML = $(optEl).html();
                    if (optEl.innerHTML==$('.themeItem.onfocus').val()) {
                        $(optEl2)[0].selected = true;
                    };
                    $('#themeChange_themeName')[0].appendChild(optEl2);
                };

                optEl.selected=false;
                if (optEl.innerHTML==$('.themeItem.onfocus').val()) {
                    optEl.selected=true;
                }
            });

        }*/

        //na.setSiteLoginLogout();
    }

    loadTheme (callback, theme, doGetPageSpecificSettings, doSwitchSpecificities, specificityName, loadBackground, includeClientOnlyThemes, preserveCurrentTheme, stickToCurrentSpecificity) {
        var
        fncn = 'na.loadTheme(callback,theme)',
        s = na.te.settings.specificity,
        u = na.settings.url,
        apps = na.globals.app;

        if (typeof specificityName=='undefined') specificityName = na.globals.specificityName;
        if (typeof preserveCurrentTheme=='undefined') preserveCurrentTheme = true;

        for (var app in apps) break;

        na.settings.running_loadTheme = true;

        na.m.log (10, 'na.loadTheme() : STARTING.', false);

        if (
            !theme
            || typeof theme=='number' // when called via na.loadContent()
        ) theme = na.globals.themeName;

        na.te.settings.selectedThemeName = theme;

        // maybe use the immediately following line instead, depends on permissions checking in /NicerAppWebOS/logic.AJAX/ajax_database_loadTheme.php
        //if (!s) var s = { url : '[default]', role : 'guests', user : 'Guest' };

        //if (!s) var s = { url : '[default]' };
        if (doGetPageSpecificSettings) {

            if (preserveCurrentTheme) {
                var ct = na.globals.themes[theme];
            } else {
                var ct = null;
            }
            debugger;
            na.loadTheme_doGetPageSpecificSettings (function() {

                na.loadTheme_do (callback, specificityName, theme, loadBackground);

            }, doSwitchSpecificities, includeClientOnlyThemes, specificityName, theme, ct, stickToCurrentSpecificity);

        } else {
            na.loadTheme_do (callback, specificityName, theme, loadBackground);
        };
    }
    loadTheme_initializeExtras () {
        // gets called at the end of a chain started by onload_phase2()

/*
        if (
            na.globals.themes.default.themeSettings
            && (
                typeof na.globals.themes.default.themeSettings['Extras']!=='object'
                || typeof na.globals.themes.default.themeSettings['Extras'].length==='number'
            )
        ) {
*/


                        /*
            for (var themeID in na.globals.themes) break;
            na.globals.themes.default.themeSettings.Extras = {
                'texts' : {
                    'css' : {
                        '#siteContent > .vividDialogContent > li > a, p:not(.backdropped, .vividTextCSS), h1:not(.backdropped, .vividTextCSS), h2:not(.backdropped, .vividTextCSS), h3:not(.backdropped, .vividTextCSS)' : { opacity : // CULPRIT for blogging app na.globals.themes[themeID].textBackgroundOpacity, backgroundClip:'text' },
                        '#siteContent .newsApp__item__outer p' : { opacity : 1, backgroundClip:'none' }
                    }
                },
                'menus' : {
                    'css' : {
                        '.vividMenu_item' : { opacity : 1 }
                    }
                }
            };
  //      };
                        */

    }
    loadTheme_doGetPageSpecificSettings (callback, doSwitchSpecificities, includeClientOnlyThemes, specificityName, theme, ct, stickToCurrentSpecificity) {
        if (typeof includeClientOnlyThemes=='undefined') includeClientOnlyThemes = true;
        if (typeof stickToCurrentSpecificity=='undefined') stickToCurrentSpecificity = true;

        var
        state = History.getState(),
        url = state.url.replace(document.location.origin,'').replace('/view/', ''),
        url2 = url.replace(document.location.origin,'').replace(document.location.host,'').replace('/view/', ''),
        url3 = '/NicerAppWebOS/logic.AJAX/ajax_get_pageSpecificSettings.php',
        ac2 = {
            type : 'GET',
            url : url3,
            data : {
                viewID : na.m.base64_encode_url(JSON.stringify(na.globals.app)),// url2
                includeClientOnlyThemes : includeClientOnlyThemes || na.globals.specificityName.match(' client')?'true':'false',
                stickToCurrentSpecificity : stickToCurrentSpecificity,
                specificityName : na.te.s.c.specificity.specificityName,
                c : na.m.changedDateTime_current()
            },
            success : function (data2, ts2, xhr2) {
                if (
                    typeof data2 == 'string'
                    && !data2.match(/E_FATAL/)
                    && !data2.match(/E_WARNING/)
                    && !data2.match(/E_NOTICE/)
                ) {
                    $('#cssPageSpecific, #jsPageSpecific').remove();
                    $('head').append(data2).delay(100);
                }
                if (doSwitchSpecificities) {
                    debugger;
                    if (ct && !na.globals.themes[ct.theme]) {
                        na.globals.themes[ct.theme] = ct;
                        na.globals.themeName = ct.theme;
                    } else {
                        if (theme) na.globals.themeName = theme;
                    }
                    na.setSpecificity(true);
                }
                setTimeout(function () {
                    if (typeof callback=='function') callback(true);
                }, 50);
            },
            error : function (xhr, textStatus, errorThrown) {
                na.ajaxFail(fncn, url3, xhr, textStatus, errorThrown);
            }
        };
        //setTimeout (function() {
            debugger;
            $.ajax(ac2);
        //}, 250);
    }

    loadTheme_do (callback, specificityName, theme, loadBackground) {
        var
        fncn = 'na.loadTheme_do(callback,theme)',
        s = na.te.settings.specificity,
        u = na.settings.url,
        apps = na.globals.app,
        acData = {
            orientation : na.settings.orientation//,
            //theme : theme//,
            //dialogs : JSON.stringify (na.desktop.settings.visibleDivs)
        };
        if (
            typeof specificityName=='undefined'
            || specificityName===null
        ) specificityName = na.globals.specificityName;
        if (typeof apps=='object')
            for (var app in apps) break;
        else app = apps;
        //if (app) acData.app = app;


        if (s) {
            if (s.view) acData.view = s.view;
            if (s.role) acData.role = s.role;
            if (s.user) acData.user = s.user;
            //if (s.specificityName) acData.specificityName = s.specificityName;
            acData.specificityName = specificityName;
            if (specificityName.match('current page')) {
                if (u) acData.url = u;
                if (s.url) acData.url = s.url;
                if (!acData.url) acData.url = window.location.href.replace('https://'+na.globals.domain,'');
            }
            if (specificityName.match('app \'')) {
                if (app) acData.app = app;
            }
            if (specificityName.match(/^site /)) {
                delete acData.view;
                delete acData.app;
                delete acData.url;
            }
        } else debugger;

        var
        url = '/NicerAppWebOS/logic.AJAX/ajax_database_loadTheme.php',
        ac = {
            type : 'POST',
            url : url,
            data : acData,
            success : function (data, ts, xhr) {
                // reload #cssPageSpecific and #jsPageSpecific
                if (data=='status : Failed.') {
                    na.m.log (10, 'na.loadTheme() : FAILED (HTTP SUCCESS, but no theme was found)');
                    na.loadTheme_applySettings (na.globals.themes[na.globals.themeName]);
                    if (typeof callback=='function') callback(true);
                    return false;
                } else if (data==='') {
                    na.m.log (10, 'na.loadTheme() : FAILED (HTTP SUCCESS, but no data returned at all)');
                    na.loadTheme_applySettings (na.globals.themes[na.globals.themeName]);
                    na.loadTheme_initializeExtras();
                    if (typeof callback=='function') callback(true);
                    return false;
                }
                try {
                    var themes = JSON.parse(data);
                } catch (error) {
                    na.m.log (10, 'na.loadTheme() : FAILED (could not decode JSON data - '+error.message+')+');
                    na.loadTheme_applySettings (na.globals.themes[na.globals.themeName]);
                    if (typeof callback=='function') callback(true);

                    // only significantly slows down startup for new viewers :
                    //na.fail (fncn+' : AJAX decode error in data returned for url='+url+', error='+error.message+', in data='+data, xhr, function () {
                    //    na.error (data);
                    //});
                    return false;
                }
                //na.globals.themes = themes;
                //na.settings.theme = themes[theme];

                /*
                var html = ''; idx = 0;
                for (var themeName in themes) {
                    var dit = themes[themeName];

                    if (themeName==theme) {
                        html += '<div id="theme_'+idx+'" class="selected onfocus">'+themeName+'</div>';
                    } else {
                        html += '<div id="theme_'+idx+'">'+themeName+'</div>';
                    }
                }
                $('.na_themes_dropdown__themes > .vividDropDownBox_selector > .vividScrollpane').html(html);
                debugger;
                $('.na_themes_dropdown__themes > .vividDropDownBox_selected').html(theme);
                */

                var dat = themes[theme];
                /*
                for (var themeName in themes) {
                    var dat = themes[themeName];
                    na.settings.theme = dat;
                    break;
                };*/
                //na.setSpecificity (true);
                na.loadTheme_applySettings (dat, callback, loadBackground);
                //na.te.onload('siteContent');
            },
            error : function (xhr, textStatus, errorThrown) {
                debugger;
                //only significantly slows down startup for new viewers :
                //na.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }
        };
        $.ajax(ac);
    }

    loadTheme_applySettings (dat, callback, loadBackground) {
        if (!dat) {
            na.m.log (1, 'Error : loadTheme_applySettings() called with dat=undefined/false', false);
            return false;
        };
        if (typeof loadBackground=='undefined') loadBackground = true;
        //if (dat.specificityName) {
            $('.na_themes_dropdown__specificity > .vividDropDownBox_selector > div')
                .removeClass('selected')
                .each (function(idx,el) {
                    /*if (el.innerHTML === dat.specificityName) {
                        $(el).addClass('selected');
                        na.te.settings.specificity = na.globals.themesDBkeys[$(el).attr('value')];
                    };*/
                    /*
                    var l = Object.keys(na.globals.themesDBkeys).length - 1;
                    if (el.innerHTML === na.globals.themesDBkeys[l].specificityName) {
                        $(el).parent().find('.vividDropDownBox_selected').html(el.innerHTML);
                        $(el).addClass('selected');
                        na.globals.specificityName = el.innerHTML;
                        na.te.settings.specificity = na.globals.themesDBkeys[$(el).attr('value')];
                    };
                    */

                });
        //};

        if (dat.menusFadingSpeed) {
            $('#menusFadingSpeed').val(dat.menusFadingSpeed);
            for (var menuID in na.settings.menus) {
                var m = na.settings.menus[menuID];
                m.fadingSpeed = parseInt(dat.menusFadingSpeed);
            }
        }

        /*
        $('#menusUseRainbowPanels')[0].checked = dat.menusUseRainbowPanels !== 'false';
        if (dat.menusUseRainbowPanels) {
            for (var menuID in na.settings.menus) {
                var m = na.settings.menus[menuID];
                m.percentageFor_rainbowPanels = dat.menusUseRainbowPanels === 'false' ? 0 : 100;
            }

        }*/

        if (loadBackground && dat.background /*&& dat.background!==na.globals.background*/) { /* doesn't jive with na.loadContent() */
            na.backgrounds.next (
                '#siteBackground',
                na.globals.backgroundSearchKey,
                dat.background,
                false
            );
        } else if (loadBackground && !dat.background /*&& dat.background!==na.globals.background*/) { /* doesn't jive with na.loadContent() */
            na.backgrounds.next (
                '#siteBackground',
                na.globals.backgroundSearchKey,
                na.globals.background,
                false
            );
        }

        clearInterval (na.settings.backgroundChangeInterval);
        if (dat.changeBackgroundsAutomatically=='true') {
    //$('#changeBackgroundsAutomatically')[0].checked = true;
            /*
            var m = $('#backgroundChange_minutes').val();
            var h = $('#backgroundChange_hours').val();
            var ms = ((h * 60)+1) * (m * 60) * 1000;
            na.settings.backgroundChangeInterval = setInterval (function() {
                na.backgrounds.next (
                    '#siteBackground',
                    na.globals.backgroundSearchKey,
                    null,
                    true
                );
            }, ms);
            */
        }
        if (dat.backgroundChange_hours) $('#backgroundChange_hours').val(dat.backgroundChange_hours);
        if (dat.backgroundChange_minutes) $('#backgroundChange_minutes').val(dat.backgroundChange_minutes);
        if (dat.vdSettings_show) $('#vdSettings_show').val(dat.vdSettings_show);
        else $('#vdSettings_show').val('transparent');

        var opacity = (
            $('#vdSettings_show').val()=='hidden'
            ? 0.000001
            : $('#vdSettings_show').val()=='transparent'
                ? 0.5
                : 1
        );
        setTimeout (function() {
            $('.vdSettings').delay(50).css({ opacity : opacity });
        }, 1500);

        var
        h = parseInt($('#backgroundChange_hours').val()),
        m = parseInt($('#backgroundChange_minutes').val()),
        ms = (
            ( h > 0 ? (h * 60) : 1) // 60 minutes in an hour
            * (m > 0 ? (m * 60) : 1) // 60 seconds in a minute
            * 1000 // 1000 milliseconds in a second
        );
        clearInterval (na.settings.backgroundChangeInterval);
        //if ($('#changeBackgroundsAutomatically')[0].checked)
        if (true)
        na.settings.backgroundChangeInterval = setInterval (function() {
            na.backgrounds.next (
                '#siteBackground',
                na.globals.backgroundSearchKey,
                null,
                true,
                "na.settings.backgroundChangeInterval() : this website's backgroundChangeInterval is currently turned on to occur every "+(ms/1000)+" seconds."
            );
        }, ms);

        if (dat.textBackgroundOpacity) {
            na.te.s.c.textBackgroundOpacity = dat.textBackgroundOpacity;
            $('#btnOptions_menu input.sliderOpacityRange').val(dat.textBackgroundOpacity * 100);
            /*
            $('li span, p, h1, h2, h3').css({
                background : 'rgba(0,0,0,'+dat.textBackgroundOpacity+')'
            });
            */
//debugger;
            $('#siteContent > .vividDialogContent > li > a, p, h1, h2, h3').not('.naVividTextCSS, .contentSectionTitle1, .contentSectionTitle1_a, .contentSectionTitle1_span, .contentSectionTitle2, .contentSectionTitle2_a, .contentSectionTitle2_span, .contentSectionTitle3, .contentSectionTitle3_a, .contentSectionTitle3_span, .backdropped').each (function(idx,el) {
                var bg = na.m.adjustColorOpacity(el, dat.textBackgroundOpacity);
                if (bg) $(el).css({background:bg});
            });
        }
        if (dat.themeSettings && dat.themeSettings['.vividDialog']) {
            $('.vividDialog').css(dat.themeSettings['.vividDialog']);
            $('.vividDialog > .vividDialogBackground1').css(dat.themeSettings['.vividDialog > .vividDialogBackground1']);
        };
        if (dat.themeSettings)
        for (var category in dat.themeSettings) {
            if (
                dID=='.vividDialog'
                || dID=='.vividDialog > .vividDialogBackground1'
                || dID=='.vividDialogBackground1'
            ) continue;
            var categoryItems = dat.themeSettings[category];
            switch (category) {
                case 'Dialogs' :
                    for (var dID in categoryItems) {
                        var dit = categoryItems[dID].css;
                        for (var divSel in dit) {
                            var dit2 = dit[divSel];
                            if (divSel=='#siteToolbarThemeEditor') dit2.opacity = 1; // dirty hack
                            $(divSel).css (dit2);
                            if (dit2.background && dID == '#'+na.te.settings.forDialogID+' > .vividDialogBackground1') {
                                var
                                del = $(dID)[0],
                                rgbaRegEx = /rgba\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\,\s*([\d\.]+)\).*/,
                                test = rgbaRegEx.test(dit.background),
                                ditbgOpacity = test ? dit.background.match(rgbaRegEx)[1] : dit.opacity;
                                $('.sliderOpacityRange', del).attr('value', ditbgOpacity*100);
                                if (test && na.te.settings.selectedButtonID == 'btnSelectBackgroundColor') {
                                    $('#colorpicker').css({display:'block'}).spectrum ({
                                        color:dit.background,
                                        type:'flat',
                                        clickoutFiresChange : false,
                                        change : function (color) {
                                            var bg = $('.vividDialogBackground1', $('#'+na.te.settings.forDialogID)[0]);
                                            $(bg).css({ background : color, opacity : 1 });
                                            na.te.settings.fireSaveTheme = true;
                                            na.saveTheme();
                                        }
                                    }).css({display:'none'});
                                }
                            }
                        }
                    }
                    break;
                case 'Apps' :
                    for (var appName in categoryItems) {
                        var appItem = categoryItems[appName].css;
                        for (var divSel in appItem) {
                            var dit = appItem[divSel];

                            /* messes up theme loading :
                            for (var prop in dit) {
                                var v = dit[prop];
                                if (typeof v=='string' && !v.match(/\!important/)) {
                                    dit[prop] = dit[prop].replace(';','').replace(/\s\s/g,' ').trim();
                                    dit[prop] += ' !important';
                                }
                            }
                            */

                            $(divSel).css(dit);

                            /*
                            if (dit.background && dID == '#'+na.te.settings.forDialogID+' > .vividDialogBackground1') {
                                var
                                del = $(dID)[0],
                                rgbaRegEx = /rgba\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\,\s*([\d\.]+)\).* /,
                                test = rgbaRegEx.test(dit.background),
                                ditbgOpacity = test ? dit.background.match(rgbaRegEx)[1] : dit.opacity;
                                $('.sliderOpacityRange', del).attr('value', ditbgOpacity*100);
                                if (test && na.te.settings.selectedButtonID == 'btnSelectBackgroundColor') {
                                    $('#colorpicker').css({display:'block'}).spectrum ({
                                        color:dit.background,
                                        type:'flat',
                                        clickoutFiresChange : false,
                                        change : function (color) {
                                            var bg = $('.vividDialogBackground1', $('#'+na.te.settings.forDialogID)[0]);
                                            $(bg).css({ background : color, opacity : 1 });
                                            na.te.settings.fireSaveTheme = true;
                                            na.saveTheme();
                                        }
                                    }).css({display:'none'});
                                }
                            }*/
                        }
                    }
                    break;
                case 'Extras' :
                    for (var btnAddGraphics_jsTreeText in categoryItems) {
                        var it = categoryItems[btnAddGraphics_jsTreeText].css;
                        for (var divSel in it) {
                            var dit = it[divSel];

                            /* messes up theme loading :
                            for (var prop in dit) {
                                var v = dit[prop];
                                if (typeof v=='string' && !v.match(/\!important/)) {
                                    dit[prop] = dit[prop].replace(';','').replace(/\s\s/g,' ').trim();
                                    dit[prop] += ' !important';
                                }
                            }
                            */

                            $(divSel).css(dit);
                            /*
                            if (dit.background && dID == '#'+na.te.settings.forDialogID+' > .vividDialogBackground1') {
                                var
                                del = $(dID)[0],
                                rgbaRegEx = /rgba\(\d{1,3}\,\s*\d{1,3}\,\s*\d{1,3}\,\s*([\d\.]+)\).* /,
                                test = rgbaRegEx.test(dit.background),
                                ditbgOpacity = test ? dit.background.match(rgbaRegEx)[1] : dit.opacity;
                                $('.sliderOpacityRange', del).attr('value', ditbgOpacity*100);
                                if (test && na.te.settings.selectedButtonID == 'btnSelectBackgroundColor') {
                                    $('#colorpicker').css({display:'block'}).spectrum ({
                                        color:dit.background,
                                        type:'flat',
                                        clickoutFiresChange : false,
                                        change : function (color) {
                                            var bg = $('.vividDialogBackground1', $('#'+na.te.settings.forDialogID)[0]);
                                            $(bg).css({ background : color, opacity : 1 });
                                            na.te.settings.fireSaveTheme = true;
                                            na.saveTheme();
                                        }
                                    }).css({display:'none'});
                                }
                            }*/
                        }
                    }
                    break;
            }
        };

        na.m.log (10, 'na.loadTheme_applySettings() : FINISHED.', false);
        na.settings.running_loadTheme = false;
        if (typeof callback=='function') callback(true);
    }

    saveTheme (callback, theme, loadBackground) {
        var
        fncn = 'na.saveTheme(callback,theme)',
        s = na.te.settings.specificity,
        u = na.settings.url,
        apps = na.globals.app;

        if (!na.te.s.c.forDialogID && !na.te.s.c.forElements) na.te.onload();

        //if (!theme) theme = na.globals.themeName;
        na.settings.running_saveTheme = true;

        if (typeof apps=='object')
            for (var app in apps) break;
        else var app = apps;

        na.m.log (10, 'na.saveTheme() : STARTING.', false);

        if (!s) return false;
        if (!theme) theme = $('.na_themes_dropdown__themes > .vividDropDownBox_selected > .vividScrollpane').html();

        clearTimeout (na.settings.saveThemeTimeout);
        na.settings.saveThemeTimeout = setTimeout(function() {
            var tApp = null;
            if (
                na.globals.themes
                && na.globals.themes[theme]
                && na.globals.themes[theme].apps
            ) tApp = na.globals.themes[theme].apps;

            var
            themeData = {
                specificityName : $('.na_themes_dropdown__specificity > .vividDropDownBox_selected').html(),
                theme : theme,
                orientation : na.settings.orientation,
                backgroundSearchKey : na.globals.backgroundSearchKey,
                background : na.globals.background,
                changeBackgroundsAutomatically : 'true',//$('#changeBackgroundsAutomatically')[0].checked?'true':'false',
                vdSettings_show : 'hidden',//$('#vdSettings_show').val(),
                backgroundChange_hours : 0,//$('#backgroundChange_hours').val(),
                backgroundChange_minutes : 1,//$('#backgroundChange_minutes').val(),
                menusFadingSpeed : 500,//$('#menusFadingSpeed').val(),
                menusUseRainbowPanels : 'true',//$('#menusUseRainbowPanels')[0].checked ? 'true' : 'false',
                dialogs : {},
                apps : tApp,
                //view : na.settings.app,
                textBackgroundOpacity : 0.4//parseInt($('#textBackgroundOpacity').val()) / 100
            };

            if (s.view) themeData.view = s.view; //else if (s.url) themeData.url = s.url;
            if (s.role) themeData.role = s.role;
            if (s.user) themeData.user = s.user;
            if (s.specificityName) themeData.specificityName = s.specificityName;
            if (s.specificityName.match('current page')) {
                if (u) themeData.url = u;
                if (s.url) themeData.url = s.url;
                if (!themeData.url) themeData.url = window.location.href.replace('https://'+na.globals.domain,'');
                //if (themeData.app) delete themeData.app;
            }
            if (
                typeof s.specificityName=='string'
                && (
                    s.specificityName.match(/site /)
                    || s.specificityName.match(/current page/)
                )
            ) {
                delete themeData.view;
                delete themeData.app;
            }
            if (
                typeof s.specificityName=='string'
                && s.specificityName.match(/app /)
            ) {
                delete themeData.view;
                if (app) themeData.app = app;
            }
            if (
                typeof s.specificityName=='string'
                && s.specificityName.match(/user /)
            ) {
                delete themeData.role;
                if (themeData.app) delete themeData.app;
            }


            /*
            for (var i=0; i<na.desktop.globals.divs.length; i++) {
                var selector = na.desktop.globals.divs[i];
                themeData.dialogs = $.extend (themeData.dialogs, na.fetchTheme (selector));
            }*/

            themeData = na.loadTheme_fetchDialogs(themeData);
            na.loadTheme_applySettings (themeData, null, false); // apply theme changes, all except .background in this case.
            na.globals.themes[na.globals.themeName] = $.extend({}, themeData);

            // ENCAPSULATE (ENCODE) json objects for HTTP transport
            themeData.themeSettings = JSON.stringify(themeData.themeSettings);
            themeData.apps = JSON.stringify(Object.assign({},themeData.apps));
            //if (themeData.dialogs.indexOf('+')!==-1) themeData.dialogs = themeData.dialogs.replace(/\+/g, ' ');
            //if (themeData.dialogs.indexOf('\\')!==-1) themeData.dialogs = themeData.dialogs.replace(/\\/g, '');

            var
            url = '/NicerAppWebOS/logic.AJAX/ajax_database_saveTheme.php?viewID='+na.m.base64_encode_url(JSON.stringify(na.globals.app)),
            ac2 = {
                type : 'POST',
                url : url,
                data : themeData,
                success : function (data, ts, xhr) {
                    if (data.match('status : Failed')) {
                        $('#siteLoginFailed').html('Could not save settings. Please login again.').fadeIn('normal', 'swing', function () {
                            setTimeout (function() {
                                $('#siteLoginFailed').fadeOut('normal', 'swing');
                            }, 2 * 1000);
                        });
                        na.m.log (10, 'na.saveTheme() : FAILED.');

                    } else {
                        //na.globals.specificityName = na.globals.specificityName_revert;
                        //na.setSpecificity();

                        //na.loadTheme(null, null, false); //REPLACED with .loadTheme_applySettings() in the block above this AJAX call.

                        na.m.log (10, 'na.saveTheme() : FINISHED.', false);
                        na.settings.running_saveTheme = false;
                        if (typeof callback=='function') callback (themeData, data);
                    }
                },
                error : function (xhr, textStatus, errorThrown) {
                    na.m.log (10, 'na.saveTheme() : FAILED (HTTP ERROR CODE : '+xhr.status+', HTTP ERROR MSG : '+errorThrown+')+');
                    na.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                }
            };
            //debugger;
            $.ajax(ac2);
        }, 750);
    }

    loadTheme_fetchDialogs (themeData) {
        themeData = $.extend (na.globals.themes[na.globals.themeName], themeData);
        for (var divSel in na.c.dialogs) {
            if (!themeData.themeSettings) {
                themeData.themeSettings = { // gets initialized through na.onload_phase2() calling na.loadTheme()
                    Dialogs : {}, // filled in below here.
                    Apps : {}, // ditto
                    Extras :  na.te.transform_jsTree_to_siteGlobalsThemes() // pulls data modified by end-users from the Theme Editor back into this na.saveTheme() AJAX call
                };
            }
            if (!themeData.themeSettings.Apps) themeData.themeSettings.Apps = {};

            var
            regExDialogs = /#site(.*)[\s\w\.\#\d\>]*/,
            regExApps = /#app__(.*)__(.*)$/;
            if (divSel.match(regExDialogs)) {
                var divName = divSel.match(regExDialogs)[1];
                if (!themeData.themeSettings['Dialogs'][divName])
                    themeData.themeSettings['Dialogs'][divName] = { css : {} };
                themeData.themeSettings['Dialogs'][divName]['css'] =
                    $.extend (
                        themeData.themeSettings['Dialogs'][divName]['css'],
                        na.fetchTheme(divSel)
                    );
            } else if (divSel.match(regExApps)) {
                var
                m = divSel.match(regExApps),
                appName = m[1],
                appDialogName = m[2];
                if (!themeData.themeSettings['Apps'][appName])
                    themeData.themeSettings['Apps'][appName] = { css : {} };
                //if (!themeData.themeSettings['Apps'][appName]['css'][divSel])
                themeData.themeSettings['Apps'][appName]['css'] =
                    $.extend(
                        themeData.themeSettings['Apps'][appName]['css'],
                        na.fetchTheme(divSel)
                    );
            }
        };

        //if (!themeData.themeSettings.Extras)
        try {
            themeData.themeSettings.Extras = na.te.transform_jsTree_to_siteGlobalsThemes();
        } catch (err) {
            //debugger;
        }

        return themeData;
    }

    fetchTheme (selector) {
        var ret = {};
        ret[selector] = {
            border : $(selector).css('border'),
            borderRadius : $(selector).css('borderRadius'),
            boxShadow : $(selector).css('boxShadow'),
            color : $(selector).css('color'),
            fontSize : $(selector).css('fontSize'),
            fontWeight : $(selector).css('fontWeight'),
            fontFamily : $(selector).css('fontFamily'),
            textShadow : $(selector+' > .vividDialogContent').css('textShadow')//,
            //opacity : $(selector).css('opacity')
        };
        ret[selector].border = // firefox work-around
            $(selector).css('borderTopWidth')+' '
            //+$(selector).css('borderRightWidth')+' '
            //+$(selector).css('borderBottomWidth')+' '
            //+$(selector).css('borderLeftWidth')+' '
            +$(selector).css('borderTopStyle')+' '
            //+$(selector).css('borderRightStyle')+' '
            //+$(selector).css('borderBottomStyle')+' '
            //+$(selector).css('borderLeftStyle')+' '
            +$(selector).css('borderTopColor')+' '
            //+$(selector).css('borderRightColor')+' '
            //+$(selector).css('borderBottomColor')+' '
            //+$(selector).css('borderLeftColor')+' ';
        ret[selector].borderRadius = // firefox work-around
            $(selector).css("borderTopLeftRadius")+' '
            +$(selector).css("borderTopRightRadius")+' '
            +$(selector).css("borderBottomRightRadius")+' '
            +$(selector).css("borderBottomLeftRadius")+' ';





        if (!$(selector+' > .vividDialogBackground1')[0]) {
            if ($(selector).css('opacity')!=='') {
                ret[selector].opacity = $(selector).css('opacity');
            };
            if ($(selector).css('backgroundSize')!=='') {
                ret[selector].backgroundSize = $(selector).css('backgroundSize');
            };

            if ($(selector).css('backgroundImage') && $(selector).css('backgroundImage')!=='' && !$(selector).css('backgroundImage').match(/none/)) {
                ret[selector].background =
                    $(selector).css('backgroundImage').match(/url\(.*\).*%/)
                        ? $(selector).css('backgroundImage')
                        : $(selector).css('backgroundImage').replace(')',') 0% 0% / ')
                    +$(selector).css('backgroundSize')+' '
                    +$(selector).css('backgroundRepeat');
            } else if ($(selector).css('backgroundColor') !== '') {
                ret[selector].background = $(selector).css('backgroundColor');
            }
        };


        if (
            !selector.match(/,/)
            && $(selector+' > .vividDialogBackground1').length>0
        ) { // for vividDialogs only
            ret[selector+' > .vividDialogBackground1'] = {
                opacity : $(selector+' > .vividDialogBackground1').css('opacity'),
                background :
                    $(selector+' > .vividDialogBackground1').css('background') && $(selector+' > .vividDialogBackground1').css('background') !==''
                    ? $(selector+' > .vividDialogBackground1').css('background').match(/url\(.*\).*%/)
                        ? $(selector+' > .vividDialogBackground1').css('background')
                        : $(selector+' > .vividDialogBackground1').css('background').replace(')',') 0% 0% / ')
                    : 'none',
                borderRadius : $(selector).css('borderRadius'),
                backgroundSize : $(selector+' > .vividDialogBackground1').css('backgroundSize'),
                boxShadow : $(selector+' > .vividDialogBackground1').css('boxShadow')
            };
            ret[selector+' > .vividDialogBackground1'].borderRadius = ret[selector].borderRadius;

            // bugfix for firefox :
            if (
                ret[selector+' > .vividDialogBackground1'].background===''
                && $(selector+' > .vividDialogBackground1').css('backgroundImage') !== ''
            ) ret[selector+' > .vividDialogBackground1'].background =
                $(selector+' > .vividDialogBackground1').css('backgroundImage').replace(/http.*?\/\/.*?\//,'')+' '
                +$(selector+' > .vividDialogBackground1').css('backgroundSize')+' '
                +$(selector+' > .vividDialogBackground1').css('backgroundRepeat');

            if (
                ret[selector+' > .vividDialogBackground1'].background
                && (
                    ret[selector+' > .vividDialogBackground1'].background===''
                    || ret[selector+' > .vividDialogBackground1'].background.match('none')
                )
                && $(selector+' > .vividDialogBackground1').css('backgroundColor') !== ''
            ) ret[selector+' > .vividDialogBackground1'].background = $(selector+' > .vividDialogBackground1').css('backgroundColor');
        };

        /*
        ret[selector+' td'] = {
            fontSize : $(selector+' td').css('fontSize'),
            fontWeight : $(selector+' td').css('fontWeight'),
            fontFamily : $(selector+' td').css('fontFamily'),
            textShadow : $(selector+' td').css('textShadow')
        };
        */
        if (ret[selector].fontFamily) ret[selector].fontFamily = ret[selector].fontFamily.replace(/"/g, '');
        //if (ret[selector+' td'].fontFamily) ret[selector+' td'].fontFamily = ret[selector+' td'].fontFamily.replace(/"/g, '');
        return ret;
    }
};
