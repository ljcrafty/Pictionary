<div id="chat">
    <div id="messages">
        <?php
            require_once "/home/MAIN/lxj7261/Sites/442/project/inc/consts.inc.php";
            require_once HOME . "classes/DB.class.php";
            require_once HELPERS . "Lib.php";
            
            sessionStart();
            $db = new DB();
            $temp = "";

            $data = $db -> getChat( $_SESSION['room'], 15 );

            foreach( $data as $row )
            {
                $bool = $row['username'] == $_SESSION['username'];
                $mine = ( $bool ? "mine" : "other" );
                $time = date( 'g:i', strtotime($row['timeSent']) );
                $date = date( 'n/j', strtotime($row['timeSent']) );
                $temp = "<div class='message $mine'>
                    <p class='date $mine'>$date <br/>" . $row['username'] . " <i>$time</i></p>
                    <span class='" . ( $bool ? "me" : '' ) . "'>" . $row['chatMsg'] . "</span>
                    </div>" . $temp;
            }
            echo $temp;
        ?>
    </div>
    <form method="POST" onsubmit="newChat(); return false;" id="messageInput">
        <textarea name="message" id="message" placeholder="Type a message..."></textarea>
        <button type="submit">Send</button>
    </form>
</div>