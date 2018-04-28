(function($) {
    //slides in the given direction until it is completely centered in the window 
    //and puts itself in front of other elements
    $.fn.slideIn = function ( ele, direction, xPos ) {
        //calculate val needed to center element
        var leftFromCenter = ($(window).width() / 2) - ($(ele).width() / 2);

        //slide in to the right
        if( direction == "right" )
        {
            //put element in the given x Pos
            $(ele).css("left", xPos + "px");

            //end condition
            if( $(ele).position().left < leftFromCenter )
            {
                $(ele).css("visibility", "visible");
                setTimeout( function() {
                    $(ele).slideIn(ele, direction, xPos + 14)
                 }, .5 );
            }
            else
            {
                //finish with the absolute right value
                $(ele).css("left", leftFromCenter + "px");
            }
        }
        else //slide in to the left
        {
            //put element in the given x Pos
            $(ele).css("left", xPos + "px");

            //end condition
            if( $(ele).position().left > leftFromCenter )
            {
                $(ele).css("visibility", "visible");
                setTimeout( function() {
                    $(ele).slideIn(ele, direction, xPos - 14)
                 }, .5 );
            }
            else
            {
                //finish with the absolute right value
                $(ele).css("left", leftFromCenter + "px");
            }
        }
    }//end slideIn

    //slides in the given direction until it is completely out of the window and then hides itself
    $.fn.slideOut = function ( ele, direction, xPos ) {
        //slide out to the right
        if( direction == "right" )
        {
            //put element in the given x Pos
            $(ele).css("left", xPos + "px");

            //end condition
            if( $(ele).position().left < $(ele).width() )
            {
                setTimeout( function() {
                    $(ele).slideOut(ele, direction, xPos + 14);
                 }, .5 );
            }
            else
            {
                $(ele).css("visibility", "hidden");
            }
        }
        else //slide out to the left
        {
            //put element in the given x Pos
            $(ele).css("left", xPos);

            //end condition
            if( ($(window).width() - $(ele).position().left) < $(ele).width() )
            {
                setTimeout( function() {
                    $(ele).slideOut(ele, direction, xPos - 14);
                 }, .5 );
            }
            else
            {
                $(ele).css("visibility", "hidden");
            }
        }
    }//end slideOut
}(jQuery));

/**
 * Shows/hides the form that users log in through using sliding animations
 * @param {Element} ele the element that was pressed to call this method
 * @param {boolean} hide whether or not to hide the form
*/
function hide( ele, hide )
{
    let el = $("#startForm");

    if(hide)
    {
        $("#startForm").slideIn(el, "left", $(el).position().left);
        byId("room").className = "break hidden";
    }
    else
    {
        if(ele.name == "join")
        {
            byId("room").className = "break";
        }
        else
        {
            $("#room input").val('');
        }

        $("#startForm").slideOut(el, "right", $(el).position().left);
    }
}

/**
 * Creates a tag of a given type
 * @param {string} tag the name of the type of tag to create
 * @returns {Element} a DOM element of the given type
*/
function create(tag)
{
    return document.createElement(tag);
}

/**
 * Shortens code needed to get an element by id
 * @param {string} id the id of the element to get
 * @returns {Element} the element in the DOM with that id
*/
function byId(id)
{
    return document.getElementById(id);
}

/**
 * Creates a new tag with a text node inside of it
 * @param {string} tag the name of the tag to create
 * @param {string} text the text to include in a text node inside of the new tag
 * @returns {Element} the created tag
*/
function createWithText(tag, text)
{
    var node = document.createElement(tag);
    var text = document.createTextNode(text);
    node.appendChild(text);

    return node;
}

/** 
 * Initializes the waiting room depending on the stage players are in and initializes the chat
*/
function initWaitRoom()
{
    init();

    var div = document.getElementsByTagName('main')[0].children[0];
    switch( stage )
    {
        case 'start':
            var h2 = createWithText('h2', 'Welcome to !Pictionary!');
            h2.setAttribute('class', 'center')
            div.insertBefore(h2, div.children[0]);
            var form = '<form action="draw.php" onsubmit="return validateWait();" >\n' + 
                '<input class="button" type="button" ' +
                'value="Back" name="join" style="--background: #999" onclick="logout();"/>\n' + 
                '<input class="button" type="submit" value="Start" style="--background: #008b00" ' + 
                '/></form>';
            div.innerHTML += form;
            playersIn(playersInHTML);
            playersIn(checkPlayers, 'start');
            break;
        case 'guessing':
        case 'guessed':
            var h2 = createWithText('h2', 'Welcome to !Pictionary!');
            h2.setAttribute('class', 'center')
            div.insertBefore(h2, div.children[0]);
            playersIn(playersInHTML, stage);    
            break;
        default:
            break;
    }
}

/**
 * Initializes the help button and pressing the send button for chat messages when the enter
 *      key is pressed
 */
function init()
{
    $('#helpBox').dialog({ autoOpen: false });

    $("#message").keypress(function (e) {
        if( e.which == 13 && !e.shiftKey )
        {
            newChat();
            return false;
        }
        return true;
    });
}

/**
  * Initializes the canvas with listeners and default values
*/
function initCanvas()
{
    init();

    let canvas = document.getElementsByTagName("canvas")[0];
	let ctx = canvas.getContext("2d");
	ctx.strokeStyle = "#000";
	ctx.lineWith = 1;
	
	//add listeners
	$("canvas").on('vmousedown', function(e){
        mouse.lastPos = getMouse(canvas, e);
        ctx.moveTo(mouse.lastPos.x, mouse.lastPos.y);
        mouse.drawing = true;
	});
	$("canvas").on('vmousemove', function(e){
        if( mouse.drawing )
        {
            mouse.curPos = getMouse(canvas, e);

            ctx.lineTo(mouse.curPos.x, mouse.curPos.y);
            ctx.stroke();
            
            mouse.lastPos = mouse.curPos;
        }
    });
    $("canvas").on('vclick', function(e){
        mouse.drawing = false;
    });
	$("canvas").on('vmouseup', function(e){
        mouse.drawing = false;

        undoQueue.unshift( canvas.toDataURL() );//every stroke adds a new URL to revert to
        redoQueue = [];//after you draw over something, you can't redo your old strokes
        document.getElementsByClassName("hover")[0].style.visibility = 'hidden';//show you can undo
        document.getElementsByName("drawing")[0].value = canvas.toDataURL();
    });
	
	// Borrowed from http://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
	window.requestAnimFrame = (function (callback) {
        return window.requestAnimationFrame || 
           window.webkitRequestAnimationFrame ||
           window.mozRequestAnimationFrame ||
           window.oRequestAnimationFrame ||
           window.msRequestAnimaitonFrame ||
           function (callback) {
        		window.setTimeout(callback, 1000/60);
           };
	})();
	
	(function draw()
    {
    	window.requestAnimFrame(draw);
    })();
}

/**
  * Changes the pen color for the canvas based on the element that was pressed to invoke the change
  * @param ele	-	the element that invoked this function onclick (should have fill value of new pen color)
*/
function changeColor(ele)
{
	let canvas = document.getElementsByTagName("canvas")[0];
	let ctx = canvas.getContext("2d");
	ctx.closePath();
	ctx.beginPath();
    ctx.strokeStyle = ele.style.fill;
    
    let rects = $("rect").not(".hover");

    //add indicator of which color is selected
    for( var i = 0; i < rects.length; i++ )
    {
        rects[i].style.stroke = "";
    }

    if( ele !== rects[rects.length - 1] )
        rects[rects.length - 1].style.stroke = "black";

    ele.style.stroke = "#666";
}

/**
  * Clears the canvas of all drawings
  * @param reset    -   boolean on whether or not the undo and redo queue should be reset
*/
function clearCanvas( reset = false )
{
    let canvas = document.getElementsByTagName("canvas")[0];
    let color = canvas.getContext("2d").strokeStyle;
    canvas.width = canvas.width;
    document.getElementsByName("drawing")[0].value = "";
    canvas.getContext("2d").strokeStyle = color;

    if( reset )
    {
        undoQueue = [];
        redoQueue = [];

        document.getElementsByClassName("hover")[0].style.visibility = 'visible';
        document.getElementsByClassName("hover")[1].style.visibility = 'visible';
    }
}

/** 
 * Removes the last stroke from the canvas
*/
function undo()
{
    clearCanvas();
    document.getElementsByClassName("hover")[0].style.visibility = 'visible';

    if( undoQueue.length > 0 )
    {
        //add to redo queue
        let undone = undoQueue.shift();
        redoQueue.unshift( undone );
        document.getElementsByClassName("hover")[1].style.visibility = 'hidden';

        //after shift
        if( undoQueue.length > 0 )
        {
            //repaint with last URL
            //Borrowed from https://stackoverflow.com/questions/4773966/drawing-an-image-from-a-data-url-to-a-canvas
            let canvas = document.getElementsByTagName("canvas")[0];
            let ctx = canvas.getContext('2d');
            let img = new Image();
            img.onload = function(){
                ctx.drawImage(img,0,0);
            };
            img.src = undoQueue[0];
            
            document.getElementsByClassName("hover")[0].style.visibility = 'hidden';
            document.getElementsByName("drawing")[0].value = canvas.toDataURL();
        }
    }
}

/** 
 * Replaces the last removed stroke onto the canvas
*/
function redo()
{
    if( redoQueue.length > 0 )
    {
        let redone = redoQueue.shift();
        undoQueue.unshift( redone );
        clearCanvas();
        document.getElementsByClassName("hover")[0].style.visibility = 'hidden';//if it was redone, it can be undone

        //repaint with last URL
        //Borrowed from https://stackoverflow.com/questions/4773966/drawing-an-image-from-a-data-url-to-a-canvas
        let canvas = document.getElementsByTagName("canvas")[0];
        let ctx = canvas.getContext('2d');
        let img = new Image();
        img.onload = function(){
            ctx.drawImage(img,0,0);
        };
        img.src = redone;

        document.getElementsByName("drawing")[0].value = canvas.toDataURL();

        //check size after shift for indicator
        if( redoQueue.length == 0 )
        {
            document.getElementsByClassName("hover")[1].style.visibility = 'visible';
        }
    }
}

/**
  * Gets the current position of the mouse on the given canvas
  * @param canvas	-	the canvas object to get the position of
  * @param event	-	the event object that holds the new position
  * @return an object holding the x and y position of the mouse under the attributes 'x' and 'y'
  * Based on http://bencentra.com/code/2014/12/05/html5-canvas-touch-events.html
  * 	and https://stackoverflow.com/questions/43853119/javascript-wrong-mouse-position-when-drawing-on-canvas
*/
function getMouse( canvas, event )
{
	let rect = canvas.getBoundingClientRect();
	return {
    	x: (event.clientX - rect.left) / rect.width * canvas.width,
    	y: (event.clientY - rect.top) / rect.height * canvas.height
  	};
}

/** 
 * Make sure that the user has entered a valid description before submitting the drawing form
*/
function validate( name, msg )
{
    let desc = document.getElementsByName(name)[0];
    
    if( desc.value.length < 1 || desc.value.length > 30 ||
        desc.value.match('[^a-z A-Z]') != null )
    {
        error(msg);
        return false;
    }
    
    error();
    return true;
}

/**
 * Check that enough people are in the waiting room before moving on
 */
function validateWait()
{
    let num = $(".center > b").html();
    console.log(num);
    
    if( num == '0' )
    {
        error( "You cannot start a game with only one player" );
        return false;
    }
    
    error();
    return true;
}

/**
 * Shows or hides an error
 * @param msg (optional) the message to show as an error; if not given, the error will be hidden
 */
function error( msg = '' )
{
    let div = byId('error');

    if(msg)
    {
        div.innerHTML = msg;
        div.style.display = "inherit";
    }
    else
    {
        div.innerHTML = '';
        div.style.display = "none";
    }
}

/** 
 * Logs a user out by destroying their session and data in the DB
*/
function logout()
{
    $.ajax({ 
        type: 'GET', 
        url: 'helpers/logout.php',
        success: () => { window.location = "index.php"; }
    });

    return undefined;
}

/**
 * Returns a user to the original waiting room when they want to play another game
 */
function playAgain()
{
    window.location = "waiting.php";
}

/**
 * Asymmetrically encrypts a message
 * Borrowed from https://medium.com/@tikiatua/symmetric-and-asymmetric-encryption-with-javascript-and-go-240043e56daf
 * @param {string} msg the message to encrypt
 * @returns {string} the encrypted message
*/
function encrypt( msg )
{
    let pub = "-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDSuHHRWHdBC8v3N+2/3PZsc1Ov" +
    "313T6u9CpXw+BrUj1cC59mjmTA6DEm7e5/HJGNeh5ZNY/aCDLIXaT/6BSUga0dlW" +
    "1EdWFI16ilE+OtBM+rhvzD2V2K8setrKLVp4Fpsw83zlFySboJEK8HSSbBVRa0LX" +
    "u/4aO72KNVllwn8a1QIDAQAB-----END PUBLIC KEY-----";

    let rsa = new JSEncrypt();
    rsa.setPublicKey( pub );
    let cryptKey = rsa.encrypt( msg );

    return cryptKey;
}

/**
 * Asymmetrically decrypts a message
 * Borrowed from https://medium.com/@tikiatua/symmetric-and-asymmetric-encryption-with-javascript-and-go-240043e56daf
 * @param {string} msg the message to decrypt
 * @returns {string} the decrypted message
*/
function decrypt( msg )
{
    let pub = window.localStorage.getItem("theKey");

    let rsa = new JSEncrypt();
    rsa.setPrivateKey( pub );
    let cryptKey = rsa.decrypt( msg );

    return cryptKey;
}

/** 
 * Sends a request to add a new message to the chat for the current room
*/
function newChat()
{
    let msg = document.getElementsByName("message")[0].value;
    document.getElementsByName("message")[0].innerHTML = '';
    document.getElementsByName("message")[0].value = '';

    $.ajax({ 
        type: 'GET', 
        url: 'helpers/newChat.php?msg=' + msg,
        success: () => { getChat(false); } 
    });
}

/**
 * Gets new chat messages from the database and inserts them into the page (optionally recursive)
 * @param {boolean} timeout whether or not the function should continue to call itself recursively
*/
function getChat( timeout = true )
{
    $.ajax({ 
        type: 'GET', 
        url: 'inc/chat.inc.php',
        success:(result) => {
            let ele = byId("messages");
            let child = create("template");
            child.innerHTML = result.trim();
            child = child.content.firstChild.childNodes[1];
            
            ele.innerHTML = ''; 
            ele.innerHTML = child.innerHTML;

            if(timeout) 
                setTimeout(getChat, 3000);
            else
                setTimeout(function() {
                    byId("messages").scrollTop = 10000;
                }, 500);
        } 
    });
}

/**
 * Gets the number of players in the room who are at a given stage
 * @param {string} stage (optional) the stage to get numbers for. If stage is not provided, the total 
 *      number of players in the room is obtained. If stage is provided, the number of players in the 
 *      room who are not at the given stage is obtained.
*/
function playersIn( callback, stage = '' )
{
    $.ajax({
        type: 'GET', 
        url: 'helpers/players.php?' + (stage != '' ? 'stage=' + stage : ''), 
        success: (result) => { callback(stage, result); } 
    });
}

/**
 * Checks whether or not the player should move onto the draw stage
 * @param {string} stage the stage you are checking for (needed to conform to other function)
 * @param {int} num the number of players outside of the current stage
 */
function checkPlayers( stage, num )
{
    //if there are any players that aren't in the given stage
    if( num > 0 )
    {
        //move to next stage
        window.location = "draw.php";
    }
    setTimeout( () => { playersIn(checkPlayers, 'start'); }, 500 );
}

/**
 * Records the number of people in the room or the number of people who are not yet at a certain
 *      stage in the room onto the page for users to see
 * @param {string} stage the stage to get numbers for
 * @param {int} num the number of people to record on the page
*/
function playersInHTML( stage, num )
{
    let text1 = '', text2 = '';

    //total players in the room
    if( stage == '' )
    {
        text1 = 'There ' + (num > 1 ? "are " : "is ");
        text2 = ' other ' + (num > 1 ? "people " : "person ") + 
            ' in your room. Press Start when everyone\'s here!';
    }
    else //players who aren't at a certain stage
    {
        //redirect if you aren't waiting for someone
        if( num <= 0 )
        {
            switch(stage)
            {
                case "guessing":
                case "drawn":
                    window.location = "guess.php";
                    break;

                case "guessed":
                    window.location = "results.php";
                    break;
                
                default:
                    window.location = "index.php";
                    break;
            }
        }
        console.log(num);

        text1 = 'We\'re waiting for ';
        text2 = ' ' + (num > 1 ? "people " : "person ") + ' in your room to finish.';
    }

    var div = document.getElementsByTagName('main')[0].children[0];
    var players = num;
    var b = createWithText('b', players);
    var p = create('p');
    p.appendChild(document.createTextNode(text1));
    p.appendChild(b);
    p.appendChild(document.createTextNode(text2));
    p.setAttribute('class', 'center');

    //remove previous paragraphs
    for( var i = 0; i < div.getElementsByTagName('p').length; i++ )
    {
        div.removeChild(div.getElementsByTagName('p')[i]);
    }

    div.insertBefore(p, div.children[2]);
    setTimeout(function() { playersIn( playersInHTML, stage ); }, 1000);
}