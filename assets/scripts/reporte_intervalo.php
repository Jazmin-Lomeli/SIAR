<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

/* Datos de la URL */
$inicio_fe = $_GET['inicio'];
$fin_fe = $_GET['final'];

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

        $fechaActual = $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y");

        $this->SetFont('Arial', 'I', 14);
        $this->Cell(185, 10, 'Reporte de asistencias ', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(185, 8, "Fecha de emision: $fechaActual", 0, 1, 'L');
    }

    function Footer()
    {
        $this->SetY(-21); // Posición: a 1,5 cm del final
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
$pdf->Ln(4); // espaciado

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(42, 10, 'Fechas del reporte: ', 0, 0, 'C');
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(60, 10, " $inicio_fe  a  $fin_fe ", 0, 1, 'L');


/* consulta correcta */
if ($result = mysqli_query($link, "SELECT * FROM asistencia Where fecha BETWEEN '$inicio_fe' AND '$fin_fe'")) {
    /* determinar el número de filas del resultado */
    $asistencias = mysqli_num_rows($result);

    if ($asistencias == 0) { // No arroja registros 

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(10, 10, utf8_decode('ID'), 1, 0, 'C', 0);
        $pdf->Cell(70, 10, utf8_decode('Nombre'), 1, 0, 'C', 0);
        $pdf->Cell(30, 10, utf8_decode('Área laboral'), 1, 0, 'C', 0);
        $pdf->Cell(25, 10, 'Fecha', 1, 0, 'C', 0);
        $pdf->Cell(25, 10, 'H. entrada', 1, 0, 'C', 0);
        $pdf->Cell(25, 10, 'H. salida', 1, 0, 'C', 0);
        $pdf->Cell(25, 10, utf8_decode('Observación'), 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(192, 15, 'Por el momento NO hay registros de asistencias', 1, 1, 'C');
        /* cerrar el resulset */
        mysqli_free_result($result);
    } else { // Si hay registros

        $pdf->SetFont('Arial', 'B', 12);

        $pdf->Cell(10, 10, utf8_decode('ID'), 1, 0, 'C', 0);
        $pdf->Cell(70, 10, utf8_decode('Nombre'), 1, 0, 'C', 0);
        $pdf->Cell(34, 10, utf8_decode('Departamento'), 1, 0, 'C', 0);
        $pdf->Cell(25, 10, 'Fecha', 1, 0, 'C', 0);
        $pdf->Cell(25, 10, 'H. entrada', 1, 0, 'C', 0);
        $pdf->Cell(25, 10, 'H. salida', 1, 1, 'C', 0);

        $pdf->SetFont('Arial', '', 11);
        /* extarer asistencias */
        $fecha_aux = " ";
        $query = "SELECT * FROM empleados INNER JOIN tipo_empleado ON tipo_empleado.tipo = empleados.tipo INNER JOIN asistencia ON empleados.id = asistencia.id_emp WHERE fecha BETWEEN '$inicio_fe' AND '$fin_fe'  ORDER BY fecha ASC";
        $result = mysqli_query($link, $query);

        while ($mostrar = mysqli_fetch_array($result)) {
            $id = $mostrar['id'];

            $fecha = $mostrar['fecha'];
            $observacion = $mostrar['observacion'];
            $entrada = $mostrar['entrada'];
            
            if ($mostrar['entrada'] < "08:16:00") {
                $observacion = "- - ";
                $pdf->SetFont('Arial', '', 11);

            } else {
                $observacion = "Retardo";
            }
            if ($mostrar['salida'] == null) {
                $salida = "- -";
            } else {
                $salida = $mostrar['salida'];
            }
            if ($fecha_aux != $fecha) {
                $pdf->SetFont('Arial', 'I', 11);
                $pdf->Cell(189, 8, ($fecha), 1, 1, 'C', 0);
                $pdf->SetFont('Arial', '', 11);

            }
            $fecha_aux = $fecha;
/*
            if ($observacion != NULL) {*/
            $pdf->SetFont('Arial', '', 11);

                $pdf->Cell(10, 8, ($mostrar['id']), 1, 0, 'C', 0);
                $pdf->Cell(70, 8, utf8_decode($mostrar['nombre'] . " " . $mostrar['apellido'] . " " . $mostrar['seg_apellido']), 1, 0, 'L', 0);
                $pdf->Cell(34, 8, utf8_decode($mostrar['t_nombre']), 1, 0, 'C', 0);
                $pdf->Cell(25, 8, ($fecha), 1, 0, 'C', 0);

                if($entrada == '00:00:00' && $salida == '00:00:00'){
                    $pdf->Cell(50, 8, ($mostrar['observacion']), 1, 1, 'C', 0);
                  
                }else{
                    $pdf->Cell(25, 8, ($entrada), 1, 0, 'C', 0);
                    $pdf->Cell(25, 8, $salida, 1, 1, 'C', 0);
                }

        }
    }
}


$pdf->Output('', 'I');

?>