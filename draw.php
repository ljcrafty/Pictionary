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

    //update stage
    $db = new DB();
    $db -> changeStage($_SESSION['username'], $_SESSION['room'], 'drawing');
    //TODO: keep people from submitting without description in PHP
    //TODO: keep from starting without any other players
    //TODO: make coming to draw page redirect all of the other users as well (when they check for users in
    //      the waiting room, if any user has moved to draw screen, redirect with JS; use players in and out
    //      in that request)
    //TODO: add touch screen drawing
    //TODO: make the pages dynamic
?>
<body onload='initCanvas();'>
    <?php require_once INC . "nav.inc.php"; ?>
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

                <use x='5' y='215' width='30' height='30' href="icons/undo.svg#undo" style='fill:none' onclick="undo();"></use>
                <use x='40' y='215' width='30' height='30' href="icons/redo.svg#redo" style='fill:none' onclick="redo();"></use>
                <use x='20' y='250' width='30' height='30' href="icons/delete.svg#delete" style='fill:none' onclick="clearCanvas()"></use>
            </svg>
            <canvas>Please update to a more modern browser to use this application</canvas>
            <form id="desc" onsubmit="validateDraw()" action="waiting.php" method="POST">
                <label for="desc">What did you draw?</label>
                <input type="text" name="desc" maxlength="30" />
                <input class="button" type="submit" value="Done" name="done" style="--background: #008b00"/>
            </form>
        </div>
        <?php
            require_once INC . "chat.inc.php";
        ?>
    </main>
    <script>
    	const mouse = {
    		drawing: false,
    		curPos: { x: 0, y: 0 },
    		lastPos: { x: 0, y: 0 }
        };

        var undoQueue = [];
        var redoQueue = [];
    </script>
</body>
<?php require_once INC . "footer.inc.php"; ?>