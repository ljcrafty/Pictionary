<?php 
$title = "Login";
require_once "inc/header.inc.php";

//TODO: make everything mobile-friendly
?>
<body class="padding" onload="startEncrypt()">
    <?php
        if( isset($_SESSION) && array_key_exists("error", $_SESSION) )
        {
            echo "<div class='error'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
    ?>

    <h1>!Pictionary</h1>

    <form id="endForm" action="helpers/login.php" method="POST">
        <span id="username" class="break">
            <label for="username">Username:&nbsp;</label>
            <input type="text" name="username" class="textbox" maxlength='20'/>
        </span>
        <span id="room" class="break hidden">
            <label for="room">Room Code:&nbsp;</label>
            <input type="text" name="room" class="textbox" maxlength='5'/>
        </span>
        <input class="button" type="button" value="Back"  name="back" style="--background: #999" onclick="hide(this, true)"/>
        <input class="button" type="submit" value="Done" name="done" style="--background: #008b00"/>
        <input type="hidden" id="key" name="key" />
    </form>
    <form id="startForm">
        <span>
            <input class="button" type="button" value="Join Room"  name="join" style="--background: #8a5293" onclick="hide(this, false)"/>
            <input class="button" type="button" value="Start Room" name="start" style="--background: #2176FF" onclick="hide(this, false)"/>
        </span>
    </form>
<?php require_once INC . "footer.inc.php"; ?>