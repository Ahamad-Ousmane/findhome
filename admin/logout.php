<?php
    
    session_start();
    session_destroy();
    header("Location: dash_user.php");
   
?>