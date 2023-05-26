<?php
/* Seguridad de Sesiones */
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
}
$user = $_SESSION['username'];

/* Datos que vienen con la URL */
$id_emp = $_GET['id'];
$info = $_GET['info'];

/* Llamar a los archivos */
require_once 'assets/config/config.php';
require('assets/scripts/consultas_detalles.php');

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}


/*  Formularios */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  /* Si es la vista para justificar una falta */
  if ($info == "justificar") {
    $dato_vacio = "00:00:00";
    if (empty(trim($_POST["motivo"]))) {
      $motivo_err = "Por favor ingresa un motivo para la inasistencia";
    } elseif (!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ0-9.,= )(]+$/', trim($_POST["motivo"]))) { // Letras mayusculas y min
      $motivo_err = " Descripción no valida.";
    } else {
      $param_mot = trim($_POST["motivo"]);
      $motivo = $param_mot;
    }
    if (empty($motivo_err)) {
      /* Inserción */
      $sql = "INSERT INTO asistencia (id_emp, fecha, entrada, salida, observacion) VALUES (?,?,?,?,?)";
      if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $id_emp, $fecha_a, $dato_vacio, $dato_vacio, $param_mot);
        $param_mot = $motivo;
        if (mysqli_stmt_execute($stmt)) {
          header("location: admin_asistencia.php?mensaje=justificada");
        } else {
          header("location: admin_asistencia.php?mensaje=error");
        }
        mysqli_stmt_close($stmt);
      }
    }
    /* Vista es agregar salida */
  } else {
    /* Validar datos */
    if (empty(trim($_POST["h_salida"]))) {
      $h_salida_err = "Por favor, agrega un  dato.";
    } else {
      $h_salida = $_POST["h_salida"];
    }
    if (empty($h_salida_err)) {
      /* Actualiza el campo del registro, ya que ya se cuenta con HORA  de entrada */
      $add_salida = mysqli_query($link, "UPDATE asistencia SET salida = '$h_salida' WHERE id_emp = '$id_emp' AND fecha = '$fecha_act' AND entrada = '$h_entrada'");
      if ($add_salida == TRUE) {
        header('Location: admin_asistencia.php?mensaje=agregado');
      } else {
        die(" No se puede Modificar el registro ");
        header('Location: admin_asistencia.php?mensaje=error');
        exit();
      }
    }
  }
} // METHOD POST

date_default_timezone_set('America/Mexico_City');
$fin = date("Y-m-d");
$antiguedad = "";
/* Funcion para calcular la antiguedad en meses */
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


/* Calculamos los meses */
$meses = meses($fin, $f_reg);
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

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Detalles</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="shortcut icon" href="./assets/img/icono.png">

<body>
  <!-- NAV BAR -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
      <div class="container-fluid dernav">
        <a class="navbar-brand">
          <img src="./assets/img/logo_3.png" width="140" height="50" alt=""> <!-- Logo -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse lista_items" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
            <li class="nav-item ">
              <a class="nav-link active" aria-current="page" href="admin_reg.php">Registros</a>
            </li>
            <li class="nav-item px-2">
              <a class="nav-link active" href="admin_asistencia.php">Asistencia</a>
            </li>

            <li class="nav-item">
              <a class="nav-link active" href="admin_recordatorios.php" tabindex="-1"
                aria-disabled="true">Recordatorios</a>
            </li>
            <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
              <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
              </a>

              <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="assets/scripts/cuenta.php"> &nbsp; Cuenta &nbsp; &nbsp; &nbsp; &nbsp;
                    &nbsp; &nbsp; &nbsp;
                    <i class="bi bi-person-circle"></i> </a></li>

                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item " href="assets/scripts/sistema.php">&nbsp; Sistema &nbsp; &nbsp; &nbsp;
                    &nbsp; &nbsp; &nbsp; &nbsp;
                    <i class="bi bi-gear"></i></a>
                </li>

                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item " href="./assets/scripts/logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp;
                    &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                    <i class="bi bi-box-arrow-right"></i></a> </li>

              </ul>

            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <!-- NAV BAR -->

  <!-- Vista para agregar la salida de algun empleado -->
  <?php
  /* Agregar salida de un empleado */
  if ($info == "salida") {
    ?>
    <style>
      body {
        background: rgba(128, 128, 128, 0.5);
        height: 100%;
      }
    </style>
    <br>
    <div class="pt-1 m-0 justify-content-center aling-items-center">
      <div class="col-auto  p-4 text-center">
        <div class="row">
          <div class="col-sm-3">

          </div>
          <div class="col-sm-6">
            <div class="card text-center">
              <div class="card-header">
                Jornada Laboral
              </div>
              <div class="card-body">
                <h4 class="card-title pb-1">Agregar hora de salida </h4>
                <h5 class="pb-2 ">
                  <?php echo $name ?>
                </h5>

                <h7 class="card-text">Datos de asistencia</h7>
                <br>
                <form method="post" id="formulario" class="pt-3">
                  <div class="row d-flex justify-content-center ">
                    <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                      <label for="id" class="">ID de empleado</label>
                      <input id="id" type="text" name="id" class="form-control" value="<?php echo $id_emp; ?>" readonly>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                      <label for="fecha" class="">Fecha</label>
                      <input id="fecha" type="" name="fecha" class="form-control" value="<?php echo $fecha_a; ?>"
                        readonly>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                      <label for="h_entrada" class="">Hora de entrada</label>
                      <input id="h_entrada" type="time" name="h_entrada" class="form-control"
                        value="<?php echo $h_entrada; ?>" readonly>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                      <label for="h_salida" class="">Hora de salida</label>
                      <input id="h_salida" type="time" name="h_salida"
                        class="form-control <?php echo (!empty($h_salida_err)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $h_salida; ?>">
                      <span class="invalid-feedback">
                        <?php echo $h_salida_err; ?>
                      </span>
                    </div>

                    <br>
                    <!--  Botones  -->
                    <div class="col-xl-10 col-lg-10 form-group mx-2 pb-2 pt-3">
                      <a class="btn btn-outline-danger px-4 mx-3" href="admin_reg.php">
                        Cancelar
                      </a>
                      <input type="submit" class="btn btn-outline-success px-4" value="Guardar">
                    </div>
                  </div>
                </form>
              </div>

              <div class="card-footer text-muted">
                <?php
                $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
                $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");

                /* Establecer la hora de Mexico por que por defecto manda la del server  */
                date_default_timezone_set("America/Mexico_City");
                echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y");
                ?>
              </div>
            </div>
          </div>
          <div class="col-sm-3">
          </div>
        </div>
      </div>

      <!-- Ventana para agregar salida  -->

      <?php
  } elseif ($info == "justificar") {
    ?>
      <!-- Vista para agregar la falta justificada de algun empleado -->
      <style>
        body {
          background: rgba(128, 128, 128, 0.5);
          height: 100%;
        }
      </style>
      <br>
      <div class="pt-0 m-0 justify-content-center aling-items-center">
        <div class="col-auto p-3 text-center">
          <div class="row">
            <div class="col-sm-3">
            </div>
            <div class="col-sm-6">
              <div class="card text-center">
                <div class="card-header">
                  Faltas
                </div>
                <div class="card-body">
                  <div class="row p-0 m-0">
                    <div class="col-1">

                    </div>
                    <div class="col-10 pt-4">
                      <h4 class="card-title pb-1">Falta justificada para el empleado </h4>
                    </div>

                    <div class="col-1 d-flex justify-content-end">
                      <abbr title='Motivos'>
                        <a class="btn btn-outline-warning btn-sm rounded-circle" data-bs-toggle="modal"
                          data-bs-target="#exampleModal">
                          <i class="bi bi-info"></i>
                        </a>
                      </abbr>
                    </div>
                  </div>


                  <h5 class="pb-2">
                    <?php echo $name ?>
                  </h5>


                  <br>
                  <form method="post" id="formulario" class="">
                    <div class="row d-flex justify-content-center ">

                      <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                        <label for="id" class="">ID de empleado</label>
                        <input id="id" type="text" name="id" class="form-control" value="<?php echo $id_emp; ?>" readonly>
                      </div>
                      <div class="col-xl-4 col-lg-4 col-4 form-group pb-2 text-center">
                        <label for="fecha" class="">Fecha</label>
                        <input id="fecha" type="" name="fecha" class="form-control" value="<?php echo $fecha_a; ?>"
                          readonly>
                      </div>

                      <div class="col-xl-8 col-lg-8 col-8 form-group pb-2">

                        <abbr title='Escribe un motivo corto'>
                          <label for="descripcion" class="espacio">Motivo de la falta</label>
                        </abbr>
                        <textarea id="motivo" name="motivo" rows="2" col="5"
                          class="form-control <?php echo (!empty($motivo_err)) ? 'is-invalid' : ''; ?>"
                          value="<?php echo $motivo; ?>">
                                                            </textarea>
                        <span class="invalid-feedback">
                          <?php echo $motivo_err; ?>
                        </span>
                        </textarea>

                        <!-- Botones -->
                        <div class="col-xl-10 col-lg-10 form-group mx-2 pb-2 pt-3 align-items-center">

                          <a class="btn btn-outline-danger px-3 mx-3" href="admin_reg.php">
                            <i class="bi bi-x-circle"> Cancelar</i>
                          </a>
                          <input type="submit" class="btn btn-outline-success px-4" value="Ingresar">

                        </div>
                      </div>
                    </div>
                  </form>

                </div>

                <div class="card-footer text-muted">
                  <?php
                  $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
                  $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");

                  /* Establecer la hora de Mexico por que por defecto manda la del server  */
                  date_default_timezone_set("America/Mexico_City");
                  echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y");
                  ?>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- Modal informativo -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Motivos </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body ">
                <div class="text-center">
                  <h6>Por favor agrega un motivo resumido y conciso en campo de motivo</h6>
                  <h6>Por ejemplo</h6>

                </div>

                <div class="row col-12 pt-1" style="font-size: 1em;">
                  <div class="col-6">
                    <ul>
                      <li> Problemas de salud.</li>
                      <li> Cita medica.</li>
                      <li> Emergencia familiar.</li>
                      <li> Muerte de un ser querido.</li>
                    </ul>
                  </div>
                  <div class="col-6">
                    <ul>
                      <li> Problemas con el auto.</li>
                      <li> Cita medica.</li>
                      <li> Emergencia familiar.</li>
                    </ul>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal informativo -->

        <?php
  } else {
    ?>
        <!-- Ver detalles de un empleado -->
        <style>
          .cont {
            background: ghostwhite;
            height: 100%;
            border-radius: 10px;
            padding-bottom: 1em;
            padding-top: 0.5em;
            margin-top: 1em;
          }
        </style>
        <!-- NAV BAR -->
        <!-- Detalles del registro  -->
        <div class=" px-4 pt-3 bienvenida">
          <div class="row">
            <div class="col align-self-start">
              <h4>Bienvenido, <b>
                  <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </b>.</h4>
            </div>
            <div class="col align-self-center"></div>
            <div class="col align-self-end d-flex flex-row-reverse pe-5">
              <?php
              $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
              $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
              ?>
              <p class="d-flex">
                <?php
                /* Establecer la hora de Mexico por que por defecto manda la del server  */
                date_default_timezone_set("America/Mexico_City");
                echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y") . ".   " . date("h:i:sa");
                ?>
              </p>
            </div>
          </div>
        </div>
        <!-- Alertas de confirmacion o  error -->
        <div class=" cont container mt-2 principal rounded-3 shadow mb-4">
          <br>
          <?php
          if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'area') {
            ?>
            <div class="alerta alert alert-success alert-dismissible fade show  text-center " role="alert">
              <strong>¡EXITO!</strong> se agrego correctamete el area de trabajo.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
          }
          ?>
          <?php
          if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'error') {
            ?>
            <div class="alerta alert alert-danger alert-dismissible fade show  text-center" role="alert">
              <strong>¡ERROR!</strong> No se pudo realizar la acción.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
          }
          ?>
          <?php
          if (isset($_GET['info']) and $_GET['info'] == 'edit') {
            ?>
            <div class="alerta alerta_error alert alert-success alert-dismissible fade show  text-center" role="alert">
              <strong>¡Éxito!</strong> Registro editado.
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>
            <?php
          }
          ?>
          <!-- Alertas de confirmacion o  error -->

          <h3 class="pt-1 pb-2 text-center ">Detalles del empleado </h3>

          <!-- Barra de buscar -->
          <div class="pt-2 pb-3">
            <div class="row">
              <div class="row align-items-end">
                <div class="col">
                  <div class="ps-2">
                    <abbr title='Atrás'>
                      <a class="btn btn-outline-dark btn-lg ml-2 " href="admin_reg.php"><i
                          class="bi bi-arrow-left-circle"></i></a>
                    </abbr>
                  </div>
                </div>
                <div class="col">

                </div>
                <div class="col align-self-end d-flex flex-row-reverse">

                  <div class="ps-2">
                    <abbr title='Eliminar registro'>
                      <a type="button" class="btn btn-outline-danger btn-lg ml-2" data-bs-toggle="modal"
                        data-bs-target="#delete">
                        <i class="bi bi-trash3-fill"></i></a>
                    </abbr>
                  </div>
                  <div class="ps-2">
                    <abbr title='Editar registro '>
                      <a class="btn btn-outline-secondary btn-lg ml-2" data-bs-toggle="modal" data-bs-target="#edit">

                        <i class="bi bi-pencil-square"></i>
                      </a>
                    </abbr>
                  </div>
                  <div class="ps-2">
                    <abbr title='Agregar asistencia, hora de entrada'>
                      <a href="admin_asistencia.php" class="btn btn-outline-primary btn-lg ml-2">
                        <i class="bi bi-stopwatch"></i>
                      </a>
                    </abbr>
                  </div>

                  <?php
                  if ($status_huella == 0) {
                    ?>
                    <div class="ps-2">
                      <abbr title='Registrar huella'>
                        <a class="btn btn-outline-warning btn-lg ml-2" data-bs-toggle="modal" data-bs-target="#huella">
                          <i class="bi bi-fingerprint">
                          </i></a>
                      </abbr>
                    </div>
                    <?php
                  }
                  ?>
                </div>
              </div>
            </div>

            <div class="container px-4">
              <div class="row ">
                <div class="heading-layout1">
                  <div class="container pt-3">
                    <div class="row align-items-start">
                      <div class="container">
                        <div class="row pb-2 pt-2">
                          <!-- DATOS -->

                          <div class="col w-auto">
                            <span class="lead"> <strong> ID de empleado: </strong>
                              <?php echo $id_emp ?>
                            </span>
                          </div>
                          <div class="col-5 w-auto pe-2">
                            <span class="lead"> <strong>Nombre: </strong>
                              <?php echo $name ?>
                            </span>
                          </div>
                          <div class="col w-auto">
                            <span class="lead"> <strong>Teléfono: </strong>
                              <?php echo $tel ?>
                            </span>
                          </div>
                          <div class="col w-auto">
                            <?php
                            if ($id_huella == "- -") {
                              ?>
                              <span class="lead"> <strong> ID de huella: </strong> <span class="text-danger">
                                  <?php echo $id_huella ?>
                                </span></span>

                              <?php
                            } else {
                              ?>
                              <span class="lead"> <strong> ID de huella:</strong>
                                <?php echo $id_huella ?>
                              </span>
                              <?php
                            }
                            ?>
                            </span>
                          </div>

                        </div>

                        <div class="row pt-2">
                          <div class="col-4 pb-2 ">
                            <span class="lead"><strong> Departamento: </strong>
                              <?php echo $area ?>
                            </span>
                          </div>
                          <div class="col-4 pb-2 ">
                            <span class="lead"><strong> Fecha de registro: </strong>
                              <?php echo $f_reg ?>
                            </span>
                          </div>

                          <div class="col-4 pb-2 ">
                            <span class="lead"><strong> Antigüedad: </strong>
                              <?php echo $antiguedad ?>
                            </span>

                          </div>
                        </div>
                        <div class="row pb-3 pt-2">
                          <div class="col-6 pb-2 ">
                            <span class="lead"><strong> Jornada semanal: </strong>
                              <?php echo $jornada . " hrs." ?>
                            </span>
                          </div>

                        </div>
                      </div>
                    </div>

                  </div>
                </div>
                <h4 class="text-start pt- pb-3"> Estatus de asistencia actual </h4>
                <div class="row pb-3">
                  <div class="col-3 pt-2">
                    <span class="lead"> <strong> Asistencia:</strong>
                      <?php echo $fecha_a ?>
                    </span>
                  </div>
                  <div class="col-3 pt-2">
                    <span class="lead"> <strong> Hora de entrada:</strong>
                      <?php echo $h_entrada ?>
                    </span>
                  </div>
                  <div class="col-3 pt-2">
                    <span class="lead"> <strong> Hora de salida :</strong>
                      <?php echo $h_salida ?>
                    </span>
                  </div>
                  <div class="col-3 d-flex justify-content-end">

                    <abbr title='Justificar falta'>

                      <a href="empleado_detalles.php?id=<?php echo $id_emp ?>&info=justificar" type="button"
                        class="btn btn-outline-success btn-lg ml-2">
                        Justificar falta
                      </a>
                    </abbr>
                  </div>
                </div>
                <!-- DATOS -->

              </div>
              <br>
              <!-- Asistencias de lo 10 dias habiles  -->

              <h4 class="text-start pt-2 pb-3">Asistencias</h4>
              <div class="container border rounded pb-3" style="border-color:rgb(220, 220, 220);">
                <div class="row pt-3">
                  <div class="col-sm-8">
                    <div class="card shadow">
                      <div class="card-body">
                        <h5 class="card-title text-center pb-3">Historial 10 días habiles</h5>
                        <table class="table table table-bordered table-hover border border-secondary text-center">
                          <thead>
                            <tr>
                              <th>Fecha</th>
                              <th>Hora de entrada</th>
                              <th>Estatus</th>
                              <th>Hora de salida</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                            $query = "SELECT entrada, fecha, salida FROM empleados INNER JOIN asistencia ON empleados.id=asistencia.id_emp AND empleados.id='$id_emp' ORDER BY asistencia.fecha DESC limit 10";
                            $result = $link->query($query);
                            while ($mostrar = mysqli_fetch_array($result)) {
                              ?>
                              <tr>
                                <th>
                                  <?php echo $mostrar['fecha']; ?>
                                </th>
                                <?php if ($mostrar['entrada'] == '00:00:00') { ?>
                                  <th>
                                    <?php echo $mostrar['entrada']; ?>
                                  </th>
                                  <th>
                                    <?php echo "Justificada"; ?>
                                  </th>
                                  <?php
                                } elseif ($mostrar['entrada'] < '08:16:00') { ?>
                                  <th>
                                    <?php echo $mostrar['entrada']; ?>
                                  </th>
                                  <th>
                                    <?php echo "A tiempo"; ?>
                                  </th>
                                  <?php
                                } else {
                                  ?>
                                  <th style="background-color: rgba(255, 0, 0, 0.6)">
                                    <?php echo $mostrar['entrada']; ?>
                                  </th>
                                  <th>
                                    <?php echo "Retardo"; ?>
                                  </th>
                                  <?php
                                }
                                ?>
                                <th>
                                  <?php echo $mostrar['salida']; ?>
                                </th>
                              </tr>
                              <?php
                            }
                            ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
              <!-- Asistencias de lo 10 dias habiles  -->

                  <div class=" col-sm-4">
                    <div class="card shadow">
                      <div class="card-body">
                        <h5 class="card-title text-center">Reporte mensual </h5>
                        <br>
                        <div class="row">
                          <div class="col pb-4">
                            <abbr title='Mes de Enero'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=1"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                01
                              </a>
                            </abbr>
                          </div>
                          <div class="col pb-4 px-1">
                            <abbr title='Mes de Febrero'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=2"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                02
                              </a>
                            </abbr>
                          </div>
                          <div class="col pb-3 pe-2">
                            <abbr title='Mes de Marzo'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=3"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                03
                              </a>
                            </abbr>
                          </div>
                          <div class="col px-0 pb-4">
                            <abbr title='Mes de Abril'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=4"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                04
                              </a>
                            </abbr>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col pb-4">
                            <abbr title='Mes de Mayo'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=5"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                05
                              </a>
                            </abbr>
                          </div>
                          <div class="col pb-4 px-1">
                            <abbr title='Mes de Junio'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=6"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                06
                              </a>
                            </abbr>
                          </div>
                          <div class="col pb-3 pe-2">
                            <abbr title='Mes de Julio'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=7"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                07
                              </a>
                            </abbr>
                          </div>
                          <div class="col px-0 pb-4">
                            <abbr title='Mes de Agosto'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=8"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                08
                              </a>
                            </abbr>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col pb-4">
                            <abbr title='Mes de Septiembre'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=9"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                09
                              </a>
                            </abbr>
                          </div>
                          <div class="col pb-4 px-1">
                            <abbr title='Mes de Octubre'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=10"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                10
                              </a>
                            </abbr>
                          </div>
                          <div class="col pb-3 pe-2">
                            <abbr title='Mes de Noviembre'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=11"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                11
                              </a>
                            </abbr>
                          </div>
                          <div class="col px-0 pb-4">
                            <abbr title='Mes de Diciembre'>
                              <!-- Mandar datos por GET -->
                              <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=12"
                                class="btn btn-primary btn-lg ml-2 raise" target="_blank"
                                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                12
                              </a>
                            </abbr>
                          </div>


                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- MODALES -->

              <!-- Modal ELIMINAR REGISTRO-->
              <div class="modal fade pt-5" id="delete" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header text-center">
                      <h5 class="modal-title " id="staticBackdropLabel">Eliminar registro</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-2">
                      <h5> ¿Estás seguro de eliminar el registro?</h5>
                      <h6> Todos los registros con el ID de empleado <strong>
                          <?php echo $id_emp ?>
                        </strong> serán eliminados.</h6>
                      <img src="assets/img/pregunta.png" class="rounded mx-auto d-block" alt="...">
                    </div>
                    <div class="modal-footer justify-content-center">
                      <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancelar</button>
                      <a href="assets/scripts/eliminar_emp.php?id=<?php echo $id_emp ?>">
                        <button type="button" class="btn btn-success px-4 mx-3">Confirmar</button>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal ELIMINAR REGISTRO-->

              <!-- Modal editar REGISTRO-->
              <div class="modal fade pt-5" id="edit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header text-center">
                      <h5 class="modal-title " id="staticBackdropLabel">Editar registro</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-2">
                      <h5> ¿Estás seguro de editar el registro?</h5>
                      <br>

                      <img src="assets/img/curriculum.png" class="rounded mx-auto d-block" alt="...">
                    </div>
                    <div class="modal-footer justify-content-center">
                      <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancelar</button>
                      <a href="assets/scripts/editar_emp.php?id=<?php echo $id_emp ?>">
                        <button type="button" class="btn btn-success px-4 mx-3">Confirmar</button>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal ELIMINAR REGISTRO-->
              
              <!-- Modal registrar huella -->
              <div class="modal fade pt-5" id="huella" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header text-center">
                      <h5 class="modal-title " id="staticBackdropLabel">Agregar huella</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-2">
                      <h5> Agregar huella dactilar a la base de datos </h5>
                      <h6>Una vez que das clic al boton de <strong>Registrar</strong> debes dirigirte a el lector de
                        huella.</h6>
                      <img src="assets/img/huella.png" class="rounded mx-auto d-block" alt="..."
                        style="width:15em;height:10rem">
                      <h6>Ya que el sistema estará en modo registrar huella y no cambiará hasta que detecte una huella
                      </h6>

                    </div>
                    <div class="modal-footer justify-content-center">

                      <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancelar</button>
                      <a href="assets/scripts/add_huella.php?id_add=<?php echo $id_emp ?>">
                        <button type="button" class="btn btn-success px-4 mx-3">Registrar</button>
                      </a>

                    </div>
                  </div>
                </div>
              </div>
              <!-- Modal registrar huella -->

              <?php
  }
  ?>

            <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
            <script type="text/javascript">
              /* Alerta que desaparece automaticamente */
              $(document).ready(function () {
                setTimeout(function () {
                  $(".alerta").fadeOut(1500);
                }, 2500);
              });
            </script>


            <!-- jQuery library -->
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
            <!-- Latest minified bootstrap js -->
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <!-- Bootstrap core JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
              integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
              crossorigin="anonymous"></script>

</body>

</html>