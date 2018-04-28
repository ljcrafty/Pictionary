<?php
    $title = 'Guessing Stage';
    require_once "inc/consts.inc.php";
    require_once INC . "header.inc.php";
    require_once HELPERS . "Lib.php";
    require_once HOME . "classes/DB.class.php";

    sessionStart();

    if( !checkSession() || !array_key_exists('room', $_SESSION) )
    {
        dies("An error occurred");
    }

    checkStage( $_SESSION['stage'], "guessing" );

    $db = new DB();

    //Check if user needs to be chosen or not
    $query = "SELECT receivedUser FROM Rooms WHERE username = ? AND roomCode = ?";
    $params = array( $_SESSION['username'], $_SESSION['room'] );
    $users = $db -> query( $query, $params );

    if( $users[0]['receivedUser'] == null )
    {
        //pick another user to have the image of (from list of users who haven't been taken)
        $query = "SELECT username, picture FROM Rooms WHERE username != ? AND roomCode = ?";
        $params = array( $_SESSION['username'], $_SESSION['room'] );
        $users = $db -> query( $query, $params );
        $user = '';
        $tried_users = array();

        while( !$user )
        {
            //in case all users have been taken somehow
            if( count($tried_users) == count($users) )
            {
                dies("An error has occurred");
            }

            $row = rand(0, count($users) - 1);
            $user = $users[$row]['username'];
            $pic = $users[$row]['picture'];

            //dont try a user you already tried
            if( in_array( $user, $tried_users ) )
            {
                continue;
            }

            $query = "SELECT receivedUser FROM Rooms WHERE receivedUser = ? AND roomCode = ?";
            $data = $db -> query($query, array( $user, $_SESSION['room'] ));

            if( count($data) > 0 )
            {
                $tried_users[] = $user;
                $user = '';
            }
        }

        //add received user to the DB and session
        $query = "UPDATE Rooms SET receivedUser = ? WHERE username = ? AND roomCode = ?";
        $params = array( $user, $_SESSION['username'], $_SESSION['room'] );
        $num = $db -> query( $query, $params );

        if( $num < 1 )
        {
            dies("A problem occurred");
        }
    }
    else //user to guess was already chosen
    {
        $query = "SELECT username, picture FROM Rooms WHERE username = ? AND roomCode = ?";
        $params = array( $users[0]['receivedUser'], $_SESSION['room'] );
        $users = $db -> query( $query, $params );
        $user = $users[0]['username'];
        $pic = $users[0]['picture'];
    }
?>
<body onload="init();" >
    <?php require_once INC . "nav.inc.php"; ?>
    <?php include_once INC . "error.inc.php" ?>
    <main>
        <div id='drawArea'>
            <canvas style="margin: auto">Please update to a more modern browser to use this application</canvas>

            <h2>Artist: <?= $user ?></h2>
            <form id="desc" onsubmit="return validate('guess', 'Please enter a valid guess for the image (No special characters)');" action="waiting.php" method="POST">
                <label for="guess">What is your guess?</label>
                <input type="text" name="guess" maxlength="30" />
                <input class="button" type="submit" value="Done" name="done" style="--background: #008b00"/>
            </form>
        </div>
        <?php require_once INC . "chat.inc.php"; ?>
    </main>
    <script><!--
        var ctx = document.getElementsByTagName("canvas")[0].getContext('2d');
        var img = new Image;
        
        img.onload = function(){
            ctx.drawImage(img,0,0);
        };
        img.src = "<?= $pic ?>";
    --></script>
<?php require_once INC . "footer.inc.php"; ?>