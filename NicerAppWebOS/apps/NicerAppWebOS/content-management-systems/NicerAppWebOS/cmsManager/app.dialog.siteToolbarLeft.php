<?php 
$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/
?>
<div class="lds-facebook"><!-- thanks for allowing CC0 license usage : https://loading.io/css/ --><div></div><div></div><div></div></div> 
<link rel="stylesheet" href="/NicerAppWebOS/3rd-party/jsTree-3.3.15/dist/themes/default/style.css" /> <!-- has style.min.css -->
<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS/usersGroupsManager/na.usersGroupsManager.source.js"></script>
<div id="jsTree_navBar">
    <div id="btnAddUser_menu" class="vividMenu noInitialShowing" style="display:none;position:absolute;height:100px; z-index:800000" type="vertical">
    <ul id="btnAddUser_menu_ul" class="vividMenu_mainUL" style="display:none;">
        <li><a href="javascript:na.ugm.onclick_btnAddUser(event)" class="noPushState">Add</a></li>
        <li><a href="javascript:na.ugm.onclick_btnRemoveUser(event)" class="noPushState">Remove</a></li>
    </ul>
    </div>
<?php
    global $naWebOS;
    echo $naWebOS->html_vividButton (
        1, 'width:100%;height:50px;',

        'btnAddUser',
        'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
        '',
        'na.ugm.onclick_btnAddUser(event)',
        'na.site.settings.menus[\'#btnAddUser_menu\'].showMenu(null, true)',
        '',

        403, 'Add user',

        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'raw_icons/icons_people/person-man.png',//.$naWebOS->dbs->userIDicon,

        '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/NicerAppWebOS/siteMedia/btnPlus_shaded.png" style="position:absolute;left:calc(50px - 25px);width:25px;z-index:2021;"/>',

        null, null, null
    );
    echo $naWebOS->html_vividButton (
        1, 'width:100%;',

        'btnAddGroup',
        'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
        '',
        'na.ugm.onclick_btnAddGroup(event)',
        '',
        '',

        404, 'Add group',

        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'raw_icons/icons_people/logp.png',

        '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/NicerAppWebOS/siteMedia/btnPlus_shaded.png" style="position:absolute;left:calc(50px - 25px);width:25px;z-index:2021;"/>',

        null, null, null
    );

    echo $naWebOS->html_vividButton (
        1, 'width:100%;',

        'btnAddFolder',
        'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
        '',
        'na.cms.onclick_btnAddFolder(event)',
        '',
        '',

        400, 'Add folder',

        'btnCssVividButton_outerBorder.png',
        'btnAddFolder.png',
        null,//'btnCssVividButton_iconBackground.png',
        null,//'raw_icons/icons_people/person-man.png',//.$naWebOS->dbs->userIDicon,

        '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/NicerAppWebOS/siteMedia/btnPlus_shaded.png" style="position:absolute;left:calc(50px - 25px);width:25px;z-index:2021;"/>',

        null, null, null
    );
    echo $naWebOS->html_vividButton (
        1, 'width:100%;',

        'btnAddDocument',
        'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
        '',
        'na.cms.onclick_btnAddDocument(event)',
        '',
        '',

        401, 'Add document',

        'btnCssVividButton_outerBorder.png',
        'btnAddDocument.png',
        null,//'btnCssVividButton_iconBackground.png',
        null,//'raw_icons/icons_people/jk.png',

        '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/NicerAppWebOS/siteMedia/btnPlus_shaded.png" style="position:absolute;left:calc(50px - 25px);width:25px;z-index:2021;"/>',

        null, null, null
    );
    echo $naWebOS->html_vividButton (
        1, 'width:100%;',

        'btnAddMediaAlbum',
        'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
        '',
        'na.cms.onclick_btnAddMediaAlbum(event)',
        '',
        '',

        402, 'Add media album',

        'btnCssVividButton_outerBorder.png',
        'btnAddMediaFolder.png',
        null,//'btnCssVividButton_iconBackground.png',
        null,//'btnPlus.png',

        '<img class="vividButton_icon_imgButtonIcon_50x50_sup1" srcPreload="/NicerAppWebOS/siteMedia/btnPlus_shaded.png" style="position:absolute;left:calc(50px - 25px);width:25px;z-index:2021;"/>',

        null, null, null
    );


    echo $naWebOS->html_vividButton (
        1, 'width:100%;',

        'btnDeleteRecord',
        'vividButton_icon_50x50 grouped btnOptions', '_50x50', 'grouped',
        '',
        'na.cms.onclick_btnDeleteRecord(event)',
        '',
        '',

        406, 'Delete (CAREFUL!)',

        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.yellow4a.png',
        null,//'btnCssVividButton_iconBackground.png',
        'btnTrashcan_red_500x500.png',

        '',

        null, null, null
    );
?>
</div>
<div id="jsTree"></div>
<script type="text/javascript">
    $(document).ready(function() {
    //setTimeout (function () {
        na.desktop.settings.visibleDivs.push('#siteToolbarLeft');
                $('#btnAddUser_menu').addClass('noInitialShowing');
                $('#btnAddUser_menu').css({
                    top : $('#btnAddUser_menu').offset().top + $('#btnAddUser_menu').height() + 10,
                    left : $('#btnAddUser_menu').offset().left + ($('#btnAddUser_menu').width() * 0.62),
                    width : $('#btnAddUser_menu').width()
                });

        $('#siteToolbarLeft .vividButton4, #siteToolbarLeft .vividButton, #siteToolbarLeft .vividButton_icon_50x50').each(function(idx,el){
            na.site.settings.buttons['#'+el.id] = new naVividButton(el);
        });

        //na.site.onresize();
        //na.cms.onload();
    //}, 250);
    });
</script>

