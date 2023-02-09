<?php

// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../../../config/config.phpogin.php");
  //exit;
}
require_once '../config/config.php';

 
$id = $_GET['id'];

 if(empty($_GET['id'])){
    header('Location:  ../../admin_recordatorios.php?id='.$id.'&mensaje=error');
}else{
/* En la base de datos el ON DELETE esta en CASCADE, se borran todos los registros con el ID de la BD  */ 

    $tabla_user = mysqli_query($link, "DELETE FROM recordatorios WHERE id_recordatorio ='$id'");
  
    if($tabla_user == TRUE){
        header('Location: ../../admin_recordatorios.php?id='.$id.'&mensaje=deleted');
    }else{
        die("No se puede borrar el registro");
        header('Location: ../../admin_recordatorios.php?id='.$id.'&mensaje=error');
    }
}
?>