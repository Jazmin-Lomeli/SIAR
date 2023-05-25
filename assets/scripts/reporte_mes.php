<?php

require '../config/config.php';
require('../fpdf/fpdf.php');

$id = $_GET['id'];
$reporte_mes = $_GET['mes'];

/* si el mes es menor a 10 agregar el 0 */
if ($reporte_mes < 10) {
    $aux = "0" . $reporte_mes;
    $reporte = $aux;
} else {
    $reporte = $reporte_mes;
}
/* establecer rangos de fechas para el reporte */
$fechaActual = date('Y');     // Año actual
 // establecemos la fecha inicial a partir de el año y el mes requerido
$inicio = $fechaActual . "-" . $reporte . "-01";    

// Establecemos fecha final a sumandole un mes a la fecha inicial 
$fin_rep = date("Y-m-d", strtotime($inicio . "+ 1 month"));
 
/* consulta  */
$datos = "SELECT * FROM empleados LEFT JOIN tipo_empleado ON empleados.tipo=tipo_empleado.tipo  LEFT JOIN huella ON huella.id_emp=empleados.id Where empleados.id ='$id' LIMIT 1";
$result_datos = mysqli_query($link, $datos);
$nombre = $tel = $id_huella = $f_ingreso = '';
while ($mostrar = mysqli_fetch_array($result_datos)) {
    $nombre = $mostrar['nombre'] . " " . $mostrar['apellido'] . " " . $mostrar['seg_apellido'];
    $tel = $mostrar['telefono'];
    $id_huella = $mostrar['id_huella'];
    $a_laboral = $mostrar['t_nombre'];
    $f_ingreso = $mostrar['f_registro'];
    $jornada = $mostrar['jornada'];

}

date_default_timezone_set('America/Mexico_City');
$fin = date("Y-m-d");
$meses = "";
function meses($fecha1, $fecha2)
{

    $datetime1 = new DateTime($fecha1);
    $datetime2 = new DateTime($fecha2);

    # obtenemos la diferencia entre las dos fechas
    $interval = $datetime2->diff($datetime1);

    # obtenemos la diferencia en meses
    $intervalMeses = $interval->format("%m");
    # obtenemos la diferencia en años y la multiplicamos por 12 para tener los meses
    $intervalAnos = $interval->format("%y") * 12;
    return $intervalMeses + $intervalAnos;
}

$meses = meses($fin, $f_ingreso);
$anios = 0;
$mes = $anios = 0;

if ($meses > 11) {
    $anios = intval($meses / 12); //Aqui realizamos la operacion de la división
    $mes = $meses % 12; //Y aqui determinamos el modulo

}

if ($anios == 0) {
    if ($meses == 0) {

        $antiguedad = "Menos de un mes.";

    } elseif ($meses == 1) {
        $antiguedad = $meses . " mes.";

    } else {
        $antiguedad = $meses . " meses.";
    }

    //  $pdf->Cell(40, 8, utf8_decode( $imprime), 1, 1, 'C', 0);

} else {
    if ($anios == 1) {
        if ($meses == 1) {
            $antiguedad = $anios . " año " . $mes . " mes.";
        } elseif ($mes == 0) {
            $antiguedad = $anios . " año ";
        } else {
            $antiguedad = $anios . " año " . $mes . " meses.";
        }
    } else {
        if ($meses == 1) {
            $antiguedad = $anios . " años " . $mes . " mes.";
        } elseif ($mes == 0) {
            $antiguedad = $anios . " años ";
        } else {
            $antiguedad = $anios . " años " . $mes . " meses.";
        }
    }
}

function saber_dia($nombredia)
{
    $dias_arr = array('', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo');
    $busca_dia = $dias_arr[date('N', strtotime($nombredia))];
    return $busca_dia;
}




class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', '', 14);
        $this->Cell(185, 7, 'Reporte mensual de asistencia', 0, 1, 'C');
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
 
$meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$pdf->SetFont('Arial', 'B', 13);
$pdf->Cell(130, 5, ("Reporte del mes de $meses[$reporte_mes]"), 0, 1, 'L', 0);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(190, 0, "", 1, 1, 'C'); // linea
//  Datos del empleado 
$pdf->SetFont('Arial', '', 14);
//$pdf->Cell(190, 0, "",1,1,'C');  
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(19, 8, utf8_decode("Nombre: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(110, 8, utf8_decode($nombre), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(21, 8, utf8_decode("Teléfono:"), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(30, 8, utf8_decode($tel), 0, 1, 'L');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(190, 0, "", 1, 1, 'C'); // Linea
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(34, 8, utf8_decode("ID de empleado:"), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(14, 8, utf8_decode($id), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(28, 8, utf8_decode("ID  de huella:"), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(14, 8, utf8_decode($id_huella), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(28, 8, utf8_decode("Área laboral:"), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(90, 8, $a_laboral, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(35, 8, utf8_decode("Jornada laboral: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(20, 8, "$jornada hrs.", 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(26, 8, utf8_decode("Antiguedad: "), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(40, 8, ($antiguedad), 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(39, 8, utf8_decode("Fecha de emisión:"), 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(35, 8, utf8_decode(date('Y-m-d')), 0, 1, 'L');
$pdf->Ln(2);
$dia = saber_dia($inicio);
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(190, 0, "", 1, 1, 'C'); // Linea
 
/* Verificampos en que dia comienza el mes, para tomar la semana desde el lunes*/

if ($dia == "Lunes") { // Le restamos un dia 
    $inicio = date("d-m-Y", strtotime($inicio . "- 0 days"));
}elseif ($dia == "Martes") { // Le restamos un dia 
    $inicio = date("d-m-Y", strtotime($inicio . "- 1 days"));
} elseif ($dia == "Miercoles") { // Le restamos un dia 
    $inicio = date("d-m-Y", strtotime($inicio . "- 2 days"));
} elseif ($dia == "Jueves") { // Le restamos 2 dias 
    $inicio = date("d-m-Y", strtotime($inicio . "- 3 days"));
} elseif ($dia == "Viernes") { // Le restamos 3 dias
    $inicio = date("d-m-Y", strtotime($inicio . "- 4 days"));
} elseif ($dia == "Sabado") { // Le restamos 4 dias
    $inicio = date("d-m-Y", strtotime($inicio . "- 5 days"));
} elseif ($dia == "Domingo") { // Le restamos 5 dias
    $inicio = date("d-m-Y", strtotime($inicio . "- 6 days"));
}

/* Una vez que se le restaron los dias para que sea lunes, se tomara dicha fecha como inicio */
$dia = saber_dia($inicio);
/* Rebertimos la fecha a Y-M-d */
$pdf->Cell(130, 5, ("Reporte del mes de $inicio"), 0, 1, 'L', 0);
$pdf->Cell(130, 5, ("REVEWRTIMOS LA FECHA "), 0, 1, 'L', 0);
$pdf->Cell(130, 5, (substr($inicio, -4, )), 0, 1, 'L', 0);



$inicio = substr($inicio, -4, ) . "-" . substr($inicio, -7, 2) . "-" . substr($inicio, 0, 2);

$dia_2 = saber_dia($fin_rep);

$pdf->Cell(130, 5, ("Reporte del mes de $inicio"), 0, 1, 'L', 0);


//    $dias_arr = array('Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado');
/* Vericampos en que dia comienza el mes, para tomar la semana desde el lunes*/
if ($dia_2 == "Martes") { // Le restamos un dia 
    $fin_rep = date("d-m-Y", strtotime($fin_rep . "- 1 days"));
} elseif ($dia_2 == "Miercoles") { // Le restamos 2 dia 
    $fin_rep = date("d-m-Y", strtotime($fin_rep . "- 2 days"));
} elseif ($dia_2 == "Jueves") { // Le restamos un dia 
    $fin_rep = date("d-m-Y", strtotime($fin_rep . "- 3 days"));
} elseif ($dia_2 == "Viernes") { // Le restamos un dia 
    $fin_rep = date("d-m-Y", strtotime($fin_rep . "- 4 days"));
} elseif ($dia_2 == "Sabado") { // Le restamos un dia 
    $fin_rep = date("d-m-Y", strtotime($fin_rep . "- 5 days"));
} elseif ($dia_2 == "Domingo") { // Le restamos un dia 
    $fin_rep = date("d-m-Y", strtotime($fin_rep . "- 6 days"));
}
/* Reacomodar fecha */ 
$fin_rep = substr($fin_rep, -4, ) . "-" . substr($fin_rep, -7, 2) . "-" . substr($fin_rep, 0, 2);




$pdf->Ln(7); // espaciado
$cont = 1; // contador semanas 
/* consulta correcta */
if ($result = mysqli_query($link, "SELECT * FROM asistencia Where id_emp= '$id' AND fecha BETWEEN '$inicio' AND '$fin_rep'")) {
    /* determinar el número de filas del resultado */
    $asistencias = mysqli_num_rows($result);

    if ($asistencias == 0) { // No arroja registros 

        // FORMATO DE LA TABLA 
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(5, 5, "", 0, 0, 'C');
        $pdf->Cell(60, 5, "Semana No: $cont", 1, 0, 'C');
        $pdf->Cell(40, 5, "", 0, 0, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20, 5, "Fechas:", 0, 0, 'R');
        $pdf->SetFont('Arial', '', 12);

        $sem_aux = date("d-m-Y", strtotime($inicio . "+ 7 days"));
        $sem_aux = substr($sem_aux, -4, ) . "-" . substr($sem_aux, -7, 2) . "-" . substr($sem_aux, 0, 2);
        $pdf->Cell(55, 5, " $inicio al $sem_aux ", 0, 0, 'R');

        $pdf->Cell(5, 5, "", 0, 1, 'R');
        $pdf->Cell(5, 5, "", 0, 0, 'C');
        $pdf->Cell(40, 5, "Fecha", 1, 0, 'C');
        $pdf->Cell(60, 5, "Estatus", 1, 0, 'C');
        $pdf->Cell(50, 5, "Horario ", 1, 0, 'C');
        $pdf->Cell(30, 5, "Horas ", 1, 1, 'C');


        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(5, 5, "", 0, 0, 'C');
        $pdf->Cell(180, 15, 'No hay registros de asistencias para el mes seleccionado', 1, 1, 'C');
        /* cerrar el resulset */
        mysqli_free_result($result);
    } else {

        // FORMATO DE LA TABLA 
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(5, 5, "", 0, 0, 'C');
        $pdf->Cell(60, 5, "Semana No: $cont", 1, 0, 'C');
        $pdf->Cell(40, 5, "", 0, 0, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(20, 5, "Fechas:", 0, 0, 'R');
        $pdf->SetFont('Arial', '', 12);

        $sem_aux = date("d-m-Y", strtotime($inicio . "+ 7 days"));
        $sem_aux = substr($sem_aux, -4, ) . "-" . substr($sem_aux, -7, 2) . "-" . substr($sem_aux, 0, 2);
        $pdf->Cell(55, 5, " $inicio al $sem_aux ", 0, 0, 'R');

        $pdf->Cell(5, 5, "", 0, 1, 'R');
        $pdf->Cell(5, 5, "", 0, 0, 'C');
        $pdf->Cell(40, 5, "Fecha", 1, 0, 'C');
        $pdf->Cell(60, 5, "Estatus", 1, 0, 'C');
        $pdf->Cell(50, 5, "Horario ", 1, 0, 'C');
        $pdf->Cell(30, 5, "Horas ", 1, 1, 'C');


        $pdf->SetFont('Arial', '', 12);
        // contadores
        $retardos = $faltas_just = $mins_tarde = $aux_ret = $aux_minstarde = $jornada_sem = $jornada_dia = 0;
        $sema_jor = $dia_jor = 0;
        $dias_asistidos = 6; /* Aqui puede variar */

        /* CONSULTA para extarer asistencias */
        $query = "SELECT * FROM asistencia Where id_emp= '$id' AND fecha BETWEEN '$inicio' AND '$fin_rep' ORDER BY fecha ASC";
        $result = mysqli_query($link, $query);

        while ($mostrar = mysqli_fetch_array($result)) {
            // Semana a semana 
            if ($cont == 1) {
                $semana_inicio = $inicio;
                $semana_fin = date("d-m-Y", strtotime($inicio . "+ 7 days"));
                $semana_fin = substr($semana_fin, -4, ) . "-" . substr($semana_fin, -7, 2) . "-" . substr($semana_fin, 0, 2);
            }

            $pdf->Cell(5, 30, "", 0, 0, 'C');
            // saber dia de la semana
            $fecha = saber_dia($mostrar['fecha']) . " " . substr($mostrar['fecha'], 5, 8);
            $entrada = $mostrar['entrada'];

            if ($mostrar['entrada'] < "08:16:00") { /* Entrada a tiempo*/
                $observacion = "- - ";

            } else { /* Retardo */
                // $observacion = "Retardo";
                /* Calculo de horas trabajadas por dia */
                $init = strtotime('08:15:00');
                $ret = strtotime($entrada);

                $tiempo_retardo = round(abs($init - $ret) / 60, 2); /* Minuts guarda la cantidad de minutos guardados */
                $observacion = $tiempo_retardo . " mins";
                /* Si el registro es cuando comienza la semana (lunes) */
                if ($mostrar['fecha'] == $semana_fin) {
                    $aux_ret++; /* Variables auxiliares para conatdores */
                    $aux_minstarde = $mins_tarde + $tiempo_retardo;
                    /* De esta manera no afectamos a la semana que acaba de terminar */
                } else { /* el dia no es lunes */
                    $retardos++;
                    $mins_tarde = $mins_tarde + $tiempo_retardo; // Contador para minutos tarde
                }
            }

            if ($mostrar['salida'] == null) { /* Aun no marca salida */
                $salida = "- -";
            } else {
                $salida = $mostrar['salida']; /*  Salida */
            }

            if ($mostrar['entrada'] == "00:00:00") { /* Justificado */
                $faltas_just++;
                $observacion = $mostrar['observacion'];
            }

            /* Calculo de horas trabajadas por dia */
            $to_time = strtotime($entrada);
            $from_time = strtotime($salida);

            $minuts = round(abs($to_time - $from_time) / 60, 2); /* Minuts guarda la cantidad de minutos guardados */

            $Horas = intval($minuts / 60); //Extraemos horas
            $mins = $minuts % 60; // Extraemos los minutos 

            $dias_asistidos--;

            if ($mostrar['fecha'] == $semana_fin) {

                $pdf->Cell(.1, 16, "", 1, 0, 'C'); // Margen 
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(41, 6, "  Faltas justificadas: ", 0, 0, 'L', 0); // Faltas justificadas 
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(10, 6, "$faltas_just", 0, 0, 'L', 0);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(44, 6, "  Faltas No justificadas: ", 0, 0, 'L', 0); // Faltas no justificadas
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(10, 6, "$dias_asistidos", 0, 0, 'L', 0);

                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(36, 6, " Horas laboradas: ", 0, 0, 'L', 0); // Horas que se trabajaron 
                $pdf->SetFont('Arial', '', 11);
                //$jornada_sem esta en minutos 
                $hours = intval($jornada_sem / 60);
                $mins = $jornada_sem % 60;
                $pdf->Cell(39, 6, "$hours hrs $mins mins", 0, 0, 'L', 0);

                $pdf->Cell(.1, 22, "", 1, 0, 'C'); // Margen 

                $pdf->Cell(5, 6, "", 0, 1, '', 0); // Nuevo renglon
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(5, 30, "", 0, 0, 'C');
                $pdf->Cell(23, 6, "  Retardos: ", 0, 0, 'L', 0); // Retardos 
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(28, 6, "$retardos  ", 0, 0, 'L', 0);
                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(23, 6, "  T. retardo: ", 0, 0, 'L', 0); // Tiempo tarde
                $pdf->SetFont('Arial', '', 11);
                
                $pdf->Cell(31, 6, "$mins_tarde mins.", 0, 0, 'L', 0);

                $pdf->SetFont('Arial', 'B', 11);
                $pdf->Cell(36, 6, " Jornada semanal: ", 0, 0, 'L', 0); // Horas que se trabajaron 
                $pdf->SetFont('Arial', '', 11);
                //$jornada_sem esta en minutos 
                if($hours < $jornada ){
                    $pdf->SetTextColor(255,0,0);
                    $pdf->Cell(39, 6, "$jornada hrs", 0, 1, 'L', 0);
                    $pdf->SetTextColor(0,0,0);
                }else{
                    $pdf->SetTextColor(0,0,255);
                    $pdf->Cell(39, 6, "$jornada hrs", 0, 1, 'L', 0);
                    $pdf->SetTextColor(0,0,0);

                }


                $pdf->Cell(180, 4, "", 0, 1, 'L', 0);

                $jornada_sem = $jornada_sem + $minuts; // contador para jornada semanal 
                // YA TERMINO LA SEMANA 
                // Reiniciamos todos los contadores para la siguiente semana 
                $retardos = $faltas_just = $mins_tarde = $jornada_dia = $jornada_sem = 0;
                $dias_asistidos = 5;

                /* nueva semana */
                // Reestablecemos los rangos 
                $inicio = $semana_fin;
                $semana_fin = date("d-m-Y", strtotime($inicio . "+ 7 days"));
                $semana_fin = substr($semana_fin, -4, ) . "-" . substr($semana_fin, -7, 2) . "-" . substr($semana_fin, 0, 2);

                $cont++; // contador de semanas

                /*  Emparejamos auxiliares con varibles contadores */
                $retardos = $aux_ret;
                $mins_tarde = $aux_minstarde;
                /*  Reiniciamos auxiliares */
                $aux_minstarde = 0;
                $aux_ret = 0;

                // Mostramos datos LUNES 
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(5, 5, "", 0, 0, 'C');
                $pdf->Cell(60, 5, "Semana No: $cont", 1, 0, 'C');
                $pdf->Cell(40, 5, "", 0, 0, 'C');
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(20, 5, "Fechas:", 0, 0, 'R');
                $pdf->SetFont('Arial', '', 12);
                $pdf->Cell(55, 5, " $inicio al $semana_fin ", 0, 0, 'R');
                $pdf->Cell(5, 5, "", 0, 1, 'R');
                $pdf->Cell(5, 5, "", 0, 0, 'C');
                $pdf->Cell(40, 8, $fecha, 1, 0, 'C', 0);
                $pdf->Cell(60, 8, $observacion, 1, 0, 'C', 0);
                $pdf->Cell(50, 8, $entrada . "- -" . $salida, 1, 0, 'C', 0);
                $pdf->Cell(30, 8, "$Horas hrs $mins mins", 1, 1, 'C', 0);
                $jornada_sem = $jornada_sem + $minuts;
            } else {
                // Mostramos datos No es LUNES 
                $pdf->Cell(40, 8, $fecha, 1, 0, 'C', 0);
                $pdf->Cell(60, 8, $observacion, 1, 0, 'C', 0);
                $pdf->Cell(50, 8, $entrada . "- -" . $salida, 1, 0, 'C', 0);
                $pdf->Cell(30, 8, "$Horas hrs $mins mins", 1, 1, 'C', 0);

                $jornada_sem = $jornada_sem + $minuts; // contador para jornada semanal 
            }
        }
    }
}


$pdf->Output();

?>