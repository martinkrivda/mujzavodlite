<?php
$config = parse_ini_file(__DIR__ . '/config.ini', true); // Assuming your php is in the root directory of your web server, so placing the file where it can't be seen by prying eyes!
$host = $config['database']['host'];
$port = $config['database']['port'];
$user = $config['database']['user'];
$password = $config['database']['password'];
$database = $config['database']['database'];
// pripojeni do db na serveru endora
$conn = new PDO(sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8', $host, $port, $database), $user, $password);
// vraci vyjimky v pripade neplatneho SQL vyrazu
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>