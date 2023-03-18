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
$id_manipular = "-";
$nombre_r = $descripcion_r = $inicio_r = $fin_r = $caracter = " ";
$aviso = $a_particular = "";
$aux_aviso = 0;


/* Extraemos el estado del sistema, para mandarlo a la esp32 */
$query = "SELECT * FROM `arduino`";
$resultado = mysqli_query($link, $query);

foreach ($resultado as $row) {
    $mode_sensor_huella = $row['finger_status']; /* indica que mode se pondra en sensor de huella */
    $hay_error = $row['finger_err']; /* Indica si hay algun error */
}

////////////////  ENVIAR A ESP32 /////////////////

/* Si se encuentra en el modo REGISTER */
if ($mode_sensor_huella == "REGISTER") {
    $id_manipular = "-"; // Mandamos ID como - 
} elseif ($mode_sensor_huella == "ENROLL") {
    // Si el modo es ENROLL  
   
    // Extraemos el id_a_manipular de la tabla auxiliar
    $consulta = "SELECT id_a_manipular AS id FROM huella_auxiliar";
    $resultado = mysqli_query($link, $consulta);
    $linea = mysqli_fetch_array($resultado);
    $id_manipular = $linea['id']; //   Se mandará a la esp32

// Limpiamos tabla auxiliar
    $huella_aux_reset = mysqli_query($link, "DELETE FROM huella_auxiliar");
    if ($huella_aux_reset == FALSE){
        echo "ERROR";
    }

}


////////////////  RECIBE DE ESP32 /////////////////

/* Segun lo que recibe evalua */

/* Lo que se RECIBE  es:
finger_err        --> Si el lector de huella si esta funcionando 
transaccion_hecha --> La transaccion que se realizó
ID                --> ID que se manipulo  
error             --> Si lan transaccion se realizo de manera correcta 
*/

/* Si agrego una nueva heulla */
if ($confirmacion == "add" && $finger_err == "Todo_bien") {
    if ($confirma_transaccion == "correcto") {
        /* Cambio de status */
        $cambio = "REGISTER";
        $aux_aviso = 0;
        /* Cambioamos modo de EROLL a REGISTER */
        $a = mysqli_query($link, "UPDATE arduino SET finger_status = '$cambio' ");
        if ($a == TRUE) {
            /* SE  registro la huella, hacer el cmabio del status del sensor */
            $id_manipular = "-";
            echo "REGISTER" . "/-";

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
            /* Nuevo registro de asistencia entrada */
            $add_entrada = "INSERT INTO asistencia (id_emp, entrada, fecha) VALUES (?,?,?)";

            if ($stmt = mysqli_prepare($link, $add_entrada)) {
                mysqli_stmt_bind_param($stmt, "sss", $empleado_asistencia, $Hora_actual, $fechaActual);
                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {

                    $tipo_emp = "";
                    $query = "SELECT tipo FROM empleados WHERE id = '$empleado_asistencia'";
                    $resultado = mysqli_query($link, $query);
                    foreach ($resultado as $row) {
                        $tipo_emp = $row['tipo'];
                    }

                    $nu_avisos = 0;
                    //  CAMBIAR EL 18  POR EL GENERAL
                    $recordatorios = "SELECT COUNT(*) FROM recordatorios Where recordatorios.r_tipo = 1 OR recordatorios.r_tipo = '$tipo_emp'";
                    $cont = mysqli_query($link, $recordatorios);
                    $res = mysqli_fetch_array($cont);
                    $nu_avisos = $res['COUNT(*)']; //   Se mandará a la esp32


                    if ($nu_avisos == NULL) {
                        $aviso = 0;
                    } else {
                        // REGISTER/si/listado de avisos
                        $aux_aviso = 1;
                        echo "AVISO/".$nu_avisos."/";

                        $mode_sensor_huella ="AVISO";
                        $cont = 0;
                       

                        /* Avisos particulares */
                        $query_avisos = "SELECT * FROM recordatorios LEFT JOIN tipo_empleado ON recordatorios.r_tipo=tipo_empleado.tipo  WHERE tipo_empleado.tipo= 1 OR tipo_empleado.tipo = '$tipo_emp'";
                        $res = mysqli_query($link, $query_avisos);
                        foreach ($res as $datos) {
                            if ($datos['tipo'] == $tipo_emp || $datos['tipo'] == 1) {
                                $cont++;
                                echo $datos['r_nombre'] . "#" . $datos['descripcion'] . "#" . $datos['inicio'] . "#" . $datos['fin'] . "#" . $datos['caracter'] . "$";
                            }

                        }
                    }

                    /*  $mode_sensor_huella = "REGISTER";
                    $id_manipular = "SI";*/

                    // Redirect to login page
                    header("location: ../../admin_asistencia.php?mensaje=agregado");
                } else {
                    header("location: ../../admin_asistencia.php?mensaje=error");
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }

        } /* Agregar salida */else {
            /* Registrar salida */
            $add_salida = mysqli_query($link, "UPDATE asistencia SET salida = '$Hora_actual' WHERE id_emp = '$empleado_asistencia'");
            if ($add_salida == TRUE) {

                //  CAMBIAR EL 18  POR EL GENERAL
                $recordatorios = "SELECT COUNT(*) AS num_avisos FROM recordatorios Where recordatorios.r_tipo = 18 OR recordatorios.r_tipo = '$id_emp'";
                $cont = mysqli_query($link, $recordatorios);
                $res = mysqli_fetch_array($cont);
                $nu_avisos = $res['num_avisos']; //   Se mandará a la esp32

                if ($nu_avisos == NULL) {
                    $aviso = 0;
                } else {
                    // REGISTER/si/listado de avisos

                    echo "AVISO/".$nu_avisos."/";
                    $mode_sensor_huella ="AVISO";
                    $cont = 0;
                    /* Avisos particulares */
                    $query_avisos = "SELECT tipo_empleado.t_nombre, recordatorios.descripcion, recordatorios.r_nombre, recordatorios.inicio, recordatorios.fin, recordatorios.caracter from recordatorios INNER JOIN tipo_empleado ON ( recordatorios.r_tipo = tipo_empleado.tipo OR recordatorios.r_tipo= 18)INNER JOIN empleados ON empleados.tipo = tipo_empleado.tipo WHERE empleados.id = '$id_emp'";
                    $res = mysqli_query($link, $query_avisos);
                    foreach ($res as $datos) {
                        $cont++;
                        echo $datos['r_nombre'] . "#" . $datos['descripcion'] . "#" . $datos['inicio'] . "#" . $datos['fin'] . "#" . $datos['caracter'] . "$" . $cont . "$";
                    }

                }





                /*   $mode_sensor_huella = "REGISTER";
                $id_manipular = "SI";
                $id_manipular = "-";
                */

                /* SE  registro la huella, hacer el cmabio del status del sensor 
                $mode_sensor_huella = "REGISTER";
                $id_manipular = "-";
                echo $mode_sensor_huella . "/" . $id_manipular;
                */



            } else {
                echo "ERROR ";
            }
        }
    } else {
    }
} elseif (($confirmacion == "error" && $finger_err == "Todo_bien")) {
    echo "\n LA TRANSACCION NO SE LLEVO";
}

if ($id_manipular == "-" && $mode_sensor_huella == "REGISTER" && $aux_aviso == 0) {
    echo $mode_sensor_huella . "/-";
} elseif ($mode_sensor_huella == "ENROLL") {
    echo $mode_sensor_huella . "/" . $id_manipular;
}elseif ($mode_sensor_huella == "AVISO"){

}
 
?>