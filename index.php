<?php

error_reporting(0);

Header("Content-Type: image/jpeg");

 

//Get IP

if (!empty($_SERVER['HTTP_CLIENT_IP']))

{

  $ip=$_SERVER['HTTP_CLIENT_IP'];

}

elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))

{

  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];

}

else

{

  $ip=$_SERVER['REMOTE_ADDR'];

}

date_default_timezone_set('Asia/Shanghai');


//Time

$actual_time = time();

$actual_day = date('Y.m.d', $actual_time);

$actual_day_chart = date('d/m/y', $actual_time);

$actual_hour = date('H:i:s', $actual_time);

 
// 获取手机型号
function getDeviceModel($userAgent) {
    if (preg_match('/Mobile|Android|iPhone|iPad|Windows Phone/i', $userAgent)) {
        return $userAgent;
    }
    return 'Unknown Device';
}
//GET Browser

$browser = $_SERVER['HTTP_USER_AGENT'];
$deviceModel = getDeviceModel($browser);
//物理地址
function getGeoLocation($ip) {
    $url = "http://ip-api.com/json/{$ip}";
    $response = file_get_contents($url);
    return json_decode($response, true);
}    
$geoData = getGeoLocation($ip);

$country = $geoData['country'] ?? 'Unknown Country';
$region = $geoData['regionName'] ?? 'Unknown Region';
$city = $geoData['city'] ?? 'Unknown City';
//其他
$serverName = $_SERVER['SERVER_NAME'];
$serverSoftware = $_SERVER['SERVER_SOFTWARE'];
$requestMethod = $_SERVER['REQUEST_METHOD'];
$remoteAddr = $_SERVER['REMOTE_ADDR'];
$queryString = $_SERVER['QUERY_STRING'];
$httpReferer = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'No Referer';
$remotePort = $_SERVER['REMOTE_PORT'];
$requestUri = $_SERVER['REQUEST_URI'];
$httpAccept = $_SERVER['HTTP_ACCEPT'];
$httpAcceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
$httpCookie = !empty($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : 'No Cookies';

//LOG

$myFile = "log.txt";

$fh = fopen($myFile, 'a+');

//$stringData = $actual_day . ' ' . $actual_hour . ' ' . $ip . ' ' . $browser . ' ' . "\r\n";
$stringData = sprintf(
    "%s %s | IP: %s | Device: %s | Browser: %s | Country: %s | Region: %s | City: %s\r\n",
    $actual_day, 
    $actual_hour, 
    $ip, 
    $deviceModel, 
    $browser, 
    $country, 
    $region, 
    $city
);
fwrite($fh, $stringData);

fclose($fh);

 

//Generate Image (Es. dimesion is 1x1)
$imagePath = '0.png'; 
$newimage = imagecreatefrompng($imagePath); 

// 确保透明度处理
imagealphablending($newimage, false);
imagesavealpha($newimage, true);

// 设置 PNG 头
header('Content-Type: image/png');

// 直接输出图片，无损压缩
imagepng($newimage, null, 0);



?>