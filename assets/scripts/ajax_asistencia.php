<?php
/* Seguridad de Sesiones */
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../../login.php");
  // exit;
}

require '../config/config.php';

$conexion = $link;
if (!$conexion) {
  header('Location: ../../login.php');
}

 /* Mostrar registros y hacer la busqueda en tiempo real */ 
$sql = '';
 
/* Un arreglo de las columnas a mostrar en la tabla */
$columns = ['id', 'nombre', 'apellido', 'seg_apellido','id_emp', 'entrada', 'fecha', 'salida', 'observacion'];

/* Nombre de las tablas */
$table = "empleados";
$table2 = "asistencia";

$campo = isset($_POST['campo']) ? $link->real_escape_string($_POST['campo']) : null;

/* Filtrado */
$where = '';
 if ($campo != null) {
    $where = "WHERE (";

    $cont = count($columns);
    for ($i = 0; $i < $cont; $i++) {
        $where .= $columns[$i] . " LIKE '%" . $campo . "%' OR ";
    }
    $where = substr_replace($where, "", -3);
    $where .= ")";
}
/* implode(", ", $columns) . "*/
date_default_timezone_set("America/Mexico_City");      
$fecha_hoy = date("Y-m-d");   
/* Consulta */
$sql = "SELECT  ". implode(", ", $columns) . " FROM " . " $table " . "INNER JOIN ". $table2 . " ON  $table.id=$table2.id_emp AND $table2.fecha='$fecha_hoy' ".  " $where ". "ORDER BY $table2.entrada"; 


$resultado = $link->query($sql);

$num_rows = $resultado->num_rows;

;/* Mostrado resultados */
$html = '';
/* Motrar los datos enconrados */ 
if ($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['nombre'] . " ". $row['apellido'] . " ". $row['seg_apellido'] . '</td>';
        $html .= '<td>' . $row['fecha'] . '</td>';

        if( $row['observacion'] != NULL && $row['entrada'] == '00:00:00' && $row['salida'] == '00:00:00'){

            $html .= '<td>' . "Justificada" . '</td>';
            $html .= '<td>' . "Justificada" . '</td>';
            $html .= "<td>
             
                <abbr title='$row[observacion]' >
                    <button  class='btn btn-outline rounded-circle'> Motivo </button>
                </abbr>
             
            </td>";

        }else{

            if ($row['entrada'] < '08:16:00') {         // entrada antes de las 8:15
                $html .= '<td>' . $row['entrada'] . '</td>';
            } else {                                    // Entrada despues de las 8:15
                $html .= '<td style="background-color: rgba(255, 0, 0, 0.6)" >' . $row['entrada'] . '</td>';
            }
            $html .= '<td>' . $row['salida'] . '</td>';
            $html .= "<td>
            <a href='empleado_detalles.php?id=".$row['id']."&info=salida'>  
                <abbr title='Registrar salida manualmente'>
                    <button  class='btn btn-outline-success ml-2' ><i class='bi bi-box-arrow-right'></i></button>
                </abbr>
             </a>   
            </td>";

            
        }

 
        $html .= '</tr>';
    }
} else {
    $html .= '<tr>';
    $html .= '<td colspan="6">Sin resultados</td>';
    $html .= '</tr>';
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);

?>