<?php
    require_once "Lib.php";
    require_once "../classes/DB.class.php";

    //TODO: it isn't deleting the rows in the DB

    sessionStart();

    if( !checkSession() || !array_key_exists("stage", $_SESSION) || !array_key_exists("room", $_SESSION) )
    {
        sessionDestroy();
    }

    $db = new DB();

    //validate username, stage and room
    if( !$db -> validateRoom($_SESSION['room']) || !$db -> validateUser($_SESSION['user'], $_SESSION['room']) )
    {
        sessionDestroy();
    }

    //remove data from DB
    if( $_SESSION['stage'] == "drawn" || $_SESSION['stage'] == "guessed" )
    {
        //if they have already drawn or guessed, keep their data, just mark them as forfeit
        $db -> forfeit( $_SESSION['username'], $_SESSION['room'] );
    }
    else
    {
        $db -> deleteUser( $_SESSION['username'], $_SESSION['room'] );
    }

    sessionDestroy();
?>