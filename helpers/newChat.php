<?php
    require_once "../classes/DB.class.php";
    require_once "Lib.php";

    sessionStart();
    
    //check input
    if( empty($_GET) || !array_key_exists('msg', $_GET) || !checkSession() || count($_GET['msg']) == 0 )
    {
        die();
    }
    
    $db = new DB();

    echo $db -> addChat( $_SESSION['room'], $_SESSION['username'], $_GET['msg'] );
?>