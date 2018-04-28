<?php
    if( isset($_SESSION) && array_key_exists("error", $_SESSION) )
    {
        echo "<div class='error' id='error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    else
    {
        echo "<div class='error' id='error' style='display: none'></div>";
    }
?>