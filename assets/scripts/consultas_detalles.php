<?php 


require_once 'assets/config/config.php';
date_default_timezone_set("America/Mexico_City");      
$fecha_act = date('Y-m-d');

$select = " ";
//  Consulta para extaer el area o tipo de empleado 
$query ="SELECT * FROM tipo_empleado";
$resultado = $link->query($query); 
$conexion = $link;

$area = $asig_area = $err_area = $motivo = $motivo_err = '';
$name = $tel = $fecha_r = $id_huella = $area = $id_huella = $f_reg = $status_huella = $area = $jornada = "";
// Detalles empleado 
$sql_dato = "SELECT * FROM empleados LEFT JOIN tipo_empleado ON empleados.tipo=tipo_empleado.tipo  LEFT JOIN huella ON huella.id_emp=empleados.id Where empleados.id = '$id_emp'";
$result = mysqli_query($link, $sql_dato);
while($mostrar = mysqli_fetch_array($result)) {
  $name = $mostrar['nombre'] ." ". $mostrar['apellido'] ." ". $mostrar['seg_apellido'] ;
  $tel = $mostrar['telefono'];
  $status_huella = $mostrar['huella'];
  $f_reg = $mostrar['f_registro'];
  $area = $mostrar['t_nombre'];
  $id_huella  = $mostrar['id_huella'];
  $jornada = $mostrar['jornada'];
}
// Aun no tiene ID de huella en el sensor 
if($status_huella == 0){
  $id_huella = "- -";
}

$fecha_a =  $h_entrada = $h_salida = $h_salida_err = "";

/* Verificar si el empleado ya cuenta con un registro de asistencia del dia actual*/ 

$asistencia_sql = "SELECT COUNT(*) FROM asistencia WHERE fecha = '$fecha_act' AND id_emp = '$id_emp'";
$asistencia = mysqli_query($conexion, $asistencia_sql);
$fila_asistencia = mysqli_fetch_assoc($asistencia);
$asistencia_status = $fila_asistencia['COUNT(*)'];
// No hay registro 
  if($asistencia_status == 0){
    $fecha_a = $fecha_act;
    $h_entrada = "- -";
    $h_salida = "- -";
} else { // si hay registro 
  $sql_asistencias = "SELECT * FROM `asistencia` WHERE id_emp = '$id_emp' AND fecha = '$fecha_act'";
  $result_a = mysqli_query($conexion, $sql_asistencias);
  while ($row = mysqli_fetch_array($result_a)) {
    $fecha_a = $row['fecha'];
    $h_entrada = $row['entrada'];
    if($row['salida'] == NULL){
      $h_salida = "- -";
    }else{
      $h_salida = $row['salida'];
    }
  }
}
 


?>