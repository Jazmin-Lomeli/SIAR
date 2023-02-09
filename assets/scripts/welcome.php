<?php
// Inicializar la sesion
session_start();

// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../login.php");
    // exit;
}

require_once '../config/config.php';

$conexion = $link;
if (!$conexion) {
  header('Location: ../../login.php');
}

 $user_name = $_SESSION['username'];

/* Establecer hora de méxico */
date_default_timezone_set("America/Mexico_City");
$fechaActual = date('Y-m-d H:i:s');

/* El usuario es Admin */

  /* Modificar el campo de Ultimo_log */
  mysqli_query($link, "UPDATE users SET ultimo_log = '$fechaActual' WHERE username = '$user_name'")
    or
    die(" Ooop, algo salio mal =(");
 /* Direccionar a la pagina segun el usuario */
  header('Location: ../../admin_reg.php');
  
 $conexion = $link;
/* Si no esta loggedo regresar a login */
if (!$conexion) {
  exit;
  header('Location: ../../login.php');
}

?>