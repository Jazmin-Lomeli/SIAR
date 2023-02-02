<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

$fechaActual = date('Y');
 class PDF extends FPDF
{
// Cabecera de página
function Header()
{
   
     $this->SetFont('Arial','',12);     
    $this->Cell(185,7,'TARJETA DE PAGOS',0,1,'C');  
    $this->Cell(185,8,'CASA DE LA CULTURA',0,0,'C');  
    
    $this->Ln(20);
}
// Pie de página
function Footer()
{
    $this->SetY(-40);// Posición: a 1,5 cm del final
    $this->SetFont('Arial','I',8);
    $this->SetTextColor(130);

    $this->Cell(200,5,utf8_decode('Gobierno Municipal de Tepatitlán 2021-2024'),0,1,'C');
    $this->Cell(200,5,utf8_decode("Hidalgo #45, Colonia Centro. C.P. 47600"), 0,1,'C'); 
    $this->Cell(200,5,utf8_decode("Tepatitlán de Morelos, Jalisco"), 0,0,'C'); 

}
}

$pdf = new PDF();    
$pdf->AliasNbPages();    // siempre s genere el pie de pagina
$pdf->AddPage();  
 


/* Tabla con los meses */
$pdf->SetY(56);   // espacio 
$pdf->Cell(48,27,'ENERO',1,0,'C');
$pdf->Cell(48,27,'FEBRERO',1,0,'C');
$pdf->Cell(48,27,'MARZO',1,0,'C');
$pdf->Cell(48,27,'ABRIL',1,1,'C');
$pdf->Cell(48,27,'MAYO',1,0,'C');
$pdf->Cell(48,27,'JUNIO',1,0,'C');
$pdf->Cell(48,27,'JULIO',1,0,'C');
$pdf->Cell(48,27,'AGOSTO',1,1,'C');
$pdf->Cell(48,27,'SEPTIEMBRE',1,0,'C');
$pdf->Cell(48,27,'OCTUBRE',1,0,'C');
$pdf->Cell(48,27,'NOVIERMBRE',1,0,'C');
$pdf->Cell(48,27,'DICIEMBRE',1,1,'C');


$pdf->SetY(138);   // espacio 
$pdf->SetFont('Arial','B',8);   // font
$pdf->Cell(0, 5,utf8_decode("NOTAS "),0,1,'L'); 
$pdf->SetFont('Arial','',7);   // font 
$pdf->Cell(0, 4,utf8_decode("1.- Los pagos se deben realizar del 01 al 10 de cada mes. "),0,1,'L');  
$pdf->Cell(0, 4,utf8_decode("2.- Con 3 faltas injustificadas automáticamente el alumno se dará de baja."),0,1,'L');  
$pdf->Cell(0, 3,utf8_decode("3.- Al darse de baja, favor de avisar a la oficina de la Jefatura de Actividades Culturales."),0,1,'L');  
$pdf->SetFont('Arial','B',15);   // font 
$pdf->Cell(0, 3,utf8_decode("- - - - - - -  - - - - - - - - - - - - - - - - - - -  - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -"),0,1,'L');  


$pdf->Output('registro.pdf', 'I');

?>
