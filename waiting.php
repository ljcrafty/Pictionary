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

    //came from draw screen
    if( $_POST && array_key_exists("desc", $_POST) )
    {
        //validate that description was given
        if( count($_POST["desc"]) > 30 || count($_POST["desc"]) < 1 || 
            preg_match('/[^a-zA-Z]/', $_POST['desc']) )
        {
            //TODO: add ability to show errors on draw page
            header("Location: draw.php");
            die();
        }

        //TODO: insert drawing and description when drawing is put in the form

        //change stage
        $db -> changeStage($_SESSION['username'], $_SESSION['room'], 'drawn');
    }

    echo "<script>var room = '$room'; var user = '$user'; var stage = '" . $_SESSION['stage'] . "'</script>";
    echo $_SESSION['key'];
?>
<body onload='init(); getChat();'>
    <?php
        include INC . "nav.inc.php";
    ?>
    <main>
        <div class="padding flow">
            <h1>
                <?= ($_SESSION['stage'] == 'start' ? 'Room Code: <b>' . $room . '</b>' : '!Pictionary') ?>
            </h1>
        </div>
        <?php
            include INC . "chat.inc.php";
        ?>
    </main>
<?php require_once INC . "footer.inc.php"; ?>