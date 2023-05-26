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
$sql = $id = '';

/* Un arreglo de las columnas a mostrar en la tabla */
$columns = ['t_nombre', 'r_nombre', 'descripcion', 'inicio','fin', 'caracter', 'id_recordatorio'];

/* Nombre de las tablas */
$table = "tipo_empleado";
$table2 = "recordatorios";

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
    
/* Consulta */
 $sql = "SELECT  ". implode(", ", $columns) . " FROM " . " $table " . "INNER JOIN ". $table2 . " ON  $table.tipo=$table2.r_tipo ".  " $where ";

$resultado = $link->query($sql);

$num_rows = $resultado->num_rows;

;/* Mostrado resultados */
$html = '';
/* Motrar los datos enconrados */ 
if ($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        date_default_timezone_set("America/Mexico_City");
        $fecha_a = date('Y-m-d');
        $html .= '<tr>';
        $html .= '<td>' . $row['t_nombre'] . '</td>';
        $html .= '<td>' . $row['r_nombre']. '</td>';
        $html .= '<td>' . $row['descripcion'] . '</td>';
        $html .= '<td>' . $row['caracter'] . '</td>';
        $fin = $row['fin'];
        if($fin > $fecha_a) {

            $html .= '<td>' . $row['inicio'] . "- - ". $row['fin'] .'</td>';

        }else{
            
            $html .= '<td style="background-color: rgba(255, 0, 0, 0.7)" >' . $row['inicio'] . "- - ". $row['fin'] .'</td>';
            
        }
        
         $html .= "<td>
        <a href='assets/scripts/eliminar_recordatorio.php?id=".$row['id_recordatorio']."'>  
            <abbr title='Eliminar recordatorio'>
                <button  class='btn btn-outline-danger ml-2' ><i class='bi bi-trash3-fill'></i></button>
            </abbr>
         </a>   
        </td>";
 
        $html .= '</tr>';
    }
} else {
    $html .= '<tr>';
    $html .= '<td colspan="6">Sin resultados</td>';
    $html .= '</tr>';
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);

?>