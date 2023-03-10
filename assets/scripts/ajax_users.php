<?php
// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../../login.php");
  // exit;
}
$user = $_SESSION['username'];
/* Mostrar registros y hacer la busqueda en tiempo real */ 
$sql = '';
require '../config/config.php';

/* Un arreglo de las columnas a mostrar en la tabla */
$columns = ['id', 'username', 'ultimo_log', 'f_ingreso'];

/* Nombre de las tablas */
$table = "users";

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
$sql = "SELECT " . implode(", ", $columns) . " FROM ". " $table " .  " $where ". "ORDER BY $table.id";
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
        
        if($user == $row['username']) {
             $html .= '<td style=" font-style: italic; font-weight: bold" >' . $row['username'] .'</td>';
        }else{
            $html .= '<td>' . $row['username'] .'</td>';
        }

        $html .= '<td>' . $row['f_ingreso'] . '</td>';
        $html .= '<td>' . $row['ultimo_log'] . '</td>';
        $html .= '</tr>';
    }
} else {
    $html .= '<tr>';
    $html .= '<td colspan="5">Sin resultados</td>';
    $html .= '</tr>';
}

echo json_encode($html, JSON_UNESCAPED_UNICODE);

?>