<?php
 
define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'no_olvides');
    
    $link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

    if($link -> connect_error){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
?>

