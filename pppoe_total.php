<?php
require('routeros_api.class.php');
header('Content-Type: application/json');

$API = new RouterosAPI();
$host = '0.0.0.0'; // IP Mikrotik
$user = 'username'; // Username
$pass = 'password'; // Password

if ($API->connect($host, $user, $pass)) {
    $API->write('/ppp/secret/print');
    $secrets = $API->read();

    $API->write('/ppp/active/print');
    $active = $API->read();

    $API->disconnect();

    $onlineUsers = array_map(fn($item) => $item['name'], $active);

    $allUsers = [];
    $enabledUsers = [];
    $disabledUsers = [];

    foreach ($secrets as $user) {
        $name = $user['name'];
        $allUsers[] = $name;

        if ($user['disabled'] === 'true') {
            $disabledUsers[] = $name;
        } else {
            $enabledUsers[] = $name;
        }
    }

    $offlineUsers = array_values(array_diff($enabledUsers, $onlineUsers));

    echo json_encode([
        'status' => 'success',
        'total_user' => count($allUsers),
        'total_online' => count($onlineUsers),
        'total_offline' => count($offlineUsers),
        'total_disabled' => count($disabledUsers),
        'online_users' => $onlineUsers,
        'offline_users' => $offlineUsers,
        'disabled_users' => $disabledUsers
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to connect to MikroTik'
    ]);
}
?>
