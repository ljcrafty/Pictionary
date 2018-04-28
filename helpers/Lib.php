<?php
require_once "/home/MAIN/lxj7261/Sites/442/project/inc/consts.inc.php";

/**
 * Kills the process and returns to index.html with an optional error message
 * $msg -   The message to show users when redirected to the home page
 */
function dies($msg = '')
{
    sessionStart();

    if( $msg )
    {
        $_SESSION['error'] = $msg;
    }

    header("Location: " . URL . "index.php");
    die();
}

/**
 * Starts a session with the right name if a session has not already been started
 */
function sessionStart()
{
    if( !isset($_SESSION) )
    {
        session_name('!Pictionary');
        session_start();
    }
}

/**
 * Destroys a session and all data associated with it, then redirects the user to the index page
 */
function sessionDestroy()
{
    sessionStart();
    session_destroy();
    unset($_SESSION);
    
    header("Location: " . URL . "index.php");
    die();
}

/**
 * Checks if a session has been started and is populated with data about the user
 * returns  -   whether or not a session has started
 */
function checkSession()
{
    if( isset($_SESSION) && array_key_exists('username', $_SESSION) )
        return true;
    return false;
}

/**
 * Decrypts a message that contains a symmetric key
 * $msg     -   the whole message sent by the user with message delimiter of ":::"
 * returns  -   the key that was sent, or false if decryption fails
 */
//NOTE: this still doesn't work so I unhooked it
function decrypt( $msg )
{
    $key = openssl_get_privatekey( file_get_contents( $_SERVER['PRIV_KEY'] ) );

    if( openssl_private_decrypt($msg, $decrypted, $key, OPENSSL_PKCS1_PADDING) )
    {
        return $decrypted;
    }
    return $msg;
}

/**
 * Checks a given token to see if it is valid
 * $token       -   the token given by the user
 * $user        -   the user that sent the token
 * $timestamp   -   a timestamp representing when the token was created
 * returns      -   whether or not the token is valid
 */
function checkToken( $token, $user, $timestamp )
{
    if( hash_hmac("sha256", $timestamp . $user, $_SERVER['HASH_KEY']) == $token )
        return true;
    return false;
}

/**
 * Creates a token for a given user
 * $user    -   the user to create a token for
 * returns  -   an associative array of the token created and the timestamp when the token was created
 *      keys are "token" and "timestamp", respectively
 */
function createToken( $user )
{
    $timestamp = date('Y-m-d H:i:s');

    $token = hash_hmac("sha256", $timestamp . $user, $_SERVER['HASH_KEY']);

    return array( "timestamp" => $timestamp, "token" => $token );
}

/**
 * Sanitizes a string of HTML entities and special characters
 * $string  -   the string to sanitize
 * returns  -   the sanitized string
 */
function sanitize( $string )
{
    return htmlentities( htmlspecialchars( $string ) );
}

/**
 * Verifies the user's stage to make sure they should access the given page
 * $stage   -   the user's stage that should be verified
 * $correct -   the stage that should be verified against; the correct stage the user should be on
 * returns  -   whether or not the stage was correct
 */
function checkStage( $stage, $correct )
{
    switch($correct)
    {
        case "start":
            if( $stage == "start" || $stage == "guessed" )
                return true;
            break;

        case "drawing":
            if( $stage == "drawing" || $stage == "start" )
                return true;
            break;

        case "guessing":
            if( $stage == "guessing" || $stage == "drawing" )
                return true;
            break;

        case "guessed":
            if( $stage == "guessed" || $stage == "guessing" )
                return true;
            break;
        
        default:
            dies("Error");
            break;
    }

    //should not be reached if stage was correct
    //redirect to the correct page
    switch( $stage )
    {
        case "start":
            header("Location: waiting.php");
            return false;
        
        case "drawing":
            header("Location: draw.php");
            return false;

        case "guessing":
            header("Location: guess.php");
            return false;

        case "guessed":
            header("Location: results.php");
            return false;

        default:
            dies("Error");
            break;
    }
}
?>