<?php 

$cfg = require(__DIR__ . '/../config/i_default.php');

$cfg_custom = require(__DIR__ . '/../../i_custom.php');

foreach ($cfg_custom as $key => $value) {
    $cfg[$key] = $value;
}

if ($_SESSION['username'] != $cfg['admin_login'] && $_SESSION['password'] != $cfg['admin_password']) {
    if(isset($_SESSION['files']) && isset($_SESSION['files'][$_GET['download']])){
        $base64 = $_SESSION['files'][$_GET['download']]['base64'];
        $name = $_SESSION['files'][$_GET['download']]['name'];
        $decoded = base64_decode($base64);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment;filename="' . $name . '"');

        echo $decoded;
        die;
    }
} else {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    
    $name = "diagram.jpg";
    $decoded = file_get_contents(__DIR__ . '/../public/diagram.jpg');
    
    header('Content-Disposition: attachment;filename="' . $name . '"');
    
    echo $decoded;
    die;
}
