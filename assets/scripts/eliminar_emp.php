<?php

// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../../login.php");
  //exit;
}

require_once '../config/config.php';
 
$id = $_GET['id'];
 
if(empty($_GET['id'])){
    header('Location:  ../../empleado_detalles.php?id='.$id.'&info=-&mensaje=error');
}else{
/* En la base de datos el ON DELETE esta en CASCADE, se borran todos los registros con el ID de la BD  */ 

    $tabla_user = mysqli_query($link, "DELETE FROM empleados WHERE id ='$id'");
  
    if($tabla_user == TRUE){
        header('Location: ../../admin_reg.php?mensaje=eliminado');
    }else{
        die("No se puede borrar el registro");
        header('Location: ../../admin_reg.php?mensaje=error');
    }
}
?>