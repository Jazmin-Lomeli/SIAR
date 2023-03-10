<?php
require_once 'assets/config/config.php';

$change_status_finger = "REGISTER";
$status_fingerprint = mysqli_query($link, "UPDATE arduino SET finger_status = '$change_status_finger'");

if($status_fingerprint == TRUE){
    header('Location: ../../admin_reg.php');
}else{
    die("No se puede borrar el registro");
    header('Location: ../../admin_reg.php');
}

?>

