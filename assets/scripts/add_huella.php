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
$status_huella = mysqli_query($link, "UPDATE users SET huella = '$change_status' WHERE id = '$id_add'");
$status_fingerprint = mysqli_query($link, "UPDATE arduino SET finger_status = '$change_status_finger'");
/* Verificamos la transacción */
if ($status_huella == TRUE && $status_fingerprint == TRUE) {
/* Si todo es correcto agregamos el id de empleado para que nos genere un ID de Huella */
    $sql = "INSERT INTO huella (id_emp) VALUES (?)";

    if ($stmt = mysqli_prepare($link, $sql)) {

        mysqli_stmt_bind_param($stmt, "s", $id_add);

        if (mysqli_stmt_execute($stmt)) {
            header("location: ../../vista_user.php?messaje=add");
        } else {
            header('Location: ../../reg_huella.php?mensaje=error');
        }

    }
} else {
    /* Si no se hace la transaccion retornar a reg_huella */
    die(" No se puede Modificar el registro ");
    header('Location: ../../vista_user.php?mensaje=error');
    exit();
}

?>