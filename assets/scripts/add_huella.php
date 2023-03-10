<?php

require '../config/config.php';
/* Id que manda el boton de agregar huella de la vista reg_huella.php */
$id_add = $_GET['id_add'];
/* Variables para cambiar el status de la base de datos */
$change_status_finger = "ENROLL";
$change_status = "1";

/* 
Cambiamos en la tabla de users a que ya tiene huella registrada 
Cambiamos el modo del sensor de huella a ENROLL 
*/
$status_fingerprint = mysqli_query($link, "UPDATE arduino SET finger_status = '$change_status_finger'");
/* Cambio de status del lector de huella */
if ($status_fingerprint == TRUE){
/* Cambiar el estatus del registro de empleado */
    $status_huella = mysqli_query($link, "UPDATE empleados SET huella = '$change_status' WHERE id = '$id_add'");
    if ($status_huella == TRUE){
/* Agregar huella en la tabla huella  */
        $sql = "INSERT INTO huella (id_emp) VALUES (?)";
        if ($stmt = mysqli_prepare($link, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $id_add);
    
            if (mysqli_stmt_execute($stmt)) {
                header("location: ../../admin_reg.php");
            } else {
                header('Location: ../../admin_reg.php');
            }
    
        }
    }

}else{
    /* Si no se hace la transaccion retornar a reg_huella */
    // ERROR

}
/*
    ?>
    <style>
        h2, h5{
            text-align: center;
        }
    </style>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">


    <br>
    <br>
    <br>
    
    <div class="contenedor">
        <h2> Error, no se puede realizar la acción</h2>
    </div>
    <br>
    <div class="contenedor">
        <h5> En este momento lector de huella no se encuentra conectado al sistema </h5>
    </div>
    <img src="../img/Error.gif" class="rounded mx-auto d-block" alt="..." style="width:25em;height:20rem">
    <br>
    <br>
    <div class="d-grid gap-2 col-2 mx-auto">
    <a href="../../admin_reg.php" type="button" class="btn btn-outline-primary btn-lg mx-3">Regresar</a>
    </div>
    <?php
    // header('Location: ../../vista_user.php?mensaje=error');
    // exit();
}*/
?>

