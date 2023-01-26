<?php
 /* Mostrar registros y hacer la busqueda en tiempo real */ 
$sql = '';
require '../config/config.php';

/* Un arreglo de las columnas a mostrar en la tabla */
$columns = ['id', 'username', 'Rol', 'f_ingreso', 'ultimo_log'];

/* Nombre de la tabla */
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
$sql = "SELECT " . implode(", ", $columns) . " FROM $table
 
$where ";
$resultado = $link->query($sql);
$num_rows = $resultado->num_rows;

/* Mostrado resultados */
$html = '';

if ($num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['username'] . '</td>';
        $html .= '<td>' . $row['Rol'] . '</td>';
        $html .= '<td>' . $row['f_ingreso'] . '</td>';
        $html .= '<td>' . $row['ultimo_log'] . '</td>';
        $html .= "<td>
        <a href='editar_user.php?id=".$row['id']."'>  
            <abbr title='Cambiar contraseña'>
                <button  class='btn btn-primary'><i class='bi bi-key'></i></button>
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
/*
    En caso de no encontrar nada imprime sin resultados 
 */
echo json_encode($html, JSON_UNESCAPED_UNICODE);

?>