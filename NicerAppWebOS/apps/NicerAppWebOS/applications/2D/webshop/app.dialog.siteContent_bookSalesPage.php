<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);    

require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/../../../userInterface/photoAlbum/4.0.0/functions.php');
$view = json_decode (base64_decode_url($_GET['apps']), true);

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


global $naWebOS;
$naWebOS = new NicerAppWebOS();
$naWebOS->init();

//$file = realpath(dirname(__FILE__).'/../../../').'/domainConfigs/'.$naWebOS->domain.'/app.'.$view['diskText']['id'].'.dialog.siteContent.php';
//echo execPHP($file);

$ad = $view['app.2D.webshop']; // appDetails

echo '<pre>'; var_dump ($ad); echo '</pre>';


?>
<h1>app.dialog.siteContent_bookSalesPage.php</h1>
<h2>Book : <a href="http://amazonOrGoogleBooksOrOtherMajorDistributor.com"><?php echo $ad['bookTitle'];?></a><br/><span class="app_2D_webshop__bookSubTitle"><?php echo $ad['bookSubTitle'];?></span></h2>
<h2>Author : <a href="https://www.facebook.com/gavanHoverswell"><?php echo $ad['author'];?></a>.</h2>
