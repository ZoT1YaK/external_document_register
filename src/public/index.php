<?php
session_start();

if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true && empty($_GET['page'])){
    if (isset($_GET['download']) && !empty($_GET['download'])){
        include_once '../check/download.php';
        return;
    }

    header('Location: /?page=home');
}
else if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == false && empty($_GET['page'])){
    header('Location: /?page=login');
}
else {
    switch ($_GET['page']) {
        case 'home':
            include_once '../home/index.php';
            break;

        case 'login':
            include_once '../login/index.php';
            break;

        case 'check':
            include_once '../check/index.php';
            break;

        case 'register':
            include_once '../register/index.php';
            break;
            
        case 'login_function':
            include_once '../login/function.php';
            break;

        case 'check_function':
            include_once '../check/function.php';
            break;

        case 'check_list_function':
            include_once '../check/list_function.php';
            break;

        case 'check_list':
            include_once '../check/list.php';
            break;

        case 'check_view':
            include_once '../check/view.php';
            break;

        case 'check_approve_function':
            include_once '../check/approve_function.php';
            break;

        case 'register_function':
            include_once '../register/function.php';
            break;
            
        case 'register_view':
            include_once '../register/view.php';
            break;

        default:
            header('Location: /?page=login');
            break;
    }
}
