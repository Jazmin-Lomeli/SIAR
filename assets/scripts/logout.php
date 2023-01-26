<?php
// Initialize the session
session_start();

// Unset all of the session variables
$_SESSION = array();

/* Destruimos la session,  
    De esta forma aseguramos que cuando le de clic a cerrar sesion no puede regresarse con la flechita del navegador
*/
session_destroy();

// Redirect to login page
header("location: ../../login.php");
exit;
?>