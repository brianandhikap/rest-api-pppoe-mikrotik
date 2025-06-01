<?php
require('routeros_api.class.php');
header('Content-Type: application/json');

$API = new RouterosAPI();
$host = '0.0.0.0'; // IP Mikrotik
$user = 'username'; // Username
$pass = 'password'; // Password

if ($API->connect($host, $user, $pass)) {
    $API->write('/ppp/active/print');
    $read = $API->read();
    $API->disconnect();

    echo json_encode([
        'status' => 'success',
        'data' => $read
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to connect to MikroTik API'
    ]);
}
?>
