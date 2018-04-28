<?php
    $title = 'Results';
    require_once "inc/consts.inc.php";
    require_once INC . "header.inc.php";
    require_once HELPERS . "Lib.php";
    require_once HOME . "classes/DB.class.php";

    sessionStart();

    //TODO: add a thing that shows the drawings, what they were, and what was guessed
    if( !checkSession() || !array_key_exists('room', $_SESSION) )
    {
        dies("An error occurred");
    }

    checkStage( $_SESSION['stage'], "guessed" );

    //get score data for the whole room
    $db = new DB();
    $query = "SELECT username, score FROM Rooms WHERE roomCode = ? ORDER BY score DESC";
    $params = array( $_SESSION['room'] );
    $data = $db -> query( $query, $params );
?>
<body onload="init();" >
    <?php require_once INC . "nav.inc.php"; ?>
    <?php include_once INC . "error.inc.php" ?>
    <main>
        <div class="drawingArea">
            <h1>Results!</h1>
            <table>
                <?php
                    //print score data for the room
                    $count = 1;
                    foreach( $data as $row )
                    {
                        echo "<tr>
                                <td><span class='bold'>" . $count . ".</span> 
                                    <span>" . $row['username'] ."</span></td>
                                <td><b>" . $row['score'] . "</b></td>
                            </tr>";
                        $count++;
                    }
                ?>
            </table>
            <form>
                <input class="button" type="button" value="Quit"  name="back" style="--background: #999" onclick="logout();"/>
                <input class="button" type="button" value="Play again" name="done" style="--background: #008b00" onclick="playAgain();"/>
            </form>
        </div>
        <?php require_once INC . "chat.inc.php"; ?>
    </main>
<?php require_once INC . "footer.inc.php"; ?>