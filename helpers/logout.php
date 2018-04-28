<?php
    require_once "Lib.php";
    require_once "../classes/DB.class.php";

    sessionStart();
    $db = new DB();

    if( !checkToken( $_SESSION['token'], $_SESSION['username'], $_SESSION['timestamp'] ) )
    {
        die();
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