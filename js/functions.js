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
function init()
{
    var div = document.getElementsByTagName('main')[0].children[0];
    switch( stage )
    {
        case 'start':
            var h2 = createWithText('h2', 'Welcome to !Pictionary!');
            h2.setAttribute('class', 'center')
            div.insertBefore(h2, div.children[0]);
            var form = '<form>\n<input class="button" type="button" value="Back" ' + 
                'name="join" style="--background: #999" onclick="logout();"/>\n' + 
                '<input class="button" type="button" value="Start" name="start" style="--background: #008b00" ' + 
                'onclick="javascript: window.location = \'draw.php\'"/></form>';
            div.innerHTML += form;
            playersIn();
            break;
        case 'drawn':
        case 'guessed':
            var h2 = createWithText('h2', 'Welcome to !Pictionary!');
            h2.setAttribute('class', 'center')
            div.insertBefore(h2, div.children[0]);
            playersIn('drawn');    
            break;
        default:
            break;
    }

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
    $('#helpBox').dialog({ autoOpen: false });

    let canvas = document.getElementsByTagName("canvas")[0];
	let ctx = canvas.getContext("2d");
	ctx.strokeStyle = "#000";
	ctx.lineWith = 1;
	
	//add listeners
	canvas.addEventListener('mousedown', function(e){
		mouse.lastPos = getMouse(canvas, e);
		mouse.drawing = true;
	});
	canvas.addEventListener('mousemove', function(e){
		mouse.curPos = getMouse(canvas, e);
	});
	canvas.addEventListener('mouseup', function(e){
        mouse.drawing = false;
        //TODO: make this listener record the canvas data as a URL in an array and hidden field
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
		renderCanvas();
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
    
    let rects = document.getElementsByTagName("rect");

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
*/
//TODO: add ability to undo and redo
function clearCanvas()
{
	let canvas = document.getElementsByTagName("canvas")[0];
	canvas.width = canvas.width;
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
  * Renders the canvas object based on the current mouse position
*/
function renderCanvas()
{
	let canvas = document.getElementsByTagName("canvas")[0];
	let ctx = canvas.getContext("2d");
	
	if( mouse.drawing )
	{
		ctx.moveTo(mouse.lastPos.x, mouse.lastPos.y);
		ctx.lineTo(mouse.curPos.x, mouse.curPos.y);
		ctx.stroke();
		mouse.lastPos = mouse.curPos;
	}
}

//TODO: make this work (validate that a description was given)
function validateDraw()
{

}

/** 
 * Logs a user out by destroying their session and data in the DB
*/
function logout()
{
    var http = new XMLHttpRequest();
    http.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200)
        {
            window.location = "index.php";
        }
    };
    http.open('GET', 'helpers/logout.php');
    http.send();
    return undefined;
}

/**
 * Sends a symmetric key to the server to de/encrypt with
 * Borrowed from https://medium.com/@tikiatua/symmetric-and-asymmetric-encryption-with-javascript-and-go-240043e56daf
*/
function startEncrypt()
{
    sjcl.beware["CBC mode is dangerous because it doesn't protect message integrity."]();
    let pub = "MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDSuHHRWHdBC8v3N+2/3PZsc1Ov" +
        "313T6u9CpXw+BrUj1cC59mjmTA6DEm7e5/HJGNeh5ZNY/aCDLIXaT/6BSUga0dlW" +
        "1EdWFI16ilE+OtBM+rhvzD2V2K8setrKLVp4Fpsw83zlFySboJEK8HSSbBVRa0LX" +
        "u/4aO72KNVllwn8a1QIDAQAB";

    let key = generateKey(32);
    let iv = btoa(generateKey(16));//base64 encode

    let obj = JSON.parse(sjcl.encrypt( key, 'test', {mode: "cbc", iv: iv} ));
    let symKey = key + ":::" + iv;

    let rsa = new JSEncrypt();
    rsa.setPublicKey( pub );
    let cryptKey = rsa.encrypt( symKey );

    window.localStorage.setItem("theKey", symKey);
    $("#key").val(cryptKey + ":::" + obj.ct);
    console.log(atob(iv) + ":::" + key + ":::" + obj.ct);
}

/**
 * Generates a key to encrypt with
 * Borrowed from https://medium.com/@tikiatua/symmetric-and-asymmetric-encryption-with-javascript-and-go-240043e56daf
 * @param {int} length the length of the key to generate
 * @returns {string} a random key of the given length
*/
function generateKey( length )
{
    // define the characters to pick from
    let chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz*&-%/!?*+=()";
    let randomstring = "";

    for (let i = 0; i < length; i++) 
    {
        let rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum,rnum+1);
    }
    return randomstring;
}

/** 
 * Sends a request to add a new message to the chat for the current room
*/
function newChat()
{
    let msg = document.getElementsByName("message")[0].value;
    document.getElementsByName("message")[0].innerHTML = '';
    document.getElementsByName("message")[0].value = '';

    var http = new XMLHttpRequest();
    http.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            getChat(false);
        }
    };
    http.open('GET', 'helpers/newChat.php?msg=' + msg);
    http.send();
}

/**
 * Gets new chat messages from the database and inserts them into the page (optionally recursive)
 * @param {boolean} timeout whether or not the function should continue to call itself recursively
*/
function getChat( timeout = true )
{
    var http = new XMLHttpRequest();
    http.onreadystatechange = function() {
        if(this.readyState == 4 && this.status == 200)
        {
            let ele = byId("messages");
            let child = create("template");
            child.innerHTML = this.responseText.trim();
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
    };
    http.open('GET', 'inc/chat.inc.php');
    http.send();
}

/**
 * Gets the number of players in the room who are at a given stage
 * @param {string} stage (optional) the stage to get numbers for. If stage is not provided, the total 
 *      number of players in the room is obtained. If stage is provided, the number of players in the 
 *      room who are not at the given stage is obtained.
*/
//TODO: add JS validation to all forms and buttons (make sure they can move onto next page)
function playersIn( stage = '' )
{
    var http = new XMLHttpRequest();
    http.onreadystatechange = function() { 
        if(this.readyState == 4 && this.status == 200)
        {
            playersInHTML(stage, this.responseText);
            console.log(this.responseText); 
        }
    };
    http.open('GET', 'helpers/players.php?' + (stage != '' ? 'stage=' + stage : ''));
    http.send();
}

/**
 * Records the number of people in the room or the number of people who are not yet at a certain
 *      stage in the room onto the page for users to see
 * @param {string} stage the stage to get numbers for
 * @param {int} num the number of people to record on the page
*/
//TODO: when you are waiting on 0 people, move everyone to next stage
function playersInHTML( stage, num )
{
    let text1 = '', text2 = '';

    //total players in the room
    if( stage == '' )
    {
        text1 = 'There are ';
        text2 = ' other people in your room. Press Start when everyone\'s here!';
    }
    else //players who aren't at a certain stage
    {
        text1 = 'We\'re waiting for ';
        text2 = ' people in your room to finish.';
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
    setTimeout(function() { playersIn(stage); }, 1000);
}