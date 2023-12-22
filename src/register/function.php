<?php
require_once __DIR__ . '/../sbapirequest.php';

$file_name = $_FILES["register_file"]["name"];

$target_dir = __DIR__ . "/../public/uploads/";
$target_file = $target_dir . basename($file_name);

if (move_uploaded_file($_FILES["register_file"]["tmp_name"], $target_file)) {
  echo "The file ". htmlspecialchars( basename( $file_name)). " has been uploaded.";
} else {
  $_SESSION['error_message'] = "Sorry, there was an error uploading your file.";
  header('Location: /?page=register');
}

$description = null;
if (! empty($_POST['register_description'])){
    $description = $_POST['register_description'];
}

$start_date = null;
if (! empty($_POST['register_start_date'])){
    $start_date = $_POST['register_start_date'];
}

$valid_to = null;
if (! empty($_POST['register_valid_to'])){
    $valid_to = $_POST['register_valid_to'];
}

$unlimited = null;
if (! empty($_POST['register_unlimited'])){
    $unlimited = ($_POST['register_unlimited'] == 'on' ? 1 : 0);
    $valid_to = null;
}

$doc_type = $_POST['register_doc_type'];

$file = file_get_contents($target_file); 
    
$file_base64 = base64_encode($file); 

if(unlink($target_file))
    echo "File Deleted.";

$xml  = '<object process="3167" group="10605">';
if(!empty($description)){
  $xml .= '<field name="o_doc_description">' . $description . '</field>';
}

if(!empty($start_date)){
  $xml .= '<field name="o_doc_start_date">' . $start_date . '</field>';
}

if(!empty($valid_to)){
  $xml .= '<field name="o_doc_validto">' . $valid_to . '</field>';
}

if(!empty($unlimited)){
  $xml .= '<field name="o_doc_perpetual">' . $unlimited . '</field>';
}

$xml .= '<field name="o_doc_type">' . $doc_type . '</field>';

$xml .= '<field name="o_file_name">' . $file_name . '</field>';
$xml .= '<file name="' . $file_name . '">' . $file_base64 . '</file>';
$xml .= '</object>';

$response = null;

try {
    $response = sbapi_request(3000, $xml);
} catch (\Throwable $th) {
    echo $th->getMessage();
}

$response = new SimpleXMLElement($response);

if((int)$response->header->error['id'] != 0){
  $error_code = (int)$response->header->error['id'];

  $error_msg = "Unspecific error <span class=\"error_message_highlight\">\"$error_code\"</span><br>";
  
  $_SESSION['error_message'] = $error_msg;

  header('Location: /?page=register');
}

$xml  = '<search>';

$xml .= '<field name="o_id" operator="=" value="' . (int)$response->body->object['id'] . '"/>';

$xml .= '</search>';

$xml .= '<data limit="1" total="off">';
$xml .= '<field name="o_num"/>';
$xml .= '</data>';

try {
    $response = sbapi_request(3020, $xml);
} catch (\Throwable $th) {
    echo $th->getMessage();
}

$response = new SimpleXMLElement($response);

header('Location: /?page=register_view&num=' . (string)$response->body->data->object->field);