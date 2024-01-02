<?php
    global $naWebOS;
    require_once ($naWebOS->basePath.'/NicerAppWebOS/domainConfigs/'.$naWebOS->domain.'/pageHeader.php');
?>
    <style>
        p {
            display : block;
        }
    </style>

    <h1 class="contentSectionTitle2">What is NicerApp WebOS?</h1><br/><br/><br/>

    <p>
    NicerApp WebOS is a CMS (Content Management System) and web-apps platform that embraces the latest web technologies to provide beautiful new views of your data and files, uses very few computer resources and minimal bandwidth, and avoids all complicated programming methods where-ever possible (especially in it's core code).<br/>
    Page owners and end-users can select any tiled or photo backgrounds, and even HD, 4K or 8K video backgrounds (courtesy of <a href="https://youtube.com" target="myThanksToYoutube" class="noPushState">YouTube.com</a>) to the content or apps.<br/>
    </p>

    <p><b>Theme settings</b> be selected by registered users, by hovering over the top-left part of any window and clicking on the painters palette icon that then appears.</p>

    <p>
    Fully legal automated daily download of new photo backgrounds according to a site operator's search criteria from <a href="https://images.google.com" class="noPushState" target="igc">Google Image Search</a> is finished and scheduled to get implemented for <a href="https://bing.com/images" class="noPushState" target="bi">Bing image search</a> at some point in the future.<br/>
    </p>

    <p>
    <b>NicerApp WebOS is completely free to use for all use-cases.</b><br/>
    Full <a href="/docs-license">MIT-licensed</a>[1] sources are available at <a href="https://github.com/NicerEnterprises/NicerApp-WebOS" target="githubNA" class="noPushState">https://github.com/NicerEnterprises/NicerApp-WebOS</a>.<br/>
    Documentation is available at <a href="https://github.com/NicerEnterprises/NicerApp-WebOS#readme" target="githubNAdocs" class="noPushState">https://github.com/NicerEnterprises/NicerApp-WebOS#readme</a>.<br/>
    The log of recent changes can be found <a href="https://github.com/NicerEnterprises/NicerApp-WebOS/commits/main" class="noPushState" target="naRecentChangesLog">here</a>.<br/>
    And the add-on packages (that are too large for github.com) can be found on <a href="https://tinyurl.com/NicerAppWebOS-bgs-favs-3" class="noPushState" target="GoogleDrive_NicerAppWebOS">https://tinyurl.com/NicerAppWebOS-bgs-favs-3</a>.<br/>
    </p>

    <!--
   <p>
   The user-interface components can be downloaded seperately starting somewhere in December 2022 via <a href="https://github.com/NicerEnterprises/STABLE-vividUI" class="noPushState" target="githubVividUI">https://github.com/NicerEnterprises/STABLE-vividUI</a>.
   </p>
   -->

   <p>
   The server side core code of NicerApp WebOS is written in PHP7+, and is designed to run on an <a href="https://ubuntu.com">ubuntu.com operating system</a> (and i recommend the kubuntu upgrade to that if you want to work with semi-transparent windows on your desktop and/or have the need for reliable desktop zoom-in and zoom-out capabilities), which are both free to use (even commercially).<br/>
   But with probably minimal adjustments, NicerApp WebOS will run on windows (using <a href="https://wampserver.com/en" class="noPushState" target="wscom">wampserver.com</a>), which includes the apache webserver, MySQL and PHP.
   </p>

   <p>
   Since the only real requirement for NicerApp WebOS is a web-server that runs PHP7+, it will probably run on MacOS and even smartphones as well. But this beyond my ability to test for, and this will remain the same. If you have bugfixes for such operating systems, <a href="mailto:rene.veerman.netherlands@gmail.com" class="noPushState">do email me about it</a> please.<br/>
   </p>

   <p>
   <b>The data storage philosophy for nicerapp is very flexible.</b><br/>
   Nearly all SQL database servers are supported by the included <span><a href="https://adodb.sourceforge.net" class="noPushState" target="asn">adodb.sourceforge.net</a></span> PHP library, and other <span><a href="https://www.google.com/search?q=different+database+types&oq=different+database+&aqs=chrome.2.69i57j0i512l9.5285j0j4&sourceid=chrome&ie=UTF-8" class="noPushState" target="exoticDBs">more exotic (cloud) database servers</a></span> are accessible via their PHP libraries as well (which usually use JSON or XML data supplied via a curl API call), or even <span><a href="https://www.google.com/search?q=webserver+programming+languages&ei=2omAYcPJBvnl7_UPjdSFqAU&oq=webserver+programming+languages&gs_lcp=Cgdnd3Mtd2l6EAMyBggAEAcQHjIICAAQCBAHEB4yCAgAEAgQBxAeMggIABAHEAUQHjIICAAQBxAFEB46BwgAEEcQsAM6BwgAELADEEM6BAgAEA06CAgAEA0QBRAeSgQIQRgAUPlfWKRtYIRwaAJwAngAgAGWAYgB_wKSAQM0LjGYAQCgAQHIAQrAAQE&sclient=gws-wiz&ved=0ahUKEwjDntG7ufjzAhX58rsIHQ1qAVUQ4dUDCA4&uact=5" class="noPushState" target="spls">via another server-side programming language than PHP</a></span>, or via C or <span><a href="https://www.cplusplus.com/" class="noPushState" target="cpp">C++</a></span> or C# as an extra-fast command-line app that gets called by PHP, <br/>
   but as a general pointer for web-development, <b>i recommend storing data in the JSON format</b>, because it is so much easier to work with than other forms of data storage for almost all web development purposes.<br/>
   The <span><a href="https://github.com/NicerAppWebOS/nicerapp#readme" target="githubNAdocs" class="noPushState">documentation for nicerapp</a></span> includes easy to follow instructions to set up a JSON database server powered by the free <span><a href="https://couchdb.apache.org" class="noPushState" target="cao">couchdb.apache.org</a></span> (<span><a href="https://docs.couchdb.org/en/stable/" class="noPushState" target="couchDocs">docs</a></span>), which is then accessed by PHP business logic (that you might write yourself) that uses the included <span><a href="https://github.com/NicerAppWebOS/sag">Sag</a></span> PHP library to access the couchdb server, and for which currently user-interfaces, businesslogic and middleware (Sag -> NicerApp WebOS) are being developed.<br/>
   One couchdb server can support multiple internet domains, each running a copy of nicerapp or any other software.<br/>
   </p>

   <p>
   JSON can also be stored on the server's filesystem if the data stored doesn't grow ridiculously large, as it does for instance for the news app, although even the news app these days stores it's data in couchdb to allow much longer stretches of time worth of news data to stay stored.
   </p>

   <p>
   Photo and video data is stored on the server's filesystem as well, and served directly as URLs through the webserver (most likely <span><a href="https://httpd.apache.org/" class="noPushState" target="apacheWebsite">Apache</a></span>, which runs on nearly every operating system).
   </p>
   <br/>

    <h2 class="contentSectionTitle2">A word on security</h2>
    <br/><br/>

    <p>
    I recommend that you use ubuntu PPAs that keep you up to date with the very latest versions of PHP, Apache, and any software you use through those access points.<br/>
    <span><a href="https://www.google.com/search?q=vulnerability+database&oq=vulnerability+data&aqs=chrome.0.0i512j69i57j0i512l8.4560j0j4&sourceid=chrome&ie=UTF-8" target="vdb" class="noPushState">New documented vulnerabilities are discovered many times per month</a></span>, and while the creators of widely used software do their best to publish the fixes to those vulnerabilities as soon as possible, using a default ubuntu installation means you lag behind these security updates by a considerable measure.<br/>
    Near-future documentation updates for nicerapp will include where to find reliable PPAs for all the Operating System components that your server uses and which face the outside world, aka get routed by your ADSL/fiber modem (and your LAN) to your server.<br/>
    </p>

    <p>
    NicerApp WebOS is hardened against a lot of user input attacks and abuse of business logic attacks, but i cannot guarantee at the moment that i've blocked all the possible attack vectors. There are MANY of these.<br/>
    </p>
