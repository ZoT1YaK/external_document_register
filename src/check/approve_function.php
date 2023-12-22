<?php
require_once __DIR__ . '/../sbapirequest.php';

$object_id = $_GET['obj_id'];

$new_state = 27770;

if ($_GET['action'] === 'decline') {
    $new_state = 27769;
}

$xml = '<object id="' . $object_id . '" old="28873" new="' . $new_state . '" />';

$response = null;

try {
    $response = sbapi_request(3010, $xml);
} catch (\Throwable $th) {
    echo $th->getMessage();
}

$response = new SimpleXMLElement($response);

if((int)$response->header->error['id'] != 0){
    $_SESSION['error_code'] = (int)$response->header->error['id'];
    $error = (string)$response->header->error['text'] . '<br>';

    if($_SESSION['error_code'] === 4003) {
        $error = "Document already approved or declined <br>";
    }

    $_SESSION['error_message'] = $error;
}

header('Location: /?page=check_function');