<?php
require_once "/home/MAIN/lxj7261/Sites/442/project/helpers/Lib.php";

class DB
{
    private $conn;

    /**
     * Creates a connection to the database
     */
    function __construct()
    {
        try
        {
            $this -> conn = new PDO("mysql:host={$_SERVER['DB_SERVER']};dbname={$_SERVER['DB']}",
                $_SERVER['DB_USER'], $_SERVER['DB_PASSWORD']);
        }
        catch(PDOException $e)
        {
            echo "Connection failed";
            die();
        }
    }

    /**
     * Queries the database with given options
     * $query   -   the query to prepare and execute
     * $params  -   the array of parameters to add to execution
     * $class   -   (optional) the class name of the objects to return from the query (from FETCH_CLASS)
     * returns  -   if the query is a select statement, it returns the data from the query in either
     *                  class form or array form, depending on the $class parameter. If it is any other
     *                  query, the number of affected rows is returned. -1 is returned on error
     */
    function query( $query, $params = array(), $class = "" )
    {
        try
        {
            $stmt = $this -> conn -> prepare($query);
            $stmt -> execute( $params );

            //return the objects for a select query and the affected rows for others
            if( strtolower(explode(" ", $query)[0]) == 'select' )
            {
                if( $class )
                {
                    $data = $stmt -> fetchAll(PDO::FETCH_CLASS, $class);
                }
                else
                {
                    $data = $stmt -> fetchAll();
                }
            }
            else
            {
                $data = $stmt -> rowCount();
            }

            return $data;
        }
        catch(PDOException $e)
        {
            return -1;
        }
    }

    /**
     * Changes a user's stage in the database if possible
     * $user    -   the username to change the stage for
     * $room    -   the room the user is in
     * $stage   -   the stage to change to
     * returns  -   boolean indicating success or failure
     */
    function changeStage( $user, $room, $stage )
    { 
        //validation  
        if( !$this -> validateRoom($room) || !$this -> validateUser($user, $room) ||
            !$this -> validateStage($stage) )
        {
            return false;
        }

        $query = "UPDATE Rooms SET stage = ? WHERE username = ? AND roomCode = ?";
        $numRows = $this -> query( $query, array($stage, $user, $room) );

        if( $numRows > 0 )
        {
            $_SESSION['stage'] = $stage;
            return true;
        }
        return false;
    }

    /**
     * Gets the number of players in a room
     * $type    -   the type of data to return (options are 'in' and 'out'). Type 'in' returns
     *      the number of players who are in the room. Type 'out' returns the number of players
     *      who are in the room, but are not in the given stage.
     * $room    -   the room code to get numbers for
     * $stage   -   (optional) the stage to get numbers for
     * returns  -   the number of players included in the given type or -1 if input was invalid
     */
    function numPlayers( $room, $stage = '' )
    {
        if( !$this -> validateRoom($room) )
            return -1;

        if($stage)
        {
            if( !$this -> validateStage($stage) )
                return -1;
            
            //count players in the room that are not at the given stage yet
            $query = "SELECT roomCode, stage FROM Rooms WHERE roomCode = ? AND stage != ?";
            $data = $this -> query( $query, array($room, $stage) );
        }
        else
        {
            //count players within the room
            $query = "SELECT roomCode FROM Rooms WHERE roomCode = ?";
            $data = $this -> query( $query, array($room) );
            unset( $data[0] );//don't count user
        }
        return count($data);
    }

    /**
     * Gets chat messages from the DB for a given room
     * $room    -   the room code of the room to retreive messages for
     * $numMsg  -   the number of messages to retreive
     * returns  -   an array of all of the rows of messages requested
     */
    function getChat( $room, $numMsg )
    {
        if( !$this -> validateRoom($room) )
            return -1;

        $query = "SELECT * FROM Chat WHERE roomCode = ? ORDER BY timeSent DESC LIMIT $numMsg";
        $data = $this -> query( $query, array($room) );

        return $data;
    }

    /**
     * Adds a chat message to the DB under a certain user and room code
     * $room    -   the room to add the message under
     * $user    -   the user who sent the message
     * $msg     -   the message that was sent
     * returns  -   the number of rows affected by the query or -1 if input was invalid
     */
    function addChat( $room, $user, $msg )
    {
        if( !$this -> validateRoom($room) || !$this -> validateUser($user, $room) )
            return -1;

        $message = sanitize($msg);

        $query = "INSERT INTO Chat VALUES( ?, ?, ?, ? )";
        $data = $this -> query( $query, array( date('Y-m-d H:i:s'), $user, $room, $message ) );

        return $data;
    }

    /**
     * Assigns a given user from a room the stage of 'forfeit' and wipes the room
     * $user    -   the user to forfeit in the database
     * $room    -   the roomCode of the room the user was in
     * returns  -   the number of affected rows or -1 if the call was invalid
     */
    function forfeit( $user, $room )
    {
        if( !$this -> validateRoom($room) || !$this -> validateUser($user, $room) )
            return -1;

        $query = "UPDATE Rooms SET stage = 'forfeit' WHERE username = ? AND roomCode = ?";
        $data = $this -> query( $query, array($user, $room) );

        $this -> wipeRoom( $room );
        return $data;
    }

    /**
     * Deletes a given user from a room and wipes the room
     * $user    -   the user to delete from the database
     * $room    -   the roomCode of the room the user was in
     * returns  -   the number of affected rows or -1 if the call was invalid
     */
    function deleteUser( $user, $room )
    {
        if( !$this -> validateRoom($room) || !$this -> validateUser($user, $room) )
            return -1;
        
        $query = "DELETE FROM Rooms WHERE username = ? AND roomCode = ?";
        $data = $this -> query( $query, array($user, $room) );

        $this -> wipeRoom( $room );
        return $data;
    }

    /**
     * Deletes all data from a given room if all users in the room have forfeit
     * $room    -   the roomCode of the room to wipe
     */
    function wipeRoom( $room )
    {
        if( !$this -> validateRoom($room) )
            return;

        $query = "SELECT stage FROM Rooms WHERE roomCode = ?";
        $data = $this -> query( $query, array($room) );

        if( count($data) < 1 )
            return;

        //ensure all players have left the room
        foreach( $data as $row )
        {
            if( $row['stage'] != "forfeit" )
            {
                return;
            }
        }

        //if there are no more active players in the room, wipe it
        $query = "DELETE FROM Chat WHERE roomCode = ?";
        $this -> query( $query, array($room) );
        
        $query = "DELETE FROM Rooms WHERE roomCode = ?";
        $this -> query( $query, array($room) );
    }

    /**
     * Validates a roomCode value against the database
     * $room    -   the roomCode to validate
     * returns  -   whether or not the room code is valid
     */
    function validateRoom( $room )
    {
        if( strlen($room) != 5 || preg_match('/[^a-zA-Z]/', $room) )
        {
            return false;
        }

        //make sure room exists
        $query = "SELECT roomCode FROM Rooms WHERE roomCode = ?";
        $data = $this -> query($query, array($room));

        //doesn't exist
        if( count($data) <= 0 )
        {
            return false;
        }
        return true;
    }

    /**
     * Validates a username
     * $user    -   the username to validate
     * $room    -   (optional) the roomCode that the user is in if you want to check that 
     *      the user exists in that room
     * returns  -   whether or not the username is valid
     */
    function validateUser( $user, $room = '' )
    {
        if( strlen($user) > 20 || $user == "" || preg_match('/[^a-zA-Z\d]/', $user) )
        {
            return false;
        }

        //check that user exists in room in DB
        if( $room != '' )
        {
            if( !$this -> validateRoom($room) )
                return false;

            $query = "SELECT username FROM Rooms WHERE username = ? AND roomCode = ?";
            $data = $this -> query( $query, array($user, $room) );

            if( count($data) < 1 )
                return false;
        }
        return true;
    }

    /**
     * Validates a stage
     * $stage   -   the stage to validate
     * returns  -   whether or not the given stage name is valid in the DB
     */
    function validateStage( $stage )
    {
        $stages = array('start', 'drawing', 'drawn', 'guessing', 'guessed', 'forfeit');
        
        if( !in_array( $stage, $stages ) )
            return false;
        return true;
    }
}
?>