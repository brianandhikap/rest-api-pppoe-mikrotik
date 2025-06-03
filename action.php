<?php
require('routeros_api.class.php');
header('Content-Type: application/json');

$API = new RouterosAPI();
$host = '0.0.0.0'; // IP Mikrotik
$user = 'username'; // Username
$pass = 'password'; // Password

$API->debug = false;

$action = null;

if (isset($_GET['disable_secret'])) {
    $action = 'disable';
    $param1 = $_GET['disable_secret'];
} elseif (isset($_GET['enable_secret'])) {
    $action = 'enable';
    $param1 = $_GET['enable_secret'];
} elseif (isset($_GET['add_secret'])) {
    $action = 'add_secret';
    $param1 = $_GET['add_secret'];
} elseif (isset($_GET['edit_profile'])) {
    $action = 'edit_profile';
    $parts = explode('|', $_GET['edit_profile']);
    if (count($parts) !== 2) {
        echo json_encode(['status' => 'error', 'message' => 'Format salah. Gunakan ?edit_profile=ProfileBaru|NamaUser']);
        exit;
    }
    $param1 = $parts[0];
    $param2 = $parts[1];
} elseif (isset($_GET['add_profile'])) {
    $action = 'add_profile';
    $param1 = $_GET['add_profile'];
} elseif (isset($_GET['remove_profile'])) {
    $action = 'remove_profile';
    $param1 = $_GET['remove_profile'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid']);
    exit;
}

if ($API->connect($host, $user, $pass)) {
    switch ($action) {
        case 'disable':
        case 'enable':
            $API->write('/ppp/secret/print', false);
            $API->write('?name=' . $param1);
            $result = $API->read();

            if (!empty($result)) {
                $id = $result[0]['.id'];
                $API->write("/ppp/secret/$action", false);
                $API->write("=.id=$id");
                $API->read();

                echo json_encode(['status' => 'success', 'message' => "User $param1 berhasil di-$action"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "User $param1 tidak ditemukan"]);
            }
            break;

        case isset($_GET['add_secret']):
            $raw = $_GET['add_secret'];
            $parts = explode('|', $raw);

            $data = [];
            foreach ($parts as $part) {
                if (strpos($part, '=') !== false) {
                    list($key, $value) = explode('=', $part, 2);
                    $data[$key] = $value;
                } else {
                    $data['name'] = $part;
                }
            }

            if (empty($data['name']) || empty($data['password']) || empty($data['service']) || empty($data['profile'])) {
                echo json_encode(['status' => 'error', 'message' => 'Parameter tidak valid']);
                break;
            }

            $API->write('/ppp/secret/add', false);
            $API->write('=name=' . $data['name'], false);
            $API->write('=password=' . $data['password'], false);
            $API->write('=service=' . $data['service'], false);
            $API->write('=profile=' . $data['profile']);
            $API->read();

            echo json_encode(['status' => 'success', 'message' => 'Secret berhasil ditambahkan']);
            break;

        case 'edit_profile':
            $newProfile = $param1;
            $username = $param2;

            $API->write('/ppp/secret/print', false);
            $API->write('?name=' . $username);
            $result = $API->read();

            if (!empty($result)) {
                $id = $result[0]['.id'];

                $API->write('/ppp/secret/set', false);
                $API->write('=.id=' . $id, false);
                $API->write('=profile=' . $newProfile);
                $API->read();

                echo json_encode([
                    'status' => 'success',
                    'message' => "Profile user $username berhasil diubah ke $newProfile"
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "User $username tidak ditemukan"]);
            }
            break;

        case 'add_profile':
        case isset($_GET['add_profile']):
            $parts = explode('|', $_GET['add_profile']);
            if (count($parts) !== 4) {
                echo json_encode(['status' => 'error', 'message' => 'Format salah. Gunakan ?add_profile=Nama|local=Pool|remote=Pool|RateLimit']);
                break;
            }

            $name = $parts[0];
            $local = str_replace('local=', '', $parts[1]);
            $remote = str_replace('remote=', '', $parts[2]);
            $rate = $parts[3];

            $API->write('/ppp/profile/add', false);
            $API->write('=name=' . $name, false);
            $API->write('=local-address=' . $local, false);
            $API->write('=remote-address=' . $remote, false);
            $API->write('=rate-limit=' . $rate);
            $API->read();

            echo json_encode(['status' => 'success', 'message' => "Profile $name berhasil ditambahkan"]);
            break;

        case 'remove_profile':
            $API->write('/ppp/profile/print', false);
            $API->write('?name=' . $param1);
            $result = $API->read();

            if (!empty($result)) {
                $id = $result[0]['.id'];
                $API->write('/ppp/profile/remove', false);
                $API->write('=.id=' . $id);
                $API->read();

                echo json_encode(['status' => 'success', 'message' => "Profile $param1 berhasil dihapus"]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Profile $param1 tidak ditemukan"]);
            }
            break;
    }

    $API->disconnect();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gagal terhubung ke Mikrotik']);
}
