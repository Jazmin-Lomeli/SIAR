<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

$id = $_GET['id'];
$reporte_mes = $_GET['mes'];


if($reporte_mes < 10){
    $aux = "0" . $reporte_mes;
    $reporte = $aux;
}
$fechaActual = date('Y');
$inicio = $fechaActual . "-" . $reporte . "-01";
$fin = $fechaActual . "-" . $reporte . "-31";

$query = "SELECT * FROM asistencia Where id_emp= '$id' AND fecha BETWEEN '$inicio' AND '$fin'";
$result = mysqli_query($link, $query);
 
$datos = "SELECT * FROM empleados INNER JOIN huella ON id = id_emp Where id= '$id' LIMIT 1";
$result_datos = mysqli_query($link, $datos);
while($mostrar = mysqli_fetch_array($result_datos)) {
    $nombre = $mostrar['nombre'] . " " . $mostrar['apellido'] . " " . $mostrar['seg_apellido'];
    $tel = $mostrar['telefono'];
    $id_huella = $mostrar['id_huella'];

}

 class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    $this->SetFont('Arial','',14);     
    $this->Cell(185,7,'Reporte mensual de asistencia',0,1,'C');  
}
// Pie de página
 
}

$pdf = new PDF();    
$pdf->AliasNbPages();    // siempre s genere el pie de pagina
$pdf->AddPage();
$pdf->Ln(5);

$pdf->SetFont('Arial','B',12);     
$pdf->Cell(19, 6,utf8_decode("Nombre: "),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(100, 6,utf8_decode($nombre),0,0,'L'); 
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(21, 6,utf8_decode("Teléfono: "),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(30, 6,utf8_decode($tel),0,1,'L');

$pdf->SetFont('Arial','B',12);     
$pdf->Cell(35, 6,utf8_decode("ID de empleado: "),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(10, 6,utf8_decode($id),0,0,'L'); 
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(35, 6,utf8_decode("ID  de huella: "),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(39, 6,utf8_decode($id_huella),0,0,'L');
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(39, 6,utf8_decode("Fecha de emisión: "),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(35, 6,utf8_decode(date('Y-m-d')),0,1,'L');

$pdf->Ln(8);
 


if ($result = mysqli_query($link, "SELECT * FROM asistencia Where id_emp= '$id' AND fecha BETWEEN '$inicio' AND '$fin'")) {

    /* determinar el número de filas del resultado */
    $asistencias = mysqli_num_rows($result);

    if(  $asistencias  == 0){

    

    $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(180,13,utf8_decode("Reporte de asistencia del mes de $meses[$reporte_mes]"),0,1,'C',0);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(45,10,utf8_decode('Fecha'),1,0,'C',0);
	$pdf->Cell(45,10,'Hora de entrada',1,0,'C',0);
	$pdf->Cell(45,10,'Hora de salida',1,0,'C',0);
	$pdf->Cell(45,10,utf8_decode('Observación'),1,1,'C',0);

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(180,15,'No hay registros de asisntecias para el mes seleccionado',1,1,'C');
    /* cerrar el resulset */
    mysqli_free_result($result);
    } else {
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(180, 13, utf8_decode("Reporte de asistencia del mes de $meses[$reporte_mes]"), 0, 1, 'C', 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(45, 10, utf8_decode('Fecha'), 1, 0, 'C', 0);
        $pdf->Cell(45, 10, 'Hora de entrada', 1, 0, 'C', 0);
        $pdf->Cell(45, 10, 'Hora de salida', 1, 0, 'C', 0);
        $pdf->Cell(45, 10, utf8_decode('Observación'), 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);

        $query = "SELECT * FROM asistencia Where id_emp= '$id' AND fecha BETWEEN '$inicio' AND '$fin' ORDER BY fecha ASC";
        $result = mysqli_query($link, $query);

        while ($mostrar = mysqli_fetch_array($result)) {
            $fecha = $mostrar['fecha'];
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
            $pdf->Cell(45, 8, $fecha, 1, 0, 'C', 0);
            $pdf->Cell(45, 8, $entrada, 1, 0, 'C', 0);
            $pdf->Cell(45, 8, $salida, 1, 0, 'C', 0);
            $pdf->Cell(45, 8, $observacion, 1, 1, 'C', 0);

        }
    }
     
   

    
}





 
$pdf->Output();

?>
