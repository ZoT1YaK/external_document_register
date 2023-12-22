<?php
if($_SESSION['username'] != 'mm.admin' && $_SESSION['password'] != 'mm.password') {
    require_once __DIR__ . '/../sbapirequest.php';

    if(!empty($_POST["select_document"])) {
        $_SESSION["select_document"] = $_POST["select_document"];
    }

    $num = $_SESSION["select_document"];

    $xml  = '<search>';
    $xml .= '<field name="o_num" operator="=" value="' . $num . '" />';
    $xml .= '</search>';
    $xml .= '<data limit="10" total="on">';
    $xml .= '<field name="o_state.bps_name"/>';
    $xml .= '<field name="o_num"/>';
    $xml .= '<field name="o_author_name"/>';
    $xml .= '<field name="o_doc_reg_date"/>';
    $xml .= '<field name="o_ext_number"/>';
    $xml .= '<field name="o_doc_type.d_name"/>';
    $xml .= '<field name="o_doc_description"/>';
    $xml .= '<field name="o_file_name"/>';
    $xml .= '<file name="*" operator="exist" />';
    $xml .= '</data>';

    $response = null;

    try {
        $response = sbapi_request(3020, $xml);
    } catch (\Throwable $th) {
        echo $th->getMessage();
    }

    $response = new SimpleXMLElement($response);

    if((int)$response->header->error['id'] != 0){
        $_SESSION['error_code'] = (string)$response->header->error['id'] . '<br>';
        $_SESSION['error_message'] = (string)$response->header->error['text'] . '<br>';
        
        header('Location: /?page=check');
    }

    if((string)$response->body->data["records"] == "0"){
        $_SESSION['error_message'] = "Document <span class=\"error_message_highlight\">\"$num\"</span> does not exist <br>";
        header('Location: /?page=check');
    }

    $labels = ["Stage", "Document number", "Author", "Registration date", "External number", "Document type", "Document description", "File"];
    $fields = [];

    for ($obj=0; $obj < count($response->body->data->object); $obj++) { 
        
        for ($field=0; $field < count($response->body->data->object[$obj]->field); $field++) { 

            $fields[$obj][$field]['label'] = $labels[$field];
            $fields[$obj][$field]['value'] = (string)$response->body->data->object[$obj]->field[$field];
            $fields[$obj][$field]['type'] = "field";

            if($labels[$field] == 'File'){
                $fields[$obj][$field]['label'] = (string)$response->body->data->object[$obj]->field[$field];
                $fields[$obj][$field]['value'] = $num . '-' . $obj;
                $fields[$obj][$field]['type'] = "file";

                $_SESSION['files'][$num . '-' . $obj]['base64'] = (string)$response->body->data->object[$obj]->file;
                $_SESSION['files'][$num . '-' . $obj]['name'] = $fields[$obj][$field]['label'];

                $files = scandir(__DIR__ . '/../public/uploads');

                $files = array_diff($files, array('.', '..'));

                $fileExist = false;

                foreach ($files as $file) {
                    if($file == $fields[$obj][$field]['label']){
                        $file_contents = file_get_contents(__DIR__ . '/../public/uploads/' . $file);

                        if((string)base64_encode($file_contents) == (string)$response->body->data->object[$obj]->file) {
                            $fileExist = true;
                        }
                    };
                }

                if(!$fileExist){
                    $decodedData = base64_decode((string)$response->body->data->object[$obj]->file);
                    $filePath = __DIR__ . '/../public/uploads/'. $fields[$obj][$field]['label'];
                    file_put_contents($filePath, $decodedData);
                }
            }
        }
    }

    $_SESSION['object_id'] = (string)$response->body->data->object['id'];

    $_SESSION['document'] = $fields;
} else {
    $content = file_get_contents(__DIR__ . "/view_document.json");
    
    $_SESSION['document'] = json_decode($content, true);
}

header('Location: /?page=check_view');
