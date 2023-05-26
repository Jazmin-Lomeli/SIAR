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
/* Cambio de status del lector de huella  a ENROLL*/
if ($status_fingerprint == TRUE) {
    /* Cambiar el estatus del registro de empleado  de 0 a 1*/
    $status_huella = mysqli_query($link, "UPDATE empleados SET huella = '$change_status' WHERE id = '$id_add'");
    if ($status_huella == TRUE) {

        /* Checar si la tabla huella tiene  un hueco que para el id:huella*/
        $id_manipular = 0;       
        $huecos_en_huella = 0;
        $cont = 1;
/* Extraemos todos los id´s en orden de menor a mayor */ 
        $query_avisos = "SELECT id_huella FROM huella ORDER by id_huella ASC";
        $res = mysqli_query($link, $query_avisos);
        foreach ($res as $datos) {

            if ($datos['id_huella'] == $cont) {
                $cont++; // Aumenta pues si existe
            } else {
                $id_manipular = $cont; /* este sera el ID a agregar a la tabla (SI HAY HUECO) */
               // echo $id_manipular;
                $huecos_en_huella = 1;
                  header("location: ../../admin_reg.php");
            }
        }

        /* si no hay hueco entonces genera un id nuevo con el AUTOINCREMENTABLE */
        if ($huecos_en_huella == 0){
            $sql = "INSERT INTO huella (id_emp) VALUES (?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $id_add);
/* como se genero eon el AuntoIncrement buscamos el id_huella mayor */ 
                if (mysqli_stmt_execute($stmt)) {

                    $consulta = "SELECT MAX(id_huella) AS id FROM huella";
                    $resultado = mysqli_query($link, $consulta);
                    $linea = mysqli_fetch_array($resultado);
                    $id_manipular = $linea['id']; //   Se mandará a la esp32
                    if($id_manipular == NULL){
                        $id_manipular = 1;
                    }else{
                       // echo "NO HAY HUECO";
                        $id_manipular;
                    }

/* Agregamos a la tabla aux el nuevo id_huella para que el archivo arduino_rep.php lo consulte en la tabla */ 
                   
                    $huella_aux_reset = mysqli_query($link, "UPDATE huella_auxiliar SET id_a_manipular = $id_manipular");

                     if ($huella_aux_reset == TRUE){
                        //echo $id_manipular;
                        header("location: ../../admin_reg.php");
                    }else{
                        header("location: ../../admin_reg.php");
                    }

        
                } else {
                    header('Location: ../../admin_reg.php');
                }


            }

        }// hueco en tabla huella 
        else {
            $sql = "INSERT INTO huella (id_emp, id_huella) VALUES (?,?)";
            if ($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "ss", $id_add, $id_manipular );
                if (mysqli_stmt_execute($stmt)){
                    $huella_aux_reset = mysqli_query($link, "UPDATE huella_auxiliar SET id_a_manipular = $id_manipular");

                     if ($huella_aux_reset == TRUE){
                        echo $id_manipular;
                        header("location: ../../admin_reg.php");
                    }else{
                        header("location: ../../admin_reg.php");
                    }

                }else{
                    header('Location: ../../admin_reg.php');

                }
            }
        }
    
    }// update status huella tabla embpleado
    // fingerprint
}else {
    /* Si no se hace la transaccion retornar a reg_huella */
    // ERROR

}

?>