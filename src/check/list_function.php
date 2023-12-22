<?php
$cfg = require_once(__DIR__ . '/../config/i_default.php');

$cfg_custom = require_once(__DIR__ . '/../../i_custom.php');

foreach ($cfg_custom as $key => $value) {
    $cfg[$key] = $value;
}

if($_SESSION['username'] != $cfg['admin_login'] && $_SESSION['password'] != $cfg['admin_password']) {
    require_once __DIR__ . '/../sbapirequest.php';
    $elems_on_page = 15;
    // GET DATA FROM $_GET | $_POST IF EXIST
    if (! empty($_GET['sort'])){
        if (! empty($_SESSION['sort']) && $_SESSION['sort'] != $_GET['sort']) { 
            unset($_SESSION['list_page']);
        }
        $_SESSION['sort'] = $_GET['sort'];
    }

    if (! empty($_GET['order'])){
        if (! empty($_SESSION['order']) && $_SESSION['order'] != $_GET['order']) { 
            unset($_SESSION['list_page']);
        }
        $_SESSION['order'] = $_GET['order'];
    }

    if (! empty($_GET['list_page'])){
        $_SESSION['list_page'] = $_GET['list_page'];
    } 

    if ($_SESSION['sort'] != $_GET['sort']) { 
        unset($_SESSION['list_page']);
    }

    if(isset($_POST['check_number'])) {
        unset($_SESSION['list_page']);
    }

    if (! empty($_POST['check_number'])){
        $_SESSION['check_number'] = $_POST['check_number'];
    } else if (isset($_POST['check_number'])){
        unset($_SESSION['check_number']);
    }

    if (! empty($_POST['check_from'])){
        $_SESSION['check_from'] = $_POST['check_from'];
    } else if (isset($_POST['check_from'])){
        unset($_SESSION['check_from']);
    }

    if (! empty($_POST['check_to'])){
        $_SESSION['check_to'] = $_POST['check_to'];
    } else if (isset($_POST['check_to'])){
        unset($_SESSION['check_to']);
    }

    if (! empty($_POST['check_state'])){
        $_SESSION['check_state'] = $_POST['check_state'];
    } else if (isset($_POST['check_state'])){
        unset($_SESSION['check_state']);
    }

    // USE DATA FROM $_SESSION | $_GET | $_POST IF EXIST
    // If not empty, replace
    $limit = 9999;
    $number = 'operator="like" value="DOCS*"';
    $doc = '';
    if (! empty($_SESSION['check_number'])){
        $number = 'operator="=" value="' . $_SESSION['check_number'] . '"';
        $doc = $_SESSION['check_number'];
    }

    $from = null;
    if (! empty($_SESSION['check_from'])){
        $from = $_SESSION['check_from'];
    }

    $to = null;
    if (! empty($_SESSION['check_to'])){
        $to = $_SESSION['check_to'];
    }

    $state = null;
    if (! empty($_SESSION['check_state'])){
        $state = $_SESSION['check_state'];
    }

    $list_page = 1;
    if (! empty($_SESSION['list_page'])){
        $list_page = $_SESSION['list_page'];
    }

    $current_sort = 'num';
    if (! empty($_SESSION['sort'])){
        $current_sort = $_SESSION['sort'];
    }

    $current_order = 'asc';
    if (! empty($_SESSION['order'])){
        $current_order = $_SESSION['order'];
    }

    $sort_rules_for_num = '';
    if ($current_sort === 'num'){
        $sort_rules_for_num = 'sort="' . $current_order . '"';
    }

    $sort_rules_for_date = '';
    if ($current_sort === 'reg_date'){
        $sort_rules_for_date = 'sort="' . $current_order . '"';
    }

    $sort_rules_for_state = '';
    if ($current_sort === 'state'){
        $sort_rules_for_state = 'sort="' . $current_order . '"';
    }

    $sort_rules_for_author = '';
    if ($current_sort === 'author'){
        $sort_rules_for_author = 'sort="' . $current_order . '"';
    }

    $xml  = '<search>';
    // By status
    $xml .= '<field name="o_status" operator="in" value="[1;2;4]" />';
    // By number
    $xml .= '<field name="o_num" ' . $number . '/>';

    // By date
    if (!empty($from)){
        $xml .= '<field name="o_doc_reg_date" operator=">=" value="' . $from . '" />';
    }
    // By date
    if (!empty($to)){
        $xml .= '<field name="o_doc_reg_date" operator="<=" value="' . $to . '" />';
    }
    //by state
    if (!empty($state)){
        $xml .= '<field name="o_state" operator="=" value="' . $state . '" />';
    }

    $xml .= '</search>';

    $xml .= '<data limit="' . $limit . '" total="on">';
    $xml .= '<field name="o_num" ' . $sort_rules_for_num . '/>';
    $xml .= '<field name="o_state_name" ' . $sort_rules_for_state . '/>';
    $xml .= '<field name="o_doc_reg_date" ' . $sort_rules_for_date . '/>';
    $xml .= '<field name="o_author_name" ' . $sort_rules_for_author . '/>';
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

    $records = (int)$response->body->data["records"];

    if($records === 0){
        $error = "Documents ";

        if (!empty($doc)){
            $error .= "<span class=\"error_message_highlight\">\"$doc\"</span> ";
        } 
        if (!empty($from) && !empty($to)){
            $error .= "between dates: <span class=\"error_message_highlight\">\"$from - $to\"</span> ";
        } else if (!empty($from)){
            $error .= "from date: <span class=\"error_message_highlight\">\"$from\"</span> ";
        } else if (!empty($to)){
            $error .= "to date: <span class=\"error_message_highlight\">\"$to\"</span> ";
        } 
        if (!empty($state)){
            $state_name = '';
            switch ($state) {
                case '27769':
                    $state_name = 'New';
                    break;

                case '28873':
                    $state_name = 'Need approve';
                    break;

                case '27770':
                    $state_name = 'Active';
                    break;

                case '27771':
                    $state_name = 'Archived';
                    break;

                case '27772':
                    $state_name = 'Rejected';
                    break;
                
                default:
                    $state_name = '';
                    break;
            }
            $error .= "with state: <span class=\"error_message_highlight\">\"$state_name\"</span> ";
        }

        $error .= "does not exist <br>";

        $_SESSION['error_message'] = $error;

        header('Location: /?page=check');
    }

    $labels = ["Number", "State", "Reg. date", "Author"];

    $fields = [];
    $object_len = count($response->body->data->object);


    $last_list_page = ceil($records / $elems_on_page);

    $_SESSION['last_list_page'] = $last_list_page;

    if ($list_page > $last_list_page) {
        $list_page = $last_list_page;
    }

    if ($list_page < 1) {
        $list_page = 1;
    }

    if( $list_page === 1) {
        $start_from = 0;
    } else {
        $start_from = ($elems_on_page * $list_page - $elems_on_page);
    }

    if($records < $start_from + $elems_on_page) {
        $end = $records;
    } else {
        $end = $start_from + $elems_on_page;
    }

    for ($obj=$start_from; $obj < $end; $obj++) {
        $o_doc_reg_date = '';

        for ($field=0; $field < (count($response->body->data->object[$obj]->field)); $field++) {
            switch ((string)$response->body->data->object[$obj]->field[$field]['name']) {
                case 'o_doc_reg_date':
                    $o_doc_reg_date = (string)$response->body->data->object[$obj]->field[$field];
                    break;
                
                default:
                    # code...
                    break;
            }
            
            $fields[$obj][$field]['label'] = $labels[$field];
            $fields[$obj][$field]['value'] = (string)$response->body->data->object[$obj]->field[$field];
            $fields[$obj][$field]['type'] = "field";
        }
        
        
        
        if(($obj) === 0) {
            $_SESSION['first_object'] = (int)$response->body->data->object[$obj]['id'];
            $_SESSION['first_object_date'] = $o_doc_reg_date;
        }

        if(($obj + 1) === $object_len) {
            $_SESSION['last_object'] = (int)$response->body->data->object[$obj]['id'];
            $_SESSION['last_object_date'] = $o_doc_reg_date;
        }
    }

    $_SESSION['documents'] = $fields;
} else {
    $content = file_get_contents(__DIR__ . "/documents.json");

    $_SESSION['documents'] = json_decode($content, true);
}

header('Location: /?page=check_list');
