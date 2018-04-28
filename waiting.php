<?php
    $title = "Waiting Room";
    require_once "/home/MAIN/lxj7261/Sites/442/project/inc/consts.inc.php";
    require_once INC . "header.inc.php";
    require_once HOME . "classes/DB.class.php";

    $db = new DB();

    if( !checkSession() || !$db -> validateRoom($_SESSION['room']) || 
        !$db -> validateUser($_SESSION['username'], $_SESSION['room']) )
    {
        dies("An error occurred");
    }
    
    $room = $_SESSION['room'];
    $user = $_SESSION['username'];

    if( !$_POST )
    {
        $db -> changeStage($_SESSION['username'], $_SESSION['room'], 'start');
    }
    else
    {    
        //came from draw screen
        if( $_POST && array_key_exists("desc", $_POST) )
        {
            //validate that description was given
            if( count($_POST["desc"]) > 30 || count($_POST["desc"]) < 1 || 
                preg_match('/[^a-zA-Z ]/', $_POST['desc']) )
            {
                $_SESSION['error'] = "Please enter a valid description (0-30 characters long with no special characters)";
                header("Location: draw.php");
                die();
            }

            //insert drawing and description into database
            $query = "UPDATE Rooms SET picture = ?, picDescr = ? WHERE username = ? AND roomCode = ?";
            $params = array( $_POST['drawing'], $_POST["desc"], $_SESSION['username'], $_SESSION['room'] );
            $num = $db -> query( $query, $params );

            //error adding into database
            if( $num < 1 )
            {
                $_SESSION['error'] = "There was an error adding your data to the database, please try again.";
                header("Location: draw.php");
                die();
            }

            //change stage
            $db -> changeStage($_SESSION['username'], $_SESSION['room'], 'guessing');
        }//end came from draw

        //came from guess screen
        if( $_POST && array_key_exists("guess", $_POST) )
        {
            //validate guess
            if( count($_POST["guess"]) > 30 || count($_POST["guess"]) < 1 || 
                preg_match('/[^a-zA-Z ]/', $_POST['guess']) )
            {
                $_SESSION['error'] = "Please enter a valid guess (0-30 characters long with no special characters)";
                header("Location: guess.php");
                die();
            }

            //insert guess into database
            $query = "UPDATE Rooms SET picGuess = ? WHERE username = ? AND roomCode = ?";
            $params = array( $_POST['guess'], $_SESSION['username'], $_SESSION['room'] );
            $num = $db -> query( $query, $params );

            //error adding into database
            if( $num < 1 )
            {
                $_SESSION['error'] = "There was an error adding your data to the database, please try again.";
                header("Location: guess.php");
                die();
            }

            //check if player's guess was right
            $query = "SELECT r1.username, r1.picDescr, r1.score AS otherScore, r2.score  FROM Rooms r1 
                JOIN Rooms r2 ON r1.username = r2.receivedUser AND r1.roomCode = r2.roomCode WHERE 
                r2.username = ? AND r2.roomCode = ?";
            $params = array( $_SESSION['username'], $_SESSION['room'] );
            $received = $db -> query( $query, $params );

            if( count($received) <= 0 )
            {
                $_SESSION['error'] = "There was an error adding your data to the database, please try again.";
                header("Location: guess.php");
                die();
            }

            //if guess is right
            if( strtolower($received[0]['picDescr']) == strtolower($_POST['guess']) )
            {
                //add points to guesser
                $score = $received[0]['score'] + 50;
                $query = "UPDATE Rooms SET score = ?  WHERE username = ? AND roomCode = ?";
                $params = array( $score, $_SESSION['username'], $_SESSION['room'] );
                $num = $db -> query( $query, $params );

                //add points to guessed
                $score = $received[0]['otherScore'] + 25;
                $query = "UPDATE Rooms SET score = ?  WHERE username = ? AND roomCode = ?";
                $params = array( $score, $received[0]['username'], $_SESSION['room'] );
                $num = $db -> query( $query, $params );
            }//end came from guess screen

            //change stage
            $db -> changeStage($_SESSION['username'], $_SESSION['room'], 'guessed');
        }
    }//end post data sent

    echo "<script>var room = '$room'; var user = '$user'; var stage = '" . $_SESSION['stage'] . "'</script>";
?>
<body onload='initWaitRoom(); getChat();'>
    <?php include INC . "nav.inc.php"; ?>
    <?php include INC . "error.inc.php"; ?>
    <main>
        <div class="padding flow">
            <h1>
                <?= ($_SESSION['stage'] == 'start' ? 'Room Code: <b>' . $room . '</b>' : '!Pictionary') ?>
            </h1>
        </div>
        <?php include INC . "chat.inc.php"; ?>
    </main>
<?php require_once INC . "footer.inc.php"; ?>