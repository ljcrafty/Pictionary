<nav>
    <button id="quit" onclick="javascript: logout();">Quit</button>
    <button id="help" onclick="javascript: $('#helpBox').dialog('open');">?</button>
</nav> 

<div id="helpBox">
  <?php
    switch($_SESSION['stage'])
    {
        case "start":
            echo "<h1>Waiting Room</h1><p>You are in the waiting room! The 5-letter code on your 
                screen is your room code. To play, give your friends your room code. They will need 
                to select \"Join a Room\" on the start screen and enter your room code. The box on 
                the right of the screen is where you can chat with your friends throughout the game. 
                The counter underneath your room code shows how many other players are in your room. 
                Once all of your friends have joined, select \"Star\"!</p>
                <p>If you've changed your mind and want to join a different room, just select \"Back\". 
                This will bring you back to the home screen so you can change your room. If you want to 
                leave the game at any point, just press the \"Quit\" button in the top left corner 
                of the screen. Happy drawing!";
            break;
        
        case "drawing":
            echo "<h1>Drawing</h1><p>This screen is where you should create a drawing! Draw anything that 
                you want to by dragging around the screen within the large square (canvas) in the center of 
                the page. Then, describe the item/scene that you drew in the text box below the canvas.</p>
                <p>To change pen colors, choose a color from the palette on the left side of the screen.
                If you make a mistake in your drawing, you can use the back arrow (pointing to the left) 
                to undo your line. If you undo something you wanted to keep, you can use the forward arrow 
                (pointing to the right). If you want to start your drawing over, you can use the trash can 
                icon to clear the canvas. When you are finished with your drawing and you have added a 
                description of up to 30 characters, press the \"Done\" button, to tell your friends 
                you're ready to move on.</p>
                <p>As always, the box on the right of the screen is where you can chat with your friends 
                during the game. You can leave the game at any point by pressing the \"Quit\" button in 
                the top left corner of the screen. Happy drawing!</p>";
            break;

        case "guessing":
            if( basename($_SERVER['PHP_SELF']) == "guess.php" )
            {
                echo "<h1>Guessing</h1><p>This screen is where you have to guess what another player drew. 
                    The other player's drawing is shown on the screen now! The artist of the drawing is 
                    shown underneath the drawing. All you need to do is guess what the other player drew 
                    in their drawing by entering your guess in the text box at the bottom of the screen. 
                    When you are happy with your guess, press \"Done\"!
                    <p>As always, the box on the right of the screen is where you can chat with your friends 
                    during the game. You can leave the game at any point by pressing the \"Quit\" button in 
                    the top left corner of the screen. Happy guessing!</p>";
            }
            else
            {
                echo "<h1>Waiting Room</h1><p>You are in the waiting room! It seems like your friends are 
                still drawing. You can still chat with them by using the chat box on the right side of 
                the screen, just remember: no cheating! Once all of your friends have finished drawing, 
                you will be automatically redirected to the next stage of the game. The counter on the 
                screen shows you how many friends you are waiting on. Good luck!</p>";
            }
            break;
        
        case "guessed":
            echo "<h1>Waiting Room</h1><p>You are in the waiting room! It seems like your friends are 
                still guessing. You can still chat with them by using the chat box on the right side of 
                the screen, just remember: no cheating! Once all of your friends have finished guessing, 
                you will be automatically redirected to the next stage of the game. The counter on the 
                screen shows you how many friends you are waiting on. Good luck!</p>";
            break;
        
        //if they are in the waiting room with start, that means that they are at the results screen
        case "start":
            echo "<h1>Results</h1><p>You've finished the game! This screen shows you the results of 
                your game. The player on the top won with the most points, the second player from the 
                top came in second, etc. If you'd like to play again with the same team, press \"Play 
                Again\". If you would like to start a new game with a new team or stop playing, press 
                \"Quit\".</p>";
            break;

        default:
            echo "";
    }
  ?>
</div>