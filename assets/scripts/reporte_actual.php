<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

date_default_timezone_set("America/Mexico_City");
$fecha_rep = date('Y-m-d');
$fechaActual = " ";

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
        $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
        /* Establecer la hora de Mexico por que por defecto manda la del server  */
       
        $fechaActual = $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y") ;

        $this->SetFont('Arial', 'I', 14);
        $this->Cell(185, 8, $fechaActual, 0, 1, 'C');
        $this->Cell(185, 7, 'Reporte Actual de asistencia', 0, 1, 'C');
    }

    function Footer()
    {
        $this->SetY(-25); // Posición: a 1,5 cm del final
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(130);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(200, 5, utf8_decode('SIAR'), 0, 1, 'C');
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(200, 5, utf8_decode("Sistema de Asistencias y Rcordatorios"), 0, 1, 'C');
    }

}
$pdf = new PDF();
$pdf->AliasNbPages(); // siempre s genere el pie de pagina
$pdf->AddPage();
$pdf->Ln(5);  // espaciado

/* consulta correcta */
if ($result = mysqli_query($link, "SELECT * FROM asistencia Where fecha = '$fecha_rep'")) {
    /* determinar el número de filas del resultado */
    $asistencias = mysqli_num_rows($result);

    if(  $asistencias  == 0){  // No arroja registros 

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(12, 10, utf8_decode('ID'), 1, 0, 'C', 0);
        $pdf->Cell(60, 10, utf8_decode('Nombre'), 1, 0, 'C', 0);
        $pdf->Cell(30,10,utf8_decode('Área laboral'),1,0,'C',0);
        $pdf->Cell(30, 10, 'H. entrada', 1, 0, 'C', 0);
        $pdf->Cell(30, 10, 'H. salida', 1, 0, 'C', 0);
        $pdf->Cell(30, 10, utf8_decode('Observación'), 1, 1, 'C', 0);

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(180,15,'Por el momento NO hay registros de asistencias',1,1,'C');
    /* cerrar el resulset */
    mysqli_free_result($result);
    } else {   // Si hay registros
        
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(12, 10, utf8_decode('ID'), 1, 0, 'C', 0);
        $pdf->Cell(60, 10, utf8_decode('Nombre'), 1, 0, 'C', 0);
        $pdf->Cell(34,10,utf8_decode('Área laboral'),1,0,'C',0);
        $pdf->Cell(28, 10, 'H. entrada', 1, 0, 'C', 0);
        $pdf->Cell(28, 10, 'H. salida', 1, 0, 'C', 0);
        $pdf->Cell(30, 10, utf8_decode('Observación'), 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
/* extarer asistencias */ 
$query = "SELECT * FROM asistencia LEFT JOIN empleados ON asistencia.id_emp=empleados.id INNER JOIN tipo_empleado ON tipo_empleado.tipo = tipo_empleado.tipo Where tipo_empleado.tipo > 1 AND fecha = '$fecha_rep' ORDER BY entrada ASC";
$result = mysqli_query($link, $query);

while ($mostrar = mysqli_fetch_array($result)) {
    $id = $mostrar['id'];
    $nombre = $mostrar['nombre']." ".$mostrar['apellido']." ". $mostrar['seg_apellido'] ;
    $fecha = $mostrar['fecha'];
    $area = $mostrar['t_nombre'];

    $entrada = $mostrar['entrada'];
    if ($mostrar['entrada'] < "08:16:00") {
        $observacion = "- - ";
    } else {
        $observacion = "Retardo";
    }
    if ($mostrar['salida'] == null) {
        $salida = "- -";
    } else {
        $salida = $mostrar['salida'];
    }

    $pdf->Cell(12, 8, $id, 1, 0, 'C', 0);
    $pdf->Cell(60, 8, $nombre, 1, 0, 'L', 0);
    $pdf->Cell(34, 8, $area, 1, 0, 'L', 0);
    $pdf->Cell(28, 8,utf8_decode($entrada), 1, 0, 'C', 0);
    $pdf->Cell(28, 8, $salida, 1, 0, 'C', 0);
    $pdf->Cell(30, 8, $observacion, 1, 1, 'C', 0);
}
    } 
}


$pdf->Output('', 'I');

?>