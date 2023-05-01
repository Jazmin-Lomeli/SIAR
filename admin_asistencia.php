<?php
/* Seguridad de Sesiones */
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
}

require_once 'assets/config/config.php';

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}

date_default_timezone_set("America/Mexico_City");
$fecha_hoy = date("Y-m-d");
/* Consulta  para asistencias*/
$query = "SELECT * FROM empleados";
$resultado = $link->query($query);
$conexion = $link;

$id = $entrada = $salida = $fecha = "";
$id_err = $entrada_err = $salida_err = $fecha_err = "";

/* Formulario */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  /* Validar ID del usuario  */
  if ($_POST["id"] == "select") {
    $id_err = "ID vacio.";
  } else {
    $param_id = trim($_POST["id"]);
    $id = $param_id;
  }
  /* Validar HORA del registro  */
  if (empty(trim($_POST["hora"]))) {
    $entrada_err = "Hora de entrada vacia.";
  } else {
    $param_entrada = trim($_POST["hora"]);
    $entrada = $param_entrada;
  }
  /* Validar Fecha del registro  */
  if (empty(trim($_POST["fecha"])) || $_POST["fecha"] > date('Y-m-d')) {
    $fecha_err = "Error";
  } else {
    $fecha = $_POST["fecha"];
  }

  // Si no hay errores proseguimos a hacer la insercion en la tabla de aisistencia
  if (empty($id_err) && empty($entrada_err) && empty($fecha_err)) {
    /* Inserción  */
    $sql = "INSERT INTO asistencia (id_emp, entrada, fecha) VALUES (?,?,?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "sss", $id, $entrada, $fecha);

      if (mysqli_stmt_execute($stmt)) {
        header("location: admin_asistencia.php?mensaje=agregado"); // Correcto 
      } else {
        header("location: admin_asistencia.php?mensaje=error"); // Algo salio mal 
      }

      mysqli_stmt_close($stmt);
    }
  } else {
    header("location: admin_asistencia.php?mensaje=error");
  }
  mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Asistencia</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="shortcut icon" href="./assets/img/icono.png">

</head>
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

<body>
  <!-- NAV BAR -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
      <div class="container-fluid dernav">
        <a class="navbar-brand">
          <img src="./assets/img/logo_3.png" width="140" height="50" alt=""> <!-- Logo -->
        </a>
        <!-- Barra de navegación comprimida -->
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
              <!-- Submenu de barra de navegación -->
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
  <!-- Modal AGREGAR ASISTENCIA -->
  <div class="modal fade pt-5" id="exampleModal" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title " id="exampleModalLabel">Agregar entrada </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form method="post" id="formulario">
            <div class="row">
              <div class="col-xl-6 col-lg-6 col-6 form-group text-center">
                <label for="id">ID de empleado</label>
                <!-- Desplega lista de los empleados con su ID -->
                <select name="id" class="form-control <?php echo (!empty($area_err)) ? 'is-invalid' : ''; ?>"
                  value="<?php echo $area; ?>">
                  <option value="select">-- Seleccionar --</option>
                  <?php
                  while ($row = $resultado->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . $row['id'] . " - " . $row['nombre'] . '</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="col-xl-6 col-lg-6 col-6 form-group text-center">
                <label for="fecha">Fecha</label>
                <input id="fecha" type="date" name="fecha" class="form-control" value="<?php echo $fecha_hoy; ?>">
              </div>
              <div class="col-xl-4 col-lg-4 col-4 form-group"></div>
              <div class="col-xl-4 col-lg-4 col-4 form-group text-center">
                <label for="hora">Hora</label>
                <input id="hora" type="time" name="hora" class="form-control">
              </div>
              <div class="col-xl-4 col-lg-4 col-4 form-group"></div>
            </div>
            <br>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->

  <!-- Bienvenida  -->
  <div class="px-4 pt-3  bienvenida">
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
          $hora_actual = strtotime("-1 hour");
          echo $dia[date('w', $hora_actual)] . " " . date("d", $hora_actual) . " de " . $mes[date("m", $hora_actual) - 1] . " de " . date("Y", $hora_actual) . ".   " . date("h:i:sa", $hora_actual); ?>
        </p>
      </div>
    </div>
  </div>
  <!-- Bienvenida  -->


  <div class="cont container mt-2 rounded-3 shadow mb-4">
    <!-- Alertas de confirmaciòn o  error -->
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'error') {
      ?>
      <br>
      <div class=" alerta alert alert-danger alert-dismissible fade show  text-center" role="alert">
        <strong>¡ERROR!</strong> Vuelve a intentar.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'editado') {
      ?>
      <br>
      <div class=" alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>¡EXITO!</strong> La hora de SALIDA fue registrada.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'agregado') {
      ?>
      <br>
      <div class=" alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>¡Exito!</strong> La hora de ENTRADA fue registrada.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'justificada') {
      ?>
      <br>
      <div class=" alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>¡Exito!</strong> La falta justificada fue agregada con exito
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

      </div>
      <?php
    }
    ?>
    <!-- Alertas de confirmacion o  error -->

    <!-- Contenido -->
    <h2 style="text-align: center; padding-top: 1rem; padding-bottom: 0.5rem;">Asistencia</h2>
    <div class="container w-auto shadow pt-0 pb-0">
      <!-- Navbar 2 -->
      <nav class="navbar navbar-expand-lg navbar-light pl-4 rounded-4">
        <div class="container-fluid dernav">
          <a class="navbar-brand">
            <img src="./assets/img/asistencia_icono.png" width="45" height="45" alt=""> <!-- Logo -->
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse lista_items" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 " style="color:black;">
              <li class="nav-item pe-1">
                <a class="nav-link active" style="font-size: 1.2em;" aria-current="page"
                  href="admin_asistencia.php">Actual</a>
              </li>
              <li class="nav-item pe-1">
                <a class="nav-link active" style="font-size: 1.2em;" href="admin_asistencia_pasadas.php">Anteriores</a>
              </li>

            </ul>
            <!-- Abrir ventana para el PDF -->
            <abbr title='Imprimir registo de asistencia'>
              <a href="assets/scripts/reporte_actual.php" class="navbar-brand" target="_blank"
                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                <img src="./assets/img/impresora.png" width="45" height="45" alt=""> <!-- Logo -->
              </a>
            </abbr>
          </div>
        </div>
      </nav>
      <!-- Navbar 2 -->
    </div>

  
    <h5 class=" pt-4 pb-1 mb-0 pt-4">Reporte de asistencia del dia
      <?php
      echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y") . "."; ?>
      </h5>

    <div class="pt-2 pb-3">
      <div class="row">
        <div class="col-md-auto align-self-start pe-1">
          <abbr title='Agregar asistencia, hora de entrada'>
            <a type="button" class="btn btn-outline-primary btn-lg ml-2" data-bs-toggle="modal"
              data-bs-target="#exampleModal">
              <i class="bi bi-person-plus-fill">
              </i>
            </a>
          </abbr>
        </div>
        <div class="col align-self-center">
        </div>
        <!-- Barra de buscar -->
        <div class="col align-self-end d-flex flex-row-reverse ">
          <form class="d-flex col-md-7" role="search" action="" method="post">
            <input class="form-control me-2 light-table-filter" type="search" placeholder="Buscar" aria-label="Buscar"
              name="campo" id="campo">
          </form>
        </div>
      </div>
    </div>

    <div class="table-responsive text-center">
      <!-- Tabla con los datos -->
      <table class="table table table-bordered table-hover border border-secondary">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Fecha</th>
            <th>H. Entrada</th>
            <th>H. Salida</th>
            <th>Salida</th>
          </tr>
        </thead>
        <tbody id="content"> <!-- Contenido con AJAX -->

        </tbody>
      </table>
    </div>
  </div>

  <!--Funcion de JS para buscar en tiempo real  -->
  <script>
    /* Llamando a la función getData() */
    getData()
    /* Escuchar un evento keyup en el campo de entrada y luego llamar a la función getData. */
    document.getElementById("campo").addEventListener("keyup", getData)
    /* Peticion AJAX */
    function getData() {
      let input = document.getElementById("campo").value
      let content = document.getElementById("content")
      let url = "./assets/scripts/ajax_asistencia.php"
      let formaData = new FormData()
      formaData.append('campo', input)
      fetch(url, {
        method: "POST",
        body: formaData
      }).then(response => response.json())
        .then(data => {
          content.innerHTML = data
        }).catch(err => console.log(err))
    }
  </script>

  <!--Funcion de JS para borrar la alerta automaticamente  -->
  <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
  <!-- Alertas de edicion, borrado y error -->
  <script type="text/javascript">
    $(document).ready(function () {
      setTimeout(function () {
        $(".alerta").fadeOut(1500);
      }, 2500);
    });
  </script>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
    crossorigin="anonymous"></script>
</body>

</html>