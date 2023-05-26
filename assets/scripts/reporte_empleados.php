<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

/* estabelcer rangos de fechas para el reporte */
date_default_timezone_set("America/Mexico_City");
$fechaActual = date('Y-m-d');



class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', '', 14);
        $this->Cell(185, 7, 'Reporte de empleados registrados ', 0, 1, 'C');
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
$pdf->AliasNbPages(); // Generar pagina
$pdf->AddPage();
$pdf->Ln(7);

$pdf->Ln(2); // espaciado

$meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(12, 10, utf8_decode(' '), 0, 0, 'C', 0);

$pdf->Cell(170, 10, utf8_decode("Fecha de emisión: $fechaActual"), 0, 1, 'R', 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(13, 10, utf8_decode('ID'), 1, 0, 'C', 0);

$pdf->Cell(70, 10, utf8_decode('Departamento'), 1, 0, 'C', 0);
$pdf->Cell(95, 10, 'Nombre completo', 1, 1, 'C', 0);

$query = "SELECT * FROM empleados INNER JOIN tipo_empleado ON empleados.tipo=tipo_empleado.tipo  Order BY tipo_empleado.t_nombre";
$result = mysqli_query($link, $query);
$pdf->SetFont('Arial', '', 12);

date_default_timezone_set('America/Mexico_City');
$fin = date("Y-m-d");
//$intervalo = $fechaInicio->diff($fechaFin);

while ($mostrar = mysqli_fetch_array($result)) {
    $pdf->Cell(13, 8, utf8_decode($mostrar['id']), 1, 0, 'C', 0);
    $pdf->Cell(70, 8, utf8_decode($mostrar['t_nombre']), 1, 0, 'L', 0);
    $pdf->Cell(95, 8, utf8_decode($mostrar['nombre'] . " " . $mostrar['apellido'] . " " . $mostrar['seg_apellido']), 1, 1, 'L', 0);
}
$pdf->Output();
 


?>