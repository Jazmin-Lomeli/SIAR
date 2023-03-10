<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

$id = $_GET['id'];
$reporte_mes = $_GET['mes'];

/* si el mes es menor a 10 agregar el 0 */
if($reporte_mes < 10){
    $aux = "0" . $reporte_mes;
    $reporte = $aux;
}else{
    $reporte = $reporte_mes;
}
/* estabelcer rangos de fechas para el reporte */ 
$fechaActual = date('Y');
$inicio = $fechaActual . "-" . $reporte . "-01";
$fin = $fechaActual . "-" . $reporte . "-31";

/* consulta  */
$datos = "SELECT * FROM empleados LEFT JOIN tipo_empleado ON empleados.tipo=tipo_empleado.tipo  LEFT JOIN huella ON huella.id_emp=empleados.id Where empleados.id ='$id' LIMIT 1";
$result_datos = mysqli_query($link, $datos);
$nombre = $tel = $id_huella = '';
while($mostrar = mysqli_fetch_array($result_datos)) {
    $nombre = $mostrar['nombre'] . " " . $mostrar['apellido'] . " " . $mostrar['seg_apellido'];
    $tel = $mostrar['telefono'];
    $id_huella = $mostrar['id_huella'];
    $a_laboral = $mostrar['t_nombre'];
}

class PDF extends FPDF{
// Cabecera de página
    function Header(){
        $this->SetFont('Arial','',14);     
        $this->Cell(185,7,'Reporte mensual de asistencia',0,1,'C');  
    }

    function Footer()
{
    $this->SetY(-25);// Posición: a 1,5 cm del final
    $this->SetFont('Arial','I',8);
    $this->SetTextColor(130);
    $this->SetFont('Arial','B',10);
    $this->Cell(200,5,utf8_decode('SIAR'),0,1,'C');
    $this->SetFont('Arial','I',8);
    $this->Cell(200,5,utf8_decode("Sistema de Asistencias y Rcordatorios"), 0,1,'C'); 
}

}

$pdf = new PDF();    
$pdf->AliasNbPages();    // Generar pagina
$pdf->AddPage();
$pdf->Ln(7);

//  Datos del empleado 
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(19, 8,utf8_decode("Nombre:_______________________________________"),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(100, 8,utf8_decode($nombre),0,0,'L'); 
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(21, 8,utf8_decode("Teléfono:_____________ "),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(30, 8,utf8_decode($tel),0,1,'L');
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(28, 8,utf8_decode("Área laboral:___________________________"),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(65, 8,utf8_decode($a_laboral),0,0,'L');

$pdf->SetFont('Arial','B',12);     
$pdf->Cell(34, 8,utf8_decode("ID de empleado:____"),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(10, 8,utf8_decode($id),0,0,'L'); 
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(28, 8,utf8_decode("ID  de huella:___"),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(39, 8,utf8_decode($id_huella),0,1,'L');
$pdf->SetFont('Arial','B',12);     
$pdf->Cell(39, 8,utf8_decode("Fecha de emisión:___________"),0,0,'L');  
$pdf->SetFont('Arial','',12);     
$pdf->Cell(35, 8,utf8_decode(date('Y-m-d')),0,1,'L');

$pdf->Ln(5);  // espaciado

/* consulta correcta */
if ($result = mysqli_query($link, "SELECT * FROM asistencia Where id_emp= '$id' AND fecha BETWEEN '$inicio' AND '$fin'")) {
    /* determinar el número de filas del resultado */
    $asistencias = mysqli_num_rows($result);

    if(  $asistencias  == 0){  // No arroja registros 

    $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio","Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(180,13,utf8_decode("Reporte de asistencia del mes de $meses[$reporte_mes]"),0,1,'C',0);
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(45,10,utf8_decode('Fecha'),1,0,'C',0);
	$pdf->Cell(45,10,'Hora de entrada',1,0,'C',0);
	$pdf->Cell(45,10,'Hora de salida',1,0,'C',0);
	$pdf->Cell(45,10,utf8_decode('Observación'),1,1,'C',0);

    $pdf->SetFont('Arial','',12);
    $pdf->Cell(180,15,'No hay registros de asistencias para el mes seleccionado',1,1,'C');
    /* cerrar el resulset */
    mysqli_free_result($result);
    } else {   // Si hay registros
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(180, 13, utf8_decode("Reporte de asistencia del mes de $meses[$reporte_mes]"), 0, 1, 'C', 0);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(48, 10, utf8_decode('Fecha'), 1, 0, 'C', 0);
        $pdf->Cell(48, 10, 'Hora de entrada', 1, 0, 'C', 0);
        $pdf->Cell(48, 10, 'Hora de salida', 1, 0, 'C', 0);
        $pdf->Cell(48, 10, utf8_decode('Observación'), 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
/* extarer asistencias */ 
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
            $pdf->Cell(48, 8, $fecha, 1, 0, 'C', 0);
            $pdf->Cell(48, 8, $entrada, 1, 0, 'C', 0);
            $pdf->Cell(48, 8, $salida, 1, 0, 'C', 0);
            $pdf->Cell(48, 8, $observacion, 1, 1, 'C', 0);
        }
    } 
}


$pdf->Output();

?>
