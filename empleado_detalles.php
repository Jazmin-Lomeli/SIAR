<?php
// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  // exit;
}

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


// Variables para el registro de una area de trabajo
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if ($info == "-") { // Asignar area laboral 
    $asig_area = $_POST['area'];
    if ($asig_area == 'select') {
      $err_area = "Por favor, selecciona un área.";
    } else {
      $add_area = mysqli_query($link, "UPDATE empleados SET tipo = '$asig_area' WHERE id = '$id_emp'");

      if ($add_area == TRUE) {
        header('Location: empleado_detalles.php?id=' . $id_emp . '&mensaje=area&info=-');
      } else {
        die(" No se puede Modificar el registro ");
        header('Location: empleado_detalles.phpid=' . $id_emp . '&mensaje=error&info=-');
        exit();
      }
    }
  } else { // Agregar hora de salida 
    if (empty(trim($_POST["h_salida"]))) {
      $h_salida_err = "Por favor, agrega un  dato.";
    } else {
      $h_salida = $_POST["h_salida"];
    }
    if (empty($h_salida_err)) {
      $add_salida = mysqli_query($link, "UPDATE asistencia SET salida = '$h_salida' WHERE id_emp = '$id_emp' AND fecha = '$fecha_act' AND entrada = '$h_entrada'");

      if ($add_salida == TRUE) {
        header('Location: admin_asistencia.php?mensaje=agregado');
      } else {
        die(" No se puede Modificar el registro ");
        header('Location: admin_asistencia.php?mensaje=error');
        exit();
      }
    }
  } // else

} // METHOD POST

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

<body>
  <!-- NAV BAR -->
  <!-- NAV BAR -->
  <header >
      <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow " >
        <div class="container-fluid dernav">
          <a class="navbar-brand"> 
            <img src="./assets/img/logo.png" width="140" height="50" alt="">   <!-- Logo -->
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
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
                <a class="nav-link active" href="admin_recordatorios.php" tabindex="-1" aria-disabled="true">Recordatorios</a>
              </li>
              <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>

                <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item"  href="assets/scripts/cuenta.php"> &nbsp; Cuenta &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                  <i class="bi bi-person-circle"></i> </a></li>

                <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item " href="#">&nbsp; Sistema &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                    <i class="bi bi-gear"></i></a> 
                  </li>

                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item " href="./assets/scripts/logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                    <i class="bi bi-box-arrow-right"></i></a> </li>
 
                </ul>  

              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
  <!-- NAV BAR -->

  <?php
  if ($area == NULL) {
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
                Area Laboral
              </div>
              <div class="card-body">
                <h4 class="card-title pb-2">Aun no se ha agregado un área laboral para</h4>
                <h5 class="pb-4 ">
                  <?php echo $name ?>
                </h5>

                <h7 class="card-text">Por favor selecciona una Área laboral </h7>
                <br>
                <form method="post" id="formulario">
                  <div class="row justify-content-center align-items-center">
                    <div class="col-xl-6 col-lg-6 col-6 form-group">
                      <select name="area" class="form-control <?php echo (!empty($err_area)) ? 'is-invalid' : ''; ?>"
                        value="<?php echo $area; ?>">
                        <option value="select">-- Seleccionar --</option>
                        <?php
                        while ($row = $resultado->fetch_assoc()) {
                          echo '<option value="' . $row['tipo'] . '">' . $row['t_nombre'] . '</option>';
                        }
                        ?>
                      </select>
                      <span class="invalid-feedback">
                        <?php echo $err_area; ?>
                      </span>
                    </div>
                  </div>
                  <br>
                  <div class="col-xl-12 col-lg-12 col-12 form-group Botnones pt-2 pb-2">
                    <input type="submit" class="btn btn-outline-success">
                    <a type="button" class="btn btn-outline-info " data-bs-toggle="modal" data-bs-target="#info">
                      <i class="bi bi-info-circle"></i>
                    </a>
                    <a class="btn btn-outline-danger" href="admin_reg.php"><i class="bi bi-x-circle"></i></a>
                  </div>
                </form>
              </div>
              <div class="card-footer text-muted">
                2 days ago
              </div>
            </div>
          </div>
          <div class="col-sm-3">
          </div>
        </div>
      </div>
      <!-- Modal -->
      <div class="modal fade pt-5" id="info" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog ">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title " id="exampleModalLabel">Área de trabajo </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php $modal = 1; ?>
              <div class="row text-center">
                <div class="col-xl-12 col-lg-12 col-12 ">
                  <h5 class=""> ¿El area de trabajo no está?</h5>
                  <h6> Haz clic en el botón Cancelar, posteriemente haz clic en siguiente el botón </h6>
                  <a class="btn btn-outline-info btn-lg  ml-2">
                    <i class="bi bi-folder-plus">
                    </i></a>
                </div>
              </div>
              <br>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal -->

      <!-- Ventana para agregar salida  -->
      <?php
  } elseif ($info == "salida") { // Opcion para agregar hora de salida
  
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
                  <h4 class="card-title pb-2">Agregar Hora de salida </h4>
                  <h5 class="pb-4 ">
                    <?php echo $name ?>
                  </h5>

                  <h7 class="card-text">Datos de asistancia</h7>
                  <br>
                  <form method="post" id="formulario">
                    <div class="row">
                      <div class="col-xl-2 col-lg-2 col-2 form-group pb-2"> </div>
                      <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                        <label for="id" class="">ID de empleado</label>
                        <input id="id" type="text" name="id" class="form-control" value="<?php echo $id_emp; ?>" readonly>
                      </div>
                      <div class="col-xl-4 col-lg-4 col-4 form-group pb-2">
                        <label for="fecha" class="">Fecha</label>
                        <input id="fecha" type="" name="fecha" class="form-control" value="<?php echo $fecha_a; ?>"
                          readonly>
                      </div>
                      <div class="col-xl-2 col-lg-2 col-2 form-group pb-2"> </div>
                      <div class="col-xl-2 col-lg-2 col-2 form-group pb-2"> </div>

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
                      <div class="col-xl-2 col-lg-2 col-2 form-group pb-2"> </div>

                    </div>
                    <br>
                    <!-- Cambiar botones  -->
                    <div class="col-xl-12 col-lg-12 col-12 form-group Botnones pt-2 pb-2">
                      <input type="submit" class="btn btn-outline-success">
                      <a type="button" class="btn btn-outline-info " data-bs-toggle="modal" data-bs-target="#info">
                        <i class="bi bi-info-circle"></i>
                      </a>
                      <a class="btn btn-outline-danger" href="admin_reg.php"><i class="bi bi-x-circle"></i></a>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-muted">
                  <?php
                  /* Establecer la hora de Mexico por que por defecto manda la del server  */
                  date_default_timezone_set("America/Mexico_City");
                  echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y") . ".   " . date("h:i:sa");
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
  } else {
    ?>
        <!-- NAV BAR -->
        <!-- Detalles del registro  -->
        <div class="px-4 pt-3 bienvenida">
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
        <div class="container mt-2 principal rounded-3 shadow mb-4">
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


          <h3 style="text-align: center; padding-top: 1rem;">Detalles del empleado </h3>


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
                      <a class="btn btn-outline-secondary btn-lg ml-2" data-bs-toggle="modal"
                        data-bs-target="#edit">
                      
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
                        <a class="btn btn-outline-warning btn-lg ml-2" data-bs-toggle="modal"
                        data-bs-target="#huella">
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
              <div class="row">
                <div class="heading-layout1">


                  <div class="container pt-3">
                    <div class="row align-items-start">
                      <div class="container">
                        <div class="row pb-3">
                          <div class="col-3">
                            <span class="lead"> <strong> ID de empleado: </strong>
                              <?php echo $id_emp ?>
                            </span>
                          </div>
                          <div class="col-4">
                            <span class="lead"> <strong>Nombre: </strong>
                              <?php echo $name ?>
                            </span>
                          </div>

                          <div class="col-4">
                            <span class="lead"> <strong>Teléfono: </strong>
                              <?php echo $tel ?>
                            </span>
                          </div>
                          <div class="col-4">
                          </div>
                        </div>
                        <div class="row pb-3">
                          <div class="col-3 pb-3">
                            <span class="lead"><strong> Área laboral: </strong>
                              <?php echo $area ?>
                            </span>
                          </div>
                          <div class="col-4 pb-3">
                            <span class="lead"><strong> Fecha de registro: </strong>
                              <?php echo $f_reg ?>
                            </span>
                          </div>

                          <div class="col-2 pb-3">
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
                          </div>
                          <div class="col-2 pb-3"></div>
                        </div>
                      </div>

                    </div>
                  </div>

                  <h4 class="text-start pt-2 pb-3"> Estatus de asistencia Actual </h4>
                  <div class="row pb-3">
                    <div class="col-3 pt-2">
                      <span class="lead"> <strong> Asistencia:</strong>
                        <?php echo $fecha_a ?>
                      </span>
                    </div>
                    <div class="col-4 pt-2">
                      <span class="lead"> <strong> Hora de entrada:</strong>
                        <?php echo $h_entrada ?>
                      </span>
                    </div>
                    <div class="col-4 pt-2">
                      <span class="lead"> <strong> Hora de salida :</strong>
                        <?php echo $h_salida ?>
                      </span>
                    </div>
                  </div>
                </div>
                <br>
                <h4 class="text-start pt-2 pb-3">Asistencias</h4>
                <div class="container border rounded pb-3"
                  style="border-color:rgb(220, 220, 220); background-color: rgba(228, 215, 215, 0.4);">
                  <div class="row pt-3">
                    <div class="col-sm-8">
                      <div class="card shadow">
                        <div class="card-body">
                          <h5 class="card-title text-center pb-3">Historial 15 días habiles</h5>
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
                              $query = "SELECT entrada, fecha, salida FROM empleados INNER JOIN asistencia ON empleados.id=asistencia.id_emp AND empleados.id='$id_emp' ORDER BY asistencia.fecha DESC limit 15";
                              $result = $link->query($query);
                              while ($mostrar = mysqli_fetch_array($result)) {
                                ?>
                                <tr>
                                  <th>
                                    <?php echo $mostrar['fecha']; ?>
                                  </th>
                                  <?php if ($mostrar['entrada'] < '08:16:00') { ?>
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

                    <div class="col-sm-4">
                      <div class="card shadow">
                        <div class="card-body">
                          <h5 class="card-title text-center">Reporte mensual </h5>
                          <br>
                          <div class="row">
                            <div class="col  pb-4">
                              <abbr title='Mes de Enero'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=1" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  01
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Febrero'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=2" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  02
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Marzo'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=3" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  03
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Abril'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=4" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  04
                                </a>
                              </abbr>
                            </div>
                          </div>
                          <div class="row ">
                            <div class="col pb-4">
                              <abbr title='Mes de Mayo'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=5" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  05
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Junio'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=6" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  06
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Julio'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=7" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  07
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Agosto'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=8" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  08
                                </a>
                              </abbr>
                            </div>
                          </div>
                          <div class="row ">
                            <div class="col  pb-4">
                              <abbr title='Mes de Septiembre'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=9" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  09
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Octubre'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=10" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  10
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Noviembre'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=11" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
                                  onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                                  11
                                </a>
                              </abbr>
                            </div>
                            <div class="col pb-4">
                              <abbr title='Mes de Diciembre'>
                                <!-- Mandar datos por GET -->
                                <a href="assets/scripts/reporte_mes.php?id=<?php echo $id_emp ?>&mes=12" class="btn btn-primary btn-lg ml-2 raise"
                                  target="_blank"
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
                        <h6> Todos los registros con el ID de empleado <strong><?php echo $id_emp ?></strong> serán eliminados.</h6>
                        <img src="assets/img/pregunta.png" class="rounded mx-auto d-block" alt="...">
                      </div>
                      <div class="modal-footer justify-content-center">
                        <a href="assets/scripts/eliminar_emp.php?id=<?php echo $id_emp ?>">
                           <button type="button" class="btn btn-success px-4 mx-3">Confirmar</button>
                        </a>
                        <button type="button" class="btn btn-danger px-4"  data-bs-dismiss="modal">Cancelar</button>
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
                        <a href="assets/scripts/editar_emp.php?id=<?php echo $id_emp ?>">
                           <button type="button" class="btn btn-success px-4 mx-3">Confirmar</button>
                        </a>
                        <button type="button" class="btn btn-danger px-4"  data-bs-dismiss="modal">Cancelar</button>
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
                        <h6>Una vez que das clic al boton de <strong>Registrar</strong> debes dirigirte a el lector de huella.</h6>
                        <img src="assets/img/huella.png" class="rounded mx-auto d-block" alt="..." style="width:15em;height:10rem">
                        <h6>Ya que el sistema estará en modo registrar huella y no cambiará hasta que detecte una huella</h6>

                      </div>
                      <div class="modal-footer justify-content-center">
                        <a href="assets/scripts/add_huella.php?id_add=<?php echo $id_emp?>">
                           <button type="button" class="btn btn-success px-4 mx-3">Registrar</button>
                        </a>
                        <button type="button" class="btn btn-danger px-4"  data-bs-dismiss="modal">Cancelar</button>
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