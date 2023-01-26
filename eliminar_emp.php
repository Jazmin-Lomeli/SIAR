<?php
require_once 'assets/config/config.php';
 
$id = $_GET['id'];
 
if(empty($_GET['id'])){
    header('Location: admin_reg.php?mensaje=error');

   
}else{
/* En la base de datos el ON DELETE esta en CASCADE, se borran todos los registros con el ID de la BD  */ 

    $tabla_user = mysqli_query($link, "DELETE FROM users WHERE id ='$id'");
  
    if($tabla_user == TRUE){
        header('Location: admin_reg.php?mensaje=eliminado');
    }else{
        die("No se puede borrar el registro");
        header('Location: admin_reg.php?mensaje=error');
    }
}
?>