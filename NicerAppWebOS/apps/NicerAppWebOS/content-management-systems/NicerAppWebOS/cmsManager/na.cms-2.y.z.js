export class naCMS {
    //settings : { current : { mediaFolderView : 'view' } },
    settings = {
        current : { mediaFolderView : 'view', currentDialog : '#siteToolbarLeft' },
		loadedIn : {
			'#siteContent' : {
				settings : {
					initialized : false
				},
				saConfigUpdate (settings) {
					nicerapp.site.globals.desktop.configUpdate();
				},

				onload (settings) {
                    na.m.waitForCondition ('applications/content-management-systems/NicerAppWebOS/cmsManager start', na.m.HTMLidle, function () {
                        var
                        na1 = na.cms, g = na1.globals, s = na1.settings, c = s.current, db = c.db,
                        loadedIn = s.loadedIn['#siteContent'];

                        s.settings_onload = settings;
                        settings.onHold = true; // signals a wait for na.site.loadTheme() has started
                        na.site.settings.current.app = 'applications/content-management-systems/NicerAppWebOS/cmsManager';

                        $('#siteContent > .vividDialogContent, #siteToolbarLeft > .vividDialogContent').css({opacity:1,display:'none'}).fadeIn('slow');

                        $('#siteContent__header').fadeIn('normal', function () {
                            $('#siteContent__header').css({display:'flex'});
                        });

                        $('#document_headers').detach().appendTo('body').css({display:'block',opacity:0.0001,zIndex:-1});
                        if ($(window).width() < 400) {
                            na.cms.settings.current.activeDialog = '#siteContent';
                            na.desktop.settings.visibleDivs =
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteToolbarLeft');
                            na.desktop.settings.visibleDivs =
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteContent');
                            na.desktop.settings.visibleDivs.push('#siteContent');
                            na.desktop.resize();
                        } else {
                            na.desktop.settings.visibleDivs = arrayRemove(na.desktop.settings.visibleDivs, '#siteToolbarLeft');
                            na.desktop.settings.visibleDivs =
                            arrayRemove(na.desktop.settings.visibleDivs, '#siteContent');
                            na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
                            na.desktop.settings.visibleDivs.push('#siteContent');
                            na.desktop.resize();
                        };
                        na.m.waitForCondition ('na.cms.settings.loadedIn[\'#siteContent\'].onload(settings) : tinymce started?', function() {
                            return $('#tinymce_ifr')[0];
                        }, function() {
                            $('#cssPageSpecific', $('#tinymce_ifr')[0].contentDocument).remove();
                            $('#cssPageSpecific').clone().appendTo(
                                $('#tinymce_ifr')[0].contentDocument.head
                            );
                        }, 100);


                        /*na.m.waitForCondition('siteContent dialog reappearance', function () {
                            return (
                                $('#jsPageSpecific').length > 0
                                //&& $('#siteContent__content').length > 0
                                //&& na.m.settings.initialized.site
                            );
                            //return true;
                        }, function () {*/
                            na.cms.settings.loadedIn['#siteContent'].settings.initialized = true;
                            na.cms.settings.loadedIn['#siteContent'].settings.ready = true;
                            document.addEventListener ('keyup', na.cms.onkeyup);
                            na.cms.onload(settings);
                            //na.analytics.logMetaEvent ('applications/content-management-systems/NicerAppWebOS/cmsManager (version '+na.cms.about.version+' is starting.');

                            settings.onHold = false; // signals a wait for na.site.loadTheme() has ended
                            //});
                        //}, 30);
                    }, 30);
				},

				ondestroy : function (settings) {
                    var
                    na1 = na.cms, g = na1.globals, s = na1.settings, c = s.current, db = c.db;

                    $('#siteContent .vividButton_icon_50x50').each(function(idx,el){
                        var elID = el.id;
                        $('#'+elID).remove();
                        delete na.site.settings.buttons['#'+elID];
                    });

                    clearInterval(na.cms.settings.countDownInterval);
                    clearTimeout (na.cms.settings.refreshTimer);
                    clearTimeout (c.adsTimer1);
                    clearTimeout (c.adsTimer2);
                    clearInterval(c.adsInterval1);
                    clearTimeout (c.timerDisplayNews_loop);
                    clearTimeout (c.timerLoadNews_read_loop);
                    clearTimeout (c.timerDisplayNewsItem);
                    clearTimeout (c.timerDisplayItem);
                    clearTimeout (c.timerAnimateItemIn);
                    clearTimeout (c.timerCheck);
                    clearInterval (c.newItemsInterval);
                    clearInterval (c.intervalMailLogCountdown);

                    document.removeEventListener ('keyup', na.cms.onkeyup);
                    $(na.cms.settings.loaderIcon).remove();
                },
				onresize : function (settings) {
					// TODO : what's settings.isManualResize ???
					//if ($('#appGame').css('display')=='none') $('#appGame').fadeIn('slow');

                    na.cms.onresize(settings);

				}
			}
		}
    },

    async onload (settings) {
        var
        fncn = 'na.cms.onload()',
        url1 = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_getTreeNodes.php',
        ac = {
            type : 'GET',
            url : url1,
            success (data, ts, xhr) {
                try {
                    var dat = JSON.parse(data);
                } catch (error) {
                    na.site.fail (fncn+' : AJAX decode error in data returned for url='+url1+', error='+error.message+', in data='+data, xhr);
                    return false;
                }
debugger;
                na.cms.settings.current.db = dat;
                $.jstree.defaults.core.error = function (a,b,c,d) {
                    //debugger;
                };
                if (typeof $('#jsTree').jstree == 'function')
                $('#jsTree').css({
                    height : $('#siteToolbarLeft .vividDialogContent').height() - $('#jsTree_navBar').height()
                });
                var x = $('#jsTree');
                if (!x.is('.jstree'))
                x.jstree({
                    core : {
                        data : dat,
                        check_callback : true
                    },
                    types : {
                        "naSystemFolder" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naSystemFolder.png",
                            "valid_children" : []
                        },
                        "naUserRootFolder" : {
                            "max_depth" : 14,
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naUserRootFolder.png",
                            "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                        },
                        "naGroupRootFolder" : {
                            "max_depth" : 14,
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naGroupRootFolder.png",
                            "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                        },
                        "naFolder" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naFolder.png",
                            "valid_children" : ["naFolder", "naMediaAlbum", "naDocument"]
                        },
                        "naDialog" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naSettings.png",
                            "valid_children" : []
                        },
                        "naSettings" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naSettings.png",
                            "valid_children" : []
                        },
                        "naTheme" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naVividThemes.png",
                            "valid_children" : []
                        },
                        "naVividThemes" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naVividThemes.png",
                            "valid_children" : []
                        },
                        "naMediaAlbum" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naMediaAlbum.png",
                            "valid_children" : [ "naMediaAlbum" ]
                        },
                        "naDocument" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naDocument.png",
                            "valid_children" : []
                        },
                        "saApp" : {
                            "icon" : "/NicerAppWebOS/siteMedia/na.view.tree.naApp.png",
                            "valid_children" : []
                        }
                    },
                    plugins : [
                        "contextmenu", "dnd", "search", "state", "types", "wholerow"
                    ]
                }).on('ready.jstree', function (e, data) {
                    var tree = $('#jsTree').jstree(true);
                    for (var i=0; i<tree.settings.core.data.length; i++) {
                        var it = tree.settings.core.data[i];
                        if (it.state && it.state.selected) tree.select_node(it._id);
                    }
                }).on('open_node.jstree', function (e, data) {
                    na.cms.onchange_folderStatus_openOrClosed(e, data);

                }).on('close_node.jstree', function (e, data) {
                    na.cms.onchange_folderStatus_openOrClosed(e, data);

                }).on('rename_node.jstree', function (e, data) {
                    na.cms.onchange_rename_node(e, data);

                }).on('changed.jstree', function (e, data) {

                    if (
                        //data.action!=='ready'
                        //&&
                        /*data.action!=='model'
                        && */data.action!=='select_node'
                    ) return false;

                    $('#siteContent .vividTabPage').fadeOut('fast');
                    clearTimeout(na.cms.settings.timeout_changed);
                    na.cms.settings.timeout_changed = setTimeout (function(data) {
                        var l = data.selected.length, rec = null;
                        for (var i=0; i<l; i++) {
                            var d = data.selected[i], rec2 = data.instance.get_node(d);
                            if (rec2 && rec2.original) rec = rec2;
                        }

                        if (
                            na.cms.settings.current.selectedTreeNode
                            && rec
                            && na.cms.settings.current.selectedTreeNode.id!==rec.id
                            && na.cms.settings.current.selectedTreeNode.type=='naDocument'
                        ) na.cms.saveEditorContent(na.cms.settings.current.selectedTreeNode, function(){
                            na.cms.settings.current.selectedTreeNode = rec;
                            //na.cms.onchange_selectedNode (settings, data, rec, function() {
                                //na.cms.refresh(function() {
                            //      na.cms.onchange_jsTreeNode(settings, data,rec);
                                //});
                            //});
                        })
                        else if (rec) na.cms.onchange_jsTreeNode(settings, data, rec);

                        if (rec && rec.type=='naDocument') $('#document').fadeIn('slow');
                        if (rec && rec.type=='naMediaAlbum') $('#upload').fadeIn('slow');
                        if (
                            rec
                            && (
                                rec.type=='naDocument'
                                || rec.type=='naMediaAlbum'
                            )
                        ) {
                            if ($(window).width() < 400) {
                                na.cms.settings.current.activeDialog = '#siteContent';
                                arrayRemove(na.desktop.settings.visibleDivs, '#siteToolbarLeft');
                                arrayRemove(na.desktop.settings.visibleDivs, '#siteContent');
                                na.desktop.settings.visibleDivs.push('#siteContent');
                                na.desktop.resize();
                            } else {
                                na.cms.settings.current.activeDialog = '#siteContent';
                                arrayRemove(na.desktop.settings.visibleDivs, '#siteToolbarLeft');
                                arrayRemove(na.desktop.settings.visibleDivs, '#siteContent');
                                na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
                                na.desktop.settings.visibleDivs.push('#siteContent');
                                na.desktop.resize();
                            };
                        }

                        na.site.settings.buttons['#btnAddUser'].disable();
                        na.site.settings.buttons['#btnAddGroup'].disable();
                        na.site.settings.buttons['#btnAddFolder'].disable();
                        na.site.settings.buttons['#btnAddDocument'].disable();
                        na.site.settings.buttons['#btnAddMediaAlbum'].disable();
                        na.site.settings.buttons['#btnDeleteRecord'].disable();

                        if (rec && rec.type=='naSystemFolder' && rec.text=='Users')
                            na.site.settings.buttons['#btnAddUser'].enable();


                        if (rec && rec.type=='naSystemFolder' && rec.text=='Groups')
                            na.site.settings.buttons['#btnAddGroup'].enable();


                        if (rec &&
                            (
                                rec.type=='naUserRootFolder'
                                || rec.type=='naGroupRootFolder'
                                || rec.type=='naFolder'
                            )
                        ) na.site.settings.buttons['#btnAddFolder'].enable();


                        if (rec &&
                            (
                                rec.type=='naFolder'
                            )
                        ) {
                            na.site.settings.buttons['#btnAddDocument'].enable();
                            na.site.settings.buttons['#btnAddMediaAlbum'].enable();
                        }

                        if (rec &&
                            (
                                rec.type=='naFolder'
                                || rec.type=='naDocument'
                                || rec.type=='naMediaAlbum'
                            )
                        ) na.site.settings.buttons['#btnDeleteRecord'].enable();
                    }, 500, data);

                    //clearTimeout (na.cms.settings.current.timeoutRefresh);
                    //na.cms.settings.current.timeoutRefresh = setTimeout(na.cms.refresh,1000);

                }).on('move_node.jstree', function (e, data) {

                    var
                    tree = $('#jsTree').jstree(true),
                    oldPath = na.cms.currentPath(tree.get_node(data.old_parent)),
                    newPath = na.cms.currentPath(tree.get_node(data.parent)),
                    url2 = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_moveNode.php',
                    ac = {
                        type : 'POST',
                        url : url2,
                        data : {
                            database : data.node.original.database,
                            oldParent : data.old_parent,
                            oldPath : oldPath,
                            newParent : data.parent,
                            newPath : newPath,
                            target : data.node.original._id || original.id
                        },
                        success (data, ts, xhr) {
                        },
                        error (xhr, textStatus, errorThrown) {
                            na.site.ajaxFail(fncn, url2, xhr, textStatus, errorThrown);
                        }
                    };
                    $.ajax(ac);

                });



                $('#siteToolbarLeft .lds-facebook').fadeOut('slow');
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url1, xhr, textStatus, errorThrown);
            }
        };
        $.ajax(ac);
        na.desktop.settings.visibleDivs.push ('#siteToolbarLeft');

         //setTimeout(function() {
        na.desktop.resize(na.cms.onresize);
            na.site.settings.current.loadingApps = false;
            na.site.settings.current.startingApps = false;
        //na.cms.onresize();
        //}, 1500);

        //$(window).resize(na.cms.onresize)
        //debugger;
    }

    onchange_selectedNode (settings, data,rec, callback) {
        var
        fncn = 'na.cms.onchange_selectedNode()',
        url2 = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_changeNodeStatus_selectedNode.php',
        db = na.cms.settings.current.db,
        dbs = [],
        ac = {
            type : 'POST',
            url : url2,
            data : {
                database : rec.original.database,
                id : rec.original._id || original.id
            },
            success (data, ts, xhr) {
                if (typeof callback=='function') callback();
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url2, xhr, textStatus, errorThrown);
            }
        };
        for (var i=0; i<db.length; i++) {
            if (!dbs.includes(db[i].database)) dbs.push(db[i].database);
        };
        ac.data.databases = JSON.stringify (dbs);
        $.ajax(ac);

    }
    onchange_jsTreeNode (settings, data,rec) {
        var
        c = na.site.settings.current,
        editorHeight =
            $('#siteContent .vividDialogContent').height()
            - $('#document_navBar').height(),
        mce_bars_height = 0;
        $('.mce-toolbar-grp, .mce-statusbar').each(function() {
            mce_bars_height += $(this).height();
        });

        var
        l = data.selected.length,
        tree = $('#jsTree').jstree(true);

        if (l!==1 || data.node.id!==data.selected[0]) {
            tree.deselect_all();
            tree.select_node(data.node.id);
            return false;
        }

        for (var i=0; i<l; i++) {
            var
            d = data.selected[i],
            rec = data.instance.get_node(d),
            url0 = '',
            sel = tree.get_node(tree.get_selected()[0]);

            debugger;
            if (sel)
            for (var i=0; i<sel.parents.length; i++) {
                var p = tree.get_node(sel.parents[i]);
                if (p.type == 'naUserRootFolder') url0 = p.text;
                if (p.type == 'naGroupRootFolder') url0 = p.text;
            }

            if (rec && rec.original) {
                //na.site.setSpecificity(); // ENDLESS LOOP IN /cms
                na.cms.treeButtonsEnableDisable (rec);

                $('#documentLabel').val(rec.original.text);
                $('#documentTitle').val(rec.original.pageTitle);
                $('#mediaFolderLabel').val(rec.original.text);

                $('#url0').html('/'+url0+'/');
                $('#url1_dropdown_selected').html(rec.original.url1);
                $('#url1_dropdown_selector option').each(function(idx,optEl) {
                    if ($(optEl).html()==data) $(optEl).addClass('selected');
                });
                $('#url2_value').val(rec.original.seo_value);

                $('#nb_url0').html('/'+url0+'/');
                $('#nb_documentLabel').val(rec.original.text);
                $('#nb_documentTitle').val(rec.original.pageTitle);
                $('#nb_mediaFolderLabel').val(rec.original.text);

                $('#nb_url1_dropdown_selected').html(rec.original.url1);
                $('#nb_url1_dropdown_selector option').each(function(idx,optEl) {
                    if ($(optEl).html()==data) $(optEl).addClass('selected');
                });
                $('#nb_url2_value').val(rec.original.seo_value);


                var
                url1 = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_getPageTitle.php',
                ac = {
                    type : 'POST',
                    url : url1,
                    data : {
                        database : rec.original.database,
                        id : rec.original._id || original.id
                    },
                    success (data, ts, xhr) {
                        $('#documentTitle').val(data);
                        $('#nb_documentTitle').val(data);
                    },
                    error (xhr, textStatus, errorThrown) {
                        na.site.ajaxFail(fncn, url2, xhr, textStatus, errorThrown);
                    }
                };
                debugger;
                $.ajax(ac);


                na.cms.settings.current.selectedTreeNode = rec;
                if (rec.original.type=='naDocument') {
                    na.cms.loadEditorContent(rec);

                    $('#folder').css({display:'none'});
                    $('#upload').css({display:'none'});
                    $('#document').css({display:'block'});
                    $('.mce-tinymce').css ({
                        width : 'calc(100% - 4px)',
                        height : editorHeight - $('.mce-statusbar').height()
                    });
                    //alert (mce_bars_height);
                    $('.mce-top-part, .mce-statusbar').css({
                        background : 'rgba(0,0,50,0.4)'
                    });
                    $('#tinymce_ifr').css ({
                        width : '100%',
                        height : editorHeight - $('.mce-statusbar').height(),
                        background : 'rgba(0,0,0,0.001)'
                    });

                } else if (rec.original.type=='naMediaAlbum') {
                    debugger;
                    $('#folder').css({display:'none'});
                    $('#upload').css({display:'block'});
                    $('#document').css({display:'none'});
                    var
                    path = na.cms.currentPath(rec),
                    path = path.replace(/ /g, '%20'),
                    src = (
                        na.cms.settings.current.mediaFolderView == 'upload'
                        //? '/NicerAppWebOS/3rd-party/plupload-2.3.6/examples/jquery/jquery_ui_widget.php?c='+na.m.changedDateTime_current()+/*'&smID='+siteManager.id+'&iid='+iid+'&dialogID='+did+* /'&basePath='+path
                        ? '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/jquery_ui_widget.2.3.7.php?basePath='+path
                        : '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/index.php?basePath='+path+'&photoAlbum_emptyFolderPage=/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/jquery_ui_widget.2.3.7.php'
                    ),
                    el = $('#jQueryFileUpload')[0];
                    el.onload = na.cms.onresize;
                    el.src = src;

                } else {
                    $('#folder').css({display:'block'});
                    $('#upload').css({display:'none'});
                    $('#document').css({display:'none'});
                }

                if (c.divsInitializing)
                for (var j=0; j < c.divsInitializing.length; j++) {
                    var d = c.divsInitializing[j];
                    if (d.divID=='siteContent') d.loaded = true;
                }

                //if (settings && typeof settings.callback=='function') settings.callback();
            }
        };
    }
    
    currentPath (node) {
        var me = na.cms, s = me.settings, c = s.current;
        
        var
        path = [ ],
        n = node;
        while (n.parent!=='#') {
            path[path.length] = n.text;
            var n2 = n;
            for (var idx in c.db) {
                var st = c.db[idx];
                if (st.id && st.id == n.parent) {
                    n = st;
                    break;
                }
            }
            if (n2 === n) {
                console.log ('ERROR : na.tree.currentPath(iid, ) : n2===n');
                debugger;
                break;
            }
        };
        path[path.length] = n.text;
        path = path.reverse().join('/');
        return path;//.replace('Users/','');
        //return path; // only paths being used right now already include the username in that path (from the tree node under 'Users')
    }
    
    onresize (settings) {
        //debugger;
        //alert ('onr');
        //na.m.waitForCondition ('na.cms.onresize : desktopIdle?', na.m.desktopIdle, function () {
            //alert ('onres1');
            //na.desktop.resize(function (t) {
                //if (!t) t = this;
                //if (t.id=='siteContent') {
        //debugger;
                    $('#siteContent .vividDialogContent').css({overflow:''});
                    //alert ('onres');
                    na.m.waitForCondition('na.cms.onresize() : tree node selected and rich text editor (tinymce) ready?',
                        function () {
                            return (
                                na.m.desktopIdle()
                                &&  na.cms.settings.current.selectedTreeNode
                                && (
                                    (
                                        na.cms.settings.current.selectedTreeNode.type == 'naDocument'
                                        && typeof $('#tinymce_ifr')[0] == 'object'
                                        && $('#tinymce_ifr').css('visibility')!=='hidden'
                                        && $('#tinymce_ifr').css('display')!=='none'
                                    ) || na.cms.settings.current.selectedTreeNode.type == 'naMediaAlbum'
                                )
                            )
                        },
                        function () {
                            //if (na.cms.settings.current.activeDialog=='#siteContent')
                            switch (na.cms.settings.current.selectedTreeNode.type) {
                                case 'naDocument':
                                    let w = 0, d = $('#document').css('display');
                                    $('#document').css({display:'block'});

                                    if ($('#siteContent .vividDialogContent').width() < 400) {
                                        w += $('#url0').width() + 20;
                                        w += $('#url1_dropdown_selected').width() + 20;
                                        w += $('#url1-2').width() + 30;
                                        $('#url2_value').css({width: $('#siteContent .vividDialogContent').width() - w});
                                    } else {
                                        $('.navbar_button', $('#document_navBar')[0]).each(function(idx,el){
                                            w += $(el).width();
                                        });
                                        w += $('#nb_url0').width() + 20;
                                        w += $('#nb_url1_dropdown_selected').width() + 20;
                                        w += $('#nb_url1-2').width() + 20;
                                        w += $('#nb_url2_value').width() + 20;
                                        w += $('#nb_documentLabel_label').width() + 20;
                                        w += $('#nb_documentLabel_value').width() + 20;
                                        w += $('#nb_documentTitle_label').width();
                                        $('#documentTitle').css({
                                            width : jQuery('#siteContent .vividDialogContent').width() - w - 45
                                        });
                                    }
                                    var editorHeight = $('#siteContent .vividDialogContent').height() - $('#document_navBar').height() - 15;
                                    $('#jsTree').css({ height : $('#siteToolbarLeft .vividDialogContent').height() - $('#jsTree_navBar').height() - 20 });
                                    var mce_bars_height = 0;
                                    //alert ($('#document_navBar').height());
                                    $('.mce-toolbar-grp, .mce-statusbar').each(function() { mce_bars_height += $(this).height(); });
                                    $('.mce-tinymce').css ({
                                        width : 'calc(100% - 4px)',
                                        height : editorHeight - $('.mce-statusbar').height() +10
                                    });
                                    //alert (mce_bars_height);
                                    $('.mce-top-part, .mce-statusbar').css({
                                        background : 'rgba(0,0,50,0.4)'
                                    });
                                    $('#tinymce_ifr').css ({
                                        width : '100%',
                                        height : editorHeight - mce_bars_height +10,
                                        background : 'rgba(0,0,0,0.4)'
                                    });
                                    $('#document').css({display:d});
                                    break;

                                case 'naMediaAlbum':
                                    var
                                    p = $('#siteContent .vividDialogContent'),
                                    nb = $('#mediaFolder_navBar');

                                    $('#siteContent .vividDialogContent').css({overflow:'hidden'});
                                    $('.jQueryFileUpload').css ({
                                        top : '0px',
                                        left : '0px',
                                        width : p.width(),
                                        height : p.height() - nb.height(),
                                        border : '0px solid rgba(0,0,0,0)'
                                    });
                                    break;

                            };

                            if ($('#siteContent .vividDialogContent').width() < 400) {
                                $('#document_navBar .navbar_section').not('.shown').css({ display : 'none' });
                                $('#btnSettingsHeaders, #btnTree').css({display:'block'});
                            } else {
                                $('#document_navBar .navbar_section').not('.shown').css({ display : 'inline-block' });
                                $('#btnSettingsHeaders, #btnTree').css({display:'none'});
                            }

                            if ($('#btnTree').css('display')!=='none') {
                                var w = $('#document_navBar').width() - $('#btnTree').position().left - 60;
                                $('#nb_documentLabel').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 300
                                });
                            } else {
                                var w = $('#document_navBar').width() - $('#btnPublish').position().left - 60;
                                $('#nb_documentLabel').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 300
                                });
                            }

                            /*if ($('#nb_url0').position().left < $('#btnPublish').position().left) {
                                var w = $('#document_navBar').width() - $('#nb_url2_value').position().left - 10;
                                $('#nb_url2_value').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 'none'
                                });
                            } else {
                                var w = $('#document_navBar').width() - $('#nb_documentTitle_label').width() - $('#nb_documentTitle').position().left - 10;
                                $('#nb_documentTitle').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : $('#nb_url2_value').position().left + $('#nb_url2_value').width() + 120
                                });
                            };*/
                                var w = $('#document_navBar').width() - $('#nb_url2_value').position().left - 10;
                                $('#nb_url2_value').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 300
                                });

                            if ($('#nb_documentTitle').position().left < $('#btnPublish').position().left) {
                                var w = $('#document_navBar').width() - $('#nb_documentTitle_label').width() - 10;
                                $('#nb_documentTitle').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : 'none'
                                });
                            } else {
                                var w = $('#document_navBar').width() - $('#nb_documentTitle_label').width() - $('#nb_documentTitle').position().left - 10;
                                $('#nb_documentTitle').css({
                                    minWidth : 120,
                                    width : w,
                                    maxWidth : $('#nb_url2_value').position().left + $('#nb_url2_value').width() + 120
                                });
                            };

                            $('.navbar_section').css({margin:0,marginBottom:4});

                            if (
                                typeof settings == 'object'
                                && typeof settings.callback == 'function'
                            ) settings.callback (settings);

                        }, 500
                    );
                //}
           // });
        //}, 100);

        /*
        if (
            typeof settings == 'object'
            && typeof settings.callback == 'function'
        ) settings.callback (settings);
        */
    }
    
    refresh (callback, setSelected) {
        $('#siteToolbarLeft .lds-facebook').fadeIn('slow');
        var 
        fncn = 'na.cms.refresh(callback)',
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_getTreeNodes.php',
        ac = {
            type : 'GET',
            url : url,
            success (data, ts, xhr) {
                let 
                jt = $('#jsTree').jstree(true),
                dat = JSON.parse(data),
                jfu = $('.jQueryFileUpload')[0];
                
                na.cms.settings.current.db = dat;
                jt.settings.core.data = dat;
                jt.refresh(false, false);

                setTimeout (function (dat, jfu) {
                    if (setSelected)
                    for (var i=0; i<dat.length; i++) {
                        var dit = dat[i];
                        if (dit.state.selected) {
                            na.cms.settings.current.selectedTreeNode = dit;
                            jt.select_node(dit._id);
                        };
                    }
                    $('#siteToolbarLeft .lds-facebook').stop(true,true).fadeOut('slow');
                    var
                    rec = na.cms.settings.current.selectedTreeNode,
                    relFilePath = na.cms.currentPath(rec);
                    if (jfu) jfu.contentWindow.location.href = '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/index.php?basePath='+relFilePath;


                    setTimeout (function () {
                        if (typeof callback=='function') callback (dat);
                    }, 500);
                }, 1000, dat, jfu);


            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    
    loadEditorContent (rec, callback) {
        var
        fncn = 'na.cms.loadEditorContent(rec, callback)',
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_loadDocument.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : rec.original.database.replace('tree','documents'),
                id : rec.original._id || original.id
            },
            success (data, ts, xhr) {
                na.m.waitForCondition ('tinymce ready',
                    function () {
                        return tinymce.ready
                    },
                    function () {
                        tinymce.get('tinymce').setContent(data);
                        if (typeof callback=='function') callback(rec);
                    },
                    100
                );
            },
            error (xhr, textStatus, errorThrown) {
                tinymce.get('tinymce').setContent('');
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        debugger;
        $.ajax(ac);
    }


    saveEditorContent (rec, callback) {
        var 
        fncn = 'na.cms.saveEditorContent(rec,callback)',
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_editDocument.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : rec.original.database.replace('tree','documents'),
                id : rec.original._id || original.id,
                dataID : rec.original.dataID,
                parent : rec.original.parent,
                url1 : $('#nb_url1_dropdown_selected').html(),
                seoValue : $('#nb_url2_value').val(),
                pageTitle : $('#nb_documentTitle').val(),
                document : tinymce.get('tinymce').getContent(),
            },
            success (data, ts, xhr) {
                try {
                    var dat = JSON.parse(data);
                } catch (error) {
                    console.log ('na.cms.saveEditorContent() : FAILED :', error);
                }
                for (var ct in dat.rec) {
                    for (var tn in dat.rec[ct].document) rec.original.id = dat.rec[ct].document[tn].id; // TODO : this prevents different dataIDs in different ct environments from being used. needs a fix, eventually. by changing the dataID field in ___cms_tree__ and ___cms_documents__ to a JSON array (ct => dataID).
                    break;
                }
                if (typeof callback=='function') callback(rec);
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        },
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        role = undefined,
        user = undefined;
        for (var i=0; i<sel.parents.length; i++) {
            var p = tree.get_node(sel.parents[i]);
            if (p.type == 'naUserRootFolder') user = p.text;
            if (p.type == 'naGroupRootFolder') role = p.text;
        }
        if (user) ac.data.user = user;
        if (role) ac.data.role = role;
        $.ajax(ac);
        
    }

    onclick_btnTree (event) {
        na.cms.settings.current.activeDialog = '#siteToolbarLeft';
        na.d.s.visibleDivs.remove('#siteContent');
        na.d.s.visibleDivs.push('#siteToolbarLeft');
        na.desktop.resize();
    }

    onclick_editHeaders (event) {
        if (parseFloat($('#document_headers').css('opacity')) !== 1) {
            var t = $('#btnInsertLink').offset().top + $('#btnInsertLink').height() + na.d.g.margin;
            $('body').append('<div class="naBackPanel" style="position:absolute;top:0px;left:0px;width:100%;height:100%;z-index:19000" onclick="$(this).remove(); $(\'#document_headers\').animate ({ opacity : 0.0001, zIndex : -1 }, \'normal\');">&nbsp;</div>');
            $('#document_headers').css ({
                top : t,
                left : na.d.g.margin,
                width : $(window).width() - (2 * na.d.g.margin),
                height : $(window).height() - (2 * na.d.g.margin) - t,
                zIndex : 20 * 1000
            }).animate ({ opacity : 1 }, 'normal');
        } else {
            $('#document_headers').animate ({ opacity : 0.0001, zIndex : -1 }, 'normal');
        }
    }
    
    onchange_folderStatus_openOrClosed (event, data) {
        var 
        fncn = 'na.cms.onchange_folderStatus_openOrClosed(event,data)',
        tree = $('#jsTree').jstree(true),
        //sel = tree.get_node(tree.get_selected()[0]),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_changeNodeStatus_openOrClosed.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : data.node.original.database,
                id : data.node.id,
                open : data.node.state.opened ? 'true' : 'false'
            },
            success (data, ts, xhr) {
                na.cms.refresh();
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    onchange_rename_node (event, data) {
        var 
        fncn = 'na.cms.onchange_rename_node(event,data)',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_changeNode_rename_node.php',
        rec = na.cms.settings.current.selectedTreeNode,
        relFilePath = na.cms.currentPath(rec),
        oldFolderName = data.old,
        newFolderName = data.text,
        newRelFilePath = relFilePath.replace('/'+oldFolderName, '/'+newFolderName),
        ac = {
            type : 'POST',
            url : url,
            dbg : {
                data : data,
                relFilePath : relFilePath,
                oldFolderName : oldFolderName,
                newFolderName : newFolderName,
                newRelFilePath : newRelFilePath
            },
            data : {
                database : sel.original.database,
                id : data.node.id,
                node_title_new : data.text,
                node_title_old : data.old,
                oldPath : relFilePath.replace('/'+newFolderName,'')+'/'+data.old,
                newPath : newRelFilePath
            },
            success (data, ts, xhr) {
                na.cms.refresh(); // needs this to update the JS db!
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    onchange_mediaFolderLabel (event) {
        var 
        fncn = 'na.cms.onchange_mediaFolderLabel(event)',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        rec = na.cms.settings.current.selectedTreeNode,
        relFilePath = na.cms.currentPath(rec),
        oldFolderName = rec.original.text,
        newFolderName = $('#mediaFolderLabel').val();

        if (newFolderName.match(/</)) {
            alert ('Could not change mediaFolderLabel');
            return false;
        };

        var
        newRelFilePath = relFilePath.replace('/'+oldFolderName, '/'+newFolderName),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_changeNode_mediaFolderLabel.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : sel.original.database,
                id : sel.original._id || original.id,
                text : newFolderName,
                relFilePath : relFilePath,
                newRelFilePath : newRelFilePath
            },
            success (data, ts, xhr) {
                na.cms.refresh();
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    onchange_documentHeaders (evt,callback) {
        if (
            $('#nb_documentLabel').val().match(/</)
            || $('#nb_documentTitle').val().match(/</)
            || $('#nb_url2_value').val().match(/</)
            || $('#nb_url1_dropdown_selected').html().match(/</)
        ) {
            alert ('Could not save document settings.');
            return false;
        };
        var
        fncn = 'na.cms.onchange_documentHeaders()',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_changeNode_documentHeaders.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : sel.original.database,
                id : sel.original._id || original.id,
                url1 : $('#nb_url1_dropdown_selected').html(),
                seoValue : $('#nb_url2_value').val(),
                pageTitle : $('#nb_documentTitle').val(),
                text : $('#nb_documentLabel').val()
            },
            success (data, ts, xhr) {
                na.cms.refresh(callback);
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }
        },
        role = undefined,
        user = undefined;

        for (var i=0; i<sel.parents.length; i++) {
            var p = tree.get_node(sel.parents[i]);
            if (p.type == 'naUserRootFolder') user = p.text;
            if (p.type == 'naGroupRootFolder') role = p.text;
        }
        if (user) ac.data.user = user;
        if (role) ac.data.role = role;

        $.ajax(ac);
    }

    onclick_btnAddFolder () {
        var 
        fncn = 'na.cms.onclick_addFolder()',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        rec = na.cms.settings.current.selectedTreeNode,
        relFilePath = na.cms.currentPath(rec),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_addNode.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : sel.original.database,
                parent : sel.original._id || original.id,
                relFilePath : relFilePath,
                type : 'naFolder'
            },
            success (data, ts, xhr) {
                na.cms.refresh();
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        debugger;
        $.ajax(ac);
    }
    
    onclick_btnAddDocument () {
        debugger;
        var 
        fncn = 'na.cms.onclick_addDocument()',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_addNode.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : sel.original.database,
                parent : sel.original._id || original.id,
                type : 'naDocument'
            },
            success (data, ts, xhr) {
                na.cms.refresh();
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        $.ajax(ac);
    }
    
    onclick_btnAddMediaAlbum () {
        var 
        fncn = 'na.cms.onclick_addMediaAlbum()',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        rec = na.cms.settings.current.selectedTreeNode,
        relFilePath = na.cms.currentPath(rec),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_addNode.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : sel.original.database,
                parent : sel.original._id || original.id,
                type : 'naMediaAlbum',
                relFilePath : relFilePath
            },
            success (data, ts, xhr) {
                var 
                me = na.cms, s = me.settings, c = s.current;
                try {
                    var dat = JSON.parse(data);
                } catch (error) {
                    na.site.fail (fncn+' : AJAX decode error in data returned for url='+url1+', error='+error.message+', in data='+data, xhr);
                    return false;
                }

                debugger;
                na.cms.refresh(function(nodes) {
                    debugger;
                    for (var i=0; i<c.db.length; i++) {
                        if (c.db[i].text === dat.recordAdded.text) {
                            $('#jsTree').jstree('deselect_all');
                            $('#jsTree').jstree('select_node', c.db[i].id);
                        }
                    }
                    debugger;
                    na.cms.onclick_btnUpload();
                }, false);
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }                
        };
        debugger;
        $.ajax(ac);
    }
    
    onclick_btnDeleteRecord () {
        var 
        fncn = 'na.cms.onclick_delete()',
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        rec = na.cms.settings.current.selectedTreeNode,
        path = na.cms.currentPath(rec),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_deleteNode.php',
        ac = {
            type : 'POST',
            url : url,
            data : {
                database : sel.original.database,
                id : sel.original._id || original.id,
                currPath : path
            },
            success (data, ts, xhr) {
                var parent = $('#jsTree').jstree(true).get_node(na.cms.sel.original.parent);
                debugger;
                na.cms.onchange_selectedNode ( {}, null, parent, function() {
                    setTimeout (na.cms.refresh, 1000);//function() {
                    //      na.cms.onchange_jsTreeNode(settings, data,rec);
                    //});
                });
            },
            error (xhr, textStatus, errorThrown) {
                na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
            }
        };
        na.cms.sel = sel;
        $.ajax(ac);
    }
    
    onclick_publish () {
        var
        tree = $('#jsTree').jstree(true),
        sel = tree.get_node(tree.get_selected()[0]),
        role = undefined,
        user = undefined;
        
        for (var i=0; i<sel.parents.length; i++) {
            var p = tree.get_node(sel.parents[i]);
            if (p.type == 'naUserRootFolder') user = p.text;
            if (p.type == 'naGroupRootFolder') role = p.text;
        }
        var
        arr = {
            misc : {
                folder : '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
            },
            cmsText : {
                database : sel.original.database,
                id : sel.original._id || original.id
            }
        };
        if (user) arr.cmsText.user = user;
        if (role) arr.cmsText.role = role;
        
        //var url = '/'+user+'/in/'+sel.original.dataID;//na.m.base64_encode_url (JSON.stringify(arr));
        na.cms.saveEditorContent(sel, function(rec) {
            debugger;
            var url = '/'+user.replace(/ /g, '-')+'/'+$('#nb_url1_dropdown_selected')[0].innerText+'/'+rec.original.seo_value;
            na.site.loadContent(null, url);

        });
    }
    
    onclick_insertMedia () {
        var
        tmce = tinymce.get('tinymce');

        tmce.windowManager.open({
            title : 'Insert Photo Album',
            url : '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/index.all.php',
            width : 570,
            height: 700
        });
        $('.mce-floatpanel').css({zIndex:900000});
    }
    
    insertMediaFolder (relPath) {
        tinymce.activeEditor.execCommand ('mceInsertContent', false, ':{"mediaFolder":"'+relPath+'"}:');
        tinymce.activeEditor.windowManager.close();
    }
    
    onclick_btnUpload (evt) {
        na.cms.settings.current.mediaFolderView = 'upload';
        na.cms.mediaFolder_viewChanged();
    }
    
    onclick_btnViewMedia (evt) {
        na.cms.settings.current.mediaFolderView = 'view';
        na.cms.mediaFolder_viewChanged();
    }
    
    onclick_mediaThumbnail (evt, basePath, filename) {
        var 
        arr = {
            misc : {
                folder : '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
            },
            cmsViewMedia : {
                basePath : basePath,
                filename : filename
            }
        },
        base64 = na.m.base64_encode_url(JSON.stringify(arr));
        na.site.loadContent(evt, base64);
    }
    
    mediaFolder_viewChanged () {
        var
        rec = na.cms.settings.current.selectedTreeNode,
        path = na.cms.currentPath(rec),
        path = path.replace(/ /g, '%20'),
        src = (
            na.cms.settings.current.mediaFolderView == 'upload'
            //? '/NicerAppWebOS/3rd-party/plupload-2.3.6/examples/jquery/jquery_ui_widget.php?changed='+na.m.changedDateTime_current()+/*'&smID='+siteManager.id+'&iid='+iid+'&dialogID='+did+*/'&basePath='+path
            ? '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/jquery_ui_widget.2.3.7.php?basePath='+path
            : '/NicerAppWebOS/logic.userInterface/photoAlbum/4.0.0/index.php?basePath='+path
        ),
        el = $('#jQueryFileUpload')[0];
        el.onload = na.cms.onresize;
        el.src = src;
    }
    
    treeButtonsEnableDisable (rec) {
        $('.jsTree_navBar_button').removeClass('disabled');
        if (rec && rec.original)
        switch (rec.original.type) {
            case 'naFolder':
                break;
            case 'naSystemFolder':
                $('#jsTree_addDocument').addClass('disabled');
                $('#jsTree_addFolder').addClass('disabled');
                $('#jsTree_addMediaAlbum').addClass('disabled');
                $('#jsTree_delete').addClass('disabled');
                break;
            case 'naUserRootFolder':
                break;
            case 'naGroupRootFolder':
                break;
            case 'naDocument':
                $('#jsTree_newDocument').addClass('disabled');
                break;
        }
    }
    
    tinymce_link_list (success) {
        var me = na.cms, s = me.settings, c = s.current, rel = jQuery(s.hid)[0];
        var x = c.db;
        var r = [
            /*
            { title : 'abc', menu : [
                { title : 'xyz' : value : 'url/to/page.html' }
            ]},
            { title : 'abd', menu : [] }
            */
        ];
//debugger;
        for (var i=0; i<c.db.length; i++) {
            var it = c.db[i];
            if (it.type=='naDocument') {
                // find the parents
                var
                j = i,
                it2 = c.db[j],
                title = it2.title,
                parentsLabel = it2.text,
                parentsURL = it2.text; // or it2.title

                while (it2.parent!=='#') {
                    for (var k=0; k<c.db.length; k++) {
                        var it3 = c.db[k];
                        if (it3.id && it3.id===it2.parent) {
                            parentsLabel = it3.text + '/' + parentsLabel;
                            parentsURL = it3.text + '/' + parentsURL;
                            break;
                        }
                    }

                    if (it3.parent==='#') {
                        var userOrGroupVal = it2.text;
                    }
                    it2 = it3;
                    if (it2.parent==='#') {
                        if (it2.text==='Users') var userOrGroup = 'user';
                        if (it2.text==='Groups') var userOrGroup = 'group';
                    }
                }


                var v = { // ---> the URL part of a final URL that lists as http://yoursite.com/apps/BASE64JSON-URL
                    cmsText : { // nicerapp app name
                        database : it.database,
                        id : it.id
                    }
                },
                tree = $('#jsTree').jstree(true),
                sel = tree.get_node(tree.get_selected()[0]),
                role = undefined,
                user = undefined;
                for (var j=0; j<sel.parents.length; j++) {
                    var p = tree.get_node(sel.parents[j]);
                    if (p.type == 'naUserRootFolder') user = p.text;
                    if (p.type == 'naGroupRootFolder') role = p.text;
                }
                var
                v = {
                    misc : {
                        folder : '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    },
                    cmsText : {
                        database : it.database,
                        id : it.id
                    }
                };
                if (user) v.cmsText.user = user;
                if (role) v.cmsText.role = role;

                var y = {
                    title : parentsLabel,
                    //value : '/apps/'+na.m.base64_encode_url( JSON.stringify(v) )
                    value : '/'+user+'/'+$('#nb_url1_dropdown_selected').html()+'/'+$('#nb_url2_value').val()
                };
                r.push (y);
            }
        };

        //debugger;
        na.m.waitForCondition('.floatpanel?', function() {
            return $('.mce-floatpanel').length > 0;
        }, function() {
            $('.mce-floatpanel').css({background:'rgba(0,0,0,0.55)', zIndex:900000});
        },50);
        success(r);
    }
    
    currentNode_createPath (path) {
        var 
        fncn = 'na.cms.currentNode_createPath("'+path+'")',
        me = na.cms, s = me.settings, c = s.current,
        tree = $('#jsTree').jstree(true),
        sel = tree.get_selected()[0],
        node = tree.get_node(sel),
        
        types = {
            naFolder : 'tree_btnCreateFolder',
            naMediaAlbum : 'tree_btnCreateMediaAlbum',
            naDocument : 'tree_btnCreateDocument'
        }//,
        //proceed = parseFloat(jQuery('#'+types[type]).css('opacity')) === 1;
        
        var
        t = tree.get_json(node),
        cur = t,
        paths = path.split('/');
        
        //if (paths.length==2) debugger;
        
        for (var i=0; i<paths.length; i++) {
            var 
            folderName = paths[i],
            d = cur.children,
            found = false;
            for (var j=0; j<d.length; j++) {
                if (d[j].text===folderName) {
                    found = true;
                    cur = d[j];
                }
            }
            na.cms.settings.refreshed = true;
            na.m.waitForCondition('na.cms.settings.refreshed', function() {
                return na.cms.settings.refreshed;
            }, function() {
                var
                found = false;
                for (var j=0; j<d.length; j++) {
                    if (d[j].text===folderName) {
                        found = true;
                        cur = d[j];
                    }
                }
                if (!found && folderName!=='thumbs') {
                    var
                    n2 = tree.get_node(cur.id),
                    n = {
                        database : n2.original.database,
                        id : na.m.randomString(),
                        type : 'naMediaAlbum',
                        text : folderName,
                        title : folderName,
                        parent : cur.id,
                        state : { opened : true }
                    },
                    r = tree.create_node (cur.id, n, 'last'),
                    url = '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/cmsManager/ajax_addNode.php',
                    ac = {
                        type : 'POST',
                        url : url,
                        data : {
                            database : n2.original.database,
                            id : r,
                            parent : cur.id,
                            label : folderName,
                            type : 'naMediaAlbum'
                        },
                        success (data, ts, xhr) {
                            //na.cms.refresh(function() {
                                //na.cms.settings.refreshed = true;
                            //});
                        },
                        error (xhr, textStatus, errorThrown) {
                            //na.cms.settings.refreshed = true;
                            //na.site.ajaxFail(fncn, url, xhr, textStatus, errorThrown);
                        }
                    };
                    //na.cms.settings.refreshed = false;
                    $.ajax(ac);

                    //debugger;
                    c.db[c.db.length] = node;
                    /*

                    na.m.waitForCondition('na.cms.settings.refreshed', function() {
                        return na.cms.settings.refreshed;
                    }, function() {
                        debugger;

                    }, 100);
                    */
                    return me.currentNode_createPath (path);
                }
            }, 100);
        }
        
        return true;
    }
    
    mediaUploadComplete (up, files) {
        na.cms.refresh(function(nodes) {
            var 
            me = na.cms, s = me.settings, c = s.current,
            id = files[0].relativePath.split('/')[1];
            
            for (var i=0; i<c.db.length; i++) {
                var it = c.db[i];
                if (it.text === id) {
                    $('#jsTree').jstree('deselect_all');
                    $('#jsTree').jstree('select_node', it.id);
                };
            };
            na.cms.onclick_btnViewMedia();
        }, false);
    }
}
