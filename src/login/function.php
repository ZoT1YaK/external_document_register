<?php
function deleteDirectory($directoryPath) {
    $thresholdTimestamp = strtotime('-1 days');
    if (is_dir($directoryPath)) {
        $contents = scandir($directoryPath);
        foreach ($contents as $item) {
            if ($item !== '.' && $item !== '..') {
                $itemPath = $directoryPath . DIRECTORY_SEPARATOR . $item;
                if (is_dir($itemPath)) {
                    deleteDirectory($itemPath);
                } else {
                    $directoryModificationTime = filemtime($itemPath);

                    $file_name = explode('\\', $itemPath);

                    if(is_file($itemPath) && $file_name[count($file_name) - 1] != 'empty' && $directoryModificationTime < $thresholdTimestamp){
                        unlink($itemPath);
                    }
                }
            }
        }

        $contents = scandir($directoryPath);

        if(count($contents) == 2){
            rmdir($directoryPath);
        }
    } else {
        die("Directory $directoryPath does not exist.");
    }
}

require_once __DIR__ . '/../sbapirequest.php';

$cfg = require(__DIR__ . '/../config/i_default.php');

$cfg_custom = require(__DIR__ . '/../../i_custom.php');

foreach ($cfg_custom as $key => $value) {
    $cfg[$key] = $value;
}

$_SESSION['username'] = $_POST["username"];

$_SESSION['password'] = $_POST["password"];

$xml = "";

$response = null;

if($_SESSION['username'] != $cfg['admin_login'] && $_SESSION['password'] != $cfg['admin_password']) {
    try {
        $response = sbapi_request(9000, $xml);
    } catch (\Throwable $th) {
    
        echo $th->getMessage();
    }
    
    $response = new SimpleXMLElement($response);
    
    if((int)$response->header->error['id'] != 0){
        $_SESSION['error_code'] = (string)$response->header->error['id'] . '<br>';
    
        $_SESSION['error_message'] = "Access denied <br>";
    
        unset($_SESSION['userame']);
        unset($_SESSION['password']);
    
        header('Location: /?page=login');
    }

    $xml  = '<search dictionary="4360">';

    $xml .= '<field name="d_status" operator="=" value="1" />';

    $xml .= '</search>';

    $xml .= '<data limit="10" total="off" picture="off">';

    $xml .= '<field name="d_name" />';

    $xml .= '</data>';

    try {
        $response = sbapi_request(3100, $xml);
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }

    $response = new SimpleXMLElement($response);

    $document_types = [];

    for ($i=0; $i < count($response->body->data->element); $i++) { 
        $document_types[$i]['id'] = (int)$response->body->data->element[$i]['id'];
        $document_types[$i]['name'] = (string)$response->body->data->element[$i]->field;
    }

    $_SESSION['document_types'] = $document_types;
} else {
    $content = file_get_contents(__DIR__ . "/document_types.json");

    $_SESSION['document_types'] = json_decode($content, true);
}

$thresholdTimestamp = strtotime('-1 days');

$directoryPath_uploads = __DIR__ . '/../public/uploads/';
$directoryPath_convertedFiles = __DIR__ . '/../public/convertedFiles/';

$files_uploads = glob($directoryPath_uploads . '*');

foreach ($files_uploads as $file) {
    $file_name = explode('/', $file);
    $directoryModificationTime = filemtime($file);
    
    if (is_file($file) && $file_name[count($file_name) - 1] != 'empty' && $directoryModificationTime < $thresholdTimestamp) {
        unlink($file);
    }
}

deleteDirectory($directoryPath_convertedFiles);

$_SESSION['logged_in'] = true;
header('Location: /?page=home');
