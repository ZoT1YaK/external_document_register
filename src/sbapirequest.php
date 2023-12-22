<?php
function sbapi_request($msg_type, $hml_body){
    $cfg = require(__DIR__ . '/config/i_default.php');

    $cfg_custom = require(__DIR__ . '/../i_custom.php');

    foreach ($cfg_custom as $key => $value) {
        $cfg[$key] = $value;
    }
    
    if(empty($_SESSION['username'])){
        throw new Exception("Error Failed to process authorization", 2);
    }
    
    if(empty($_SESSION['password'])){
        throw new Exception("Error Failed to process authorization", 3);
    }
    
    $username = $_SESSION['username'];

    $password = sha1($_SESSION['password']);
    
    $created = date("Y-m-d") . "T" . date("h:i:s") . "Z";
    
    $authdata = '<authdata user="'.$username.'" password="'.$password.'" user_ip="'. $cfg['sbapi_ip'] .'" />';
    $http_body  = '<?xml version="1.0" encoding="UTF-8"?>';
    $http_body .= '<root>';
    $http_body .= '<header>';
    $http_body .= '<interface>' . $cfg['sbapi_iid'] . '</interface>';
    $http_body .= '<message>' . $msg_type . '<message/>';
    $http_body .= '<auth pwd="hash">'.base64_encode($authdata).'</auth>';
    $http_body .= '</header>';
    $http_body .= '<body>';
    $http_body .= $hml_body;
    $http_body .= '</body>';
    $http_body .= '</root>';

    // Create an array of options for the stream context
    $options = [
        'http' => [
        'method' => 'POST',
        'header' => "Content-type: text/xml\r\n",
        'content' => $http_body,
        ]
    ];

    // Create a stream context from the options array
    $context = stream_context_create($options);
    
    // Use file_get_contents to send the request and get the response
    $response = file_get_contents($cfg['sbapi_url'], false, $context);
    
    return $response;
}