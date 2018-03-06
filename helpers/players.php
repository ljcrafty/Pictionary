<?php
    /**
     * This file counts the number of users within a room, or the amount of users in a room
     * that are not at the given stage
    */
    require_once "Lib.php";
    require_once "../classes/DB.class.php";

    sessionStart();
    
    //check super globals
    if( !checkSession() || !array_key_exists('room', $_SESSION) )
    {
        dies("An error occurred.");
    }

    $db = new DB();
    $data = $db -> numPlayers( $_SESSION['room'], (array_key_exists('stage', $_GET) ? $_GET['stage'] : '') );

    //validate input
    if( $data == -1 )
    {
        dies('An error occurred');
    }
    
    echo $data;
?>