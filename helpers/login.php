<?php
    require_once "/home/MAIN/lxj7261/Sites/442/project/inc/consts.inc.php";
    require_once "Lib.php";
    require_once "../classes/DB.class.php";

    sessionStart();

    //session already exists, they are already logged in
    if( checkSession() )
    {
        dies("You are already logged in. You cannot play in more than one tab at once.");
    }

    //no data was given
    if( !$_POST || empty($_POST) || !array_key_exists("username", $_POST) )
    {
        dies("An error occurred");
    }

    $username = $_POST['username'];
    $db = new DB();
    $room = ($_POST['room'] ? strtoupper($_POST['room']) : '');

    //check username validity
    if( !$db -> validateUser($username) )
    {
        dies("Invalid Username. Ensure your username is 1-20 characters and uses no special characters.");
    }

    $user = sanitize($username);
    
    //starting a room, no need to check username in db
    if( !$room )
    {
        while( !$room )
        {
            $room = chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90)) . chr(rand(65, 90));

            $query = "SELECT roomCode FROM Rooms WHERE roomCode = ?";
            $data = $db -> query($query, array($room));

            if( count($data) > 0 )
            {
                $room = '';
            }
        }
    }
    //joining existing room, need to check room code, username
    else
    {
        //basic validate room
        if( !$db -> validateRoom($room) )
        {
            dies("Invalid room code.");
        }

        //check that people in room haven't already started a game
        if( $db -> numPlayers( $room, 'start' ) > 0 )
        {
            dies("The game in that room has already started");
        }
    }

    //create token and session cookie
    $tokenData = createToken($user);

    $query = "INSERT INTO Rooms (username, roomCode, stage, token) VALUES(?, ?, ?, ?)";
    $data = $db -> query($query, array($user, $room, 'start', $tokenData['token']));

    //one row was affected (inserted)
    if( $data == 1 )
    {
        //on success, set session vars
        $_SESSION['stage'] = 'start';
        $_SESSION['username'] = $user;
        $_SESSION['room'] = $room;
        $_SESSION['timestamp'] = $tokenData['timestamp'];
        $_SESSION['token'] = $tokenData['token'];
        $_SESSION['key'] = $key;

        header("Location: " . URL . "waiting.php");
    }
    else //error
    {
        dies('An error occurred.');
    }
?>