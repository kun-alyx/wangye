<?php
error_reporting(0);
Header("Content-Type: image/jpeg");

// Get the URL from the POST request
if (isset($_POST['url'])) {
    $url = $_POST['url'];

    // Get IP
    $ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : 
          (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : 
          $_SERVER['REMOTE_ADDR']);

    // Time
    $actual_time = time();
    $actual_day = date('Y.m.d', $actual_time);
    $actual_hour = date('H:i:s', $actual_time);

    // Get Browser
    $browser = $_SERVER['HTTP_USER_AGENT'];

    // Log
    $myFile = "log.txt";
    $fh = fopen($myFile, 'a+');
    $stringData = $actual_day . ' ' . $actual_hour . ' ' . $ip . ' ' . $browser . ' ' . $url . "\r\n";
    fwrite($fh, $stringData);
    fclose($fh);
}

// Generate Image
$newimage = ImageCreate(1,1);
$grigio = ImageColorAllocate($newimage, 255, 255, 255);
ImageJPEG($newimage);
ImageDestroy($newimage);
?>
