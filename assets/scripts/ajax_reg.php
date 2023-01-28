<?php
// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../../login.php");
  // exit;
}

/* Mostrar registros y hacer la busqueda en tiempo real */ 
$sql = '';
require '../config/config.php';

/* Un arreglo de las columnas a mostrar en la tabla */
$columns = ['id', 'nombre', 'apellido', 'seg_apellido', 'telefono', 'id_huella', 'id_emp'];

/* Nombre de las tablas */
$table = "empleados";
$table2 = "huella";


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

 /* Consulta */
$sql = "SELECT " . implode(", ", $columns) . " FROM ". " $table " . " LEFT JOIN ". $table2. " ON  $table.id=$table2.id_emp " .  " $where ". "ORDER BY $table.id";
$resultado = $link->query($sql);
$num_rows = $resultado->num_rows;

/* Mostrado resultados */
$html = '';
$vacio = "-";
/* Mostramos su nombre, telefono y el ID de la huella que se tiene an la tabla huella */ 
if ($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['nombre'] . " " . $row['apellido'] . " ". $row['seg_apellido'].'</td>';
        $html .= '<td>' . $row['telefono'] . '</td>';
       
        if(is_numeric($row['id_huella'])){
            $html .= '<td>' . $row['id_huella'] . '</td>';
        }else{
            $html .= '<td>' .$vacio .'</td>';
        }
        $html .= "<td>
        
        <a href='empleado_detalles.php?id=".$row['id']."&info=-'>  
            <abbr title='Detalles'>
                <button  class='btn btn-outline-primary '><i class='bi bi-info-square p-1'></i></button>
             </abbr>
        </a>
        </td>";
 
/*editar_emp.php?id=".$row['id']."  */ 
        
/*
DELETE FROM users WHERE id=6;
onclick="alerta()"
*/
        $html .= '</tr>';
    }
} else {
    $html .= '<tr>';
    $html .= '<td colspan="5">Sin resultados</td>';
    $html .= '</tr>';
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);

?>