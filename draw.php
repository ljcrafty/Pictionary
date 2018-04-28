<?php
    $title = 'Drawing Stage';
    require_once "inc/consts.inc.php";
    require_once INC . "header.inc.php";
    require_once HELPERS . "Lib.php";
    require_once HOME . "classes/DB.class.php";

    sessionStart();

    if( !checkSession() || !array_key_exists('room', $_SESSION) )
    {
        dies("An error occurred");
    }

    checkStage( $_SESSION['stage'], "drawing" );

    //update stage
    $db = new DB();

    //check that there are enough players to start
    $numIn = $db -> numPlayers( $_SESSION['room'] );

    if( $numIn < 1 )
    {
        $_SESSION['error'] = "You can't start a game with only one player";
        header("Location: waiting.php");
        die();
    }

    $db -> changeStage($_SESSION['username'], $_SESSION['room'], 'drawing');
?>
<script src="js/svg4everybody.min.js"></script>
<body onload='initCanvas();'>
    <?php require_once INC . "nav.inc.php"; ?>
    <?php include_once INC . "error.inc.php" ?>
    <main>
        <div id='drawArea'>
            <svg id='toolbox' width='80' height='280'>
                <rect onmousedown="changeColor(this)" x='5' y='5' width='30' height='30' style='fill: red'/>
                <rect onmousedown="changeColor(this)" x='40' y='5' width='30' height='30' style='fill: yellow'/>
                <rect onmousedown="changeColor(this)" x='5' y='40' width='30' height='30' style='fill: green'/>
                <rect onmousedown="changeColor(this)" x='40' y='40' width='30' height='30' style='fill: blue'/>
                <rect onmousedown="changeColor(this)" x='5' y='75' width='30' height='30' style='fill: purple'/>
                <rect onmousedown="changeColor(this)" x='40' y='75' width='30' height='30' style='fill: orange'/>
                <rect onmousedown="changeColor(this)" x='5' y='110' width='30' height='30' style='fill: pink'/>
                <rect onmousedown="changeColor(this)" x='40' y='110' width='30' height='30' style='fill: DeepSkyBlue'/>
                <rect onmousedown="changeColor(this)" x='5' y='145' width='30' height='30' style='fill: LimeGreen'/>
                <rect onmousedown="changeColor(this)" x='40' y='145' width='30' height='30' style='fill: Maroon'/>
                <rect onmousedown="changeColor(this)" x='5' y='180' width='30' height='30' style='fill: black'/>
                <rect onmousedown="changeColor(this)" class='stroke' x='41' y='181' width='28' height='28' style='fill: white'/>

                <use x='5' y='215' width='30' height='30' xlink:href="icons/undo.svg#undo" style='fill:none' onclick="undo();"></use>
                <rect class="hover" x='5' y='215' width='28' height='28' style='fill: rgba(255, 255, 255, 0.3)' onclick="undo();"/>

                <use x='40' y='215' width='30' height='30' xlink:href="icons/redo.svg#redo" style='fill:none' onclick="redo();"></use>
                <rect class="hover" x='40' y='215' width='28' height='28' style='fill: rgba(255, 255, 255, 0.3)' onclick="redo();"/>

                <use x='20' y='250' width='30' height='30' xlink:href="icons/delete.svg#delete" style='fill:none' onclick="clearCanvas()"></use>
                <rect class="hover" x='20' y='250' width='28' height='28' style='fill: rgba(255, 255, 255, 0.3)' onclick="clearCanvas(true);"/>
            </svg>
            <canvas>Please update to a more modern browser to use this application</canvas>
            <form id="desc" onsubmit="return validate('desc', 'Please enter a valid description of your image (No special characters)');" action="waiting.php" method="POST">
                <label for="desc">What did you draw?</label>
                <input type="text" name="desc" maxlength="30" />
                <input class="button" type="submit" value="Done" name="done" style="--background: #008b00"/>
                <input type="hidden" name="drawing"/>
            </form>
        </div>
        <?php require_once INC . "chat.inc.php"; ?>
    </main>
    <script><!--
    	const mouse = {
    		drawing: false,
    		curPos: { x: 0, y: 0 },
    		lastPos: { x: 0, y: 0 }
        };

        var undoQueue = [];
        var redoQueue = [];
    --></script>
    <script>svg4everybody();</script>
<?php require_once INC . "footer.inc.php"; ?>