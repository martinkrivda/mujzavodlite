<?php
require_once 'google-api/vendor/autoload.php';
$config = parse_ini_file(__DIR__ . '/config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
$clientId = $config['oauthGoogle']['clientId'];
$secret = $config['oauthGoogle']['secret'];
$gClient = new Google_Client();
$gClient->setClientId($clientId);
$gClient->setClientSecret($secret);
$gClient->setApplicationName("MůjZávod");
$gClient->setRedirectUri("https://" . $_SERVER['HTTP_HOST'] . "/chytryoddil/mujzavodtest/g-callback.php");
$gClient->addScope("https://www.googleapis.com/auth/plus.login");
$gClient->addScope("https://www.googleapis.com/auth/userinfo.email");
?>