<?php
// Llamamos al archivo de configuracion para conectarnos a la base de datos 
require_once 'assets/config/config.php';

/* Establecemos la fecha y la hora actual */
date_default_timezone_set("America/Mexico_City");
$fechaActual = date('Y-m-d');
$Hora_actual = date('h:i:s');

/*  Datos que manda el arduino por el metodo POST  */
$confirmacion = $_POST['transaccion_hecha'];
$finger_err = $_POST['finger_err'];
$id_emp = $_POST['id'];
$confirma_transaccion = $_POST['error'];
 
/* inicializamos variables */ 
$empleado_asistencia = " ";
$cambio = " ";

/* Extraemos el estado del sistema, para mandarlo a la esp32 */ 
$query = "SELECT * FROM `arduino`";
$resultado = mysqli_query($link, $query);

foreach ($resultado as $row) {
    $mode_sensor_huella = $row['finger_status']; /* indica que mode se pondra en sensor de huella */
    $hay_error = $row['finger_err'];            /* Indica si hay algun error */
}

////////////////  ENVIAR A ESP32 /////////////////

/* Si se encuentra en el modo REGISTER */ 
if ($mode_sensor_huella == "REGISTER") {
    $id_manipular = "-";                       // Mandamos ID como - 
} else {
/*Si el modo es ENROLL O DELETE */
// Extraemos el ID mayor de la tabla de huella */ 
    $consulta = "SELECT MAX(id_huella) AS id FROM huella";
    $resultado = mysqli_query($link, $consulta);
    $linea = mysqli_fetch_array($resultado);
    $id_manipular = $linea['id'];             //   Se mandará a la esp32
    $cambio = "REGISTER";                     //   Se mandará a la esp32
}


////////////////  RECIBE DE ESP32 /////////////////

/* Segun lo que recibe evalua */

/* LO que se RECIBE  es:
finger_err        --> Si el lector de huella si esta funcionando 
transaccion_hecha --> La transaccion que se realizó
ID                --> ID que se manipulo  
error             --> Si lan transaccion se realizo de manera correcta 
*/

/* Si agrego una nueva heulla */ 
if ($confirmacion == "add" && $finger_err == "Todo_bien") {
    if ($confirma_transaccion == "correcto") {
        /* Cambioamos modo de EROLL a REGISTER */
        $a = mysqli_query($link, "UPDATE arduino SET finger_status = '$cambio' ");
        if ($a == TRUE) {
            /* SE  registro la huella, hacer el cmabio del status del sensor */
            header('Location: vista_user.php?mensaje=correcto');
            $mode_sensor_huella = "REGISTER";
            $id_manipular = "-";
            echo $mode_sensor_huella . "/" . $id_manipular;
            header("location: ../../vista_user.php?messaje=add");
        } else {
            echo "ERROR ";
        }

    }
/* Si borro una heulla */ 
} elseif (($confirmacion == "delete" && $finger_err == "Todo_bien")) {   

///AUN NO ESTÁ DESARROLLADO 

} elseif (($confirmacion == "register" && $finger_err == "Todo_bien")) {

    if ($id_emp != "-" || $id_emp != 0 && $confirma_transaccion == "correcto") {
        /* Revisa si el ID que recibio el lector de huella se encuentra registrado en la BD */
        $query = "SELECT * FROM huella WHERE id_huella = '$id_emp'";
        $resultado = mysqli_query($link, $query);
        foreach ($resultado as $row) {
            $empleado_asistencia = $row['id_emp'];
        }
        /* Busca si el ID ya cuenta con un registro del dia de hoy, si es asi registra salida, si no registra entrada*/ 
        $query = "SELECT count(*) as reg FROM asistencia WHERE id_emp = '$empleado_asistencia' AND fecha = ' $fechaActual' ";
        $result = mysqli_query($link, $query);
        $fila = mysqli_fetch_assoc($result);
        $total = $fila['reg'];  

        if ($total == 0) {
            // Prepare an insert statement
            $add_entrada = "INSERT INTO asistencia (id_emp, entrada, fecha) VALUES (?,?,?)";

            if ($stmt = mysqli_prepare($link, $add_entrada)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sss", $empleado_asistencia, $Hora_actual, $fechaActual, );
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to login page
                    header("location: ../../admin_asistencia.php?mensaje=agregado");
                } else {
                    header("location: ../../admin_asistencia.php?mensaje=error");
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }

        } 
/* Agregar salida */ 
        else {

            $add_salida = mysqli_query($link, "UPDATE asistencia SET salida = '$Hora_actual' WHERE id_emp = '$empleado_asistencia'");
            if ($add_salida == TRUE) {
                /* SE  registro la huella, hacer el cmabio del status del sensor */
                header('Location: vista_user.php?mensaje=correcto');
                $mode_sensor_huella = "REGISTER";
                $id_manipular = "-";
                echo $mode_sensor_huella . "/" . $id_manipular;
                header("location: ../../vista_user.php?messaje=add");
            } else {
                echo "ERROR ";
            }
        }
    } else {
    }
} elseif (($confirmacion == "error" && $finger_err == "Todo_bien")) {
    echo "\n LA TRANSACCION NO SE LLEVO";
} 

if ($id_manipular == "-") {
    echo $mode_sensor_huella . "/-";
} elseif ($mode_sensor_huella == "ENROLL") {
    echo $mode_sensor_huella . "/" . $id_manipular;
}

?>