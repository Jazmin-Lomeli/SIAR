<?php
/* Seguridad de Sesiones */
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
}
/* Llamar a los archivos */
require_once 'assets/config/config.php';

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}
$conexion = $link;

/* Establecer fecha y hora de La Ciudad de Mexico */
date_default_timezone_set("America/Mexico_City");
$fecha_hoy = date("Y-m-d");

/* Consulta */
$query = "SELECT * FROM empleados";
$resultado = $link->query($query);
/* Variables */
$id = $entrada = $salida = "";
$id_err = $entrada_err = $salida_err = "";

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

  // Si no hay errores proseguimos a hacer la insercion en la tabla de aisistencia
  if (empty($id_err) && empty($entrada_err)) {
    /* Consulta */
    $sql = "INSERT INTO asistencia (id_emp, entrada, fecha) VALUES (?,?,?)";
    if ($stmt = mysqli_prepare($link, $sql)) {
      /* Agregamos los parametros */
      mysqli_stmt_bind_param($stmt, "sss", $param_id, $param_entrada, $fecha_hoy);
      if (mysqli_stmt_execute($stmt)) {
        // Redirect to login page
        header("location: admin_asistencia.php?mensaje=agregado");
      } else {
        header("location: admin_asistencia.php?mensaje=error");
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
  <title>Recordatorios</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="shortcut icon" href="./assets/img/icono.png">
</head>

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

  <!-- Modal  EDICIÓN DEL REGISTRO -->
  <div class="modal fade pt-5" id="recordatorio" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
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
          <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->

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
        $dia = array("domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado");
        ?>
        <p class="d-flex">
          <?php
          /* Establecer la hora de Mexico por que por defecto manda la del server  */
          date_default_timezone_set("America/Mexico_City");
          $hora_actual = strtotime("-1 hour");    /* Le restamos una hora ya se vio afectado por el cambio de horario */ 
          echo $dia[date('w', $hora_actual)] . " " . date("d", $hora_actual) . " de " . $mes[date("m", $hora_actual) - 1] . " de " . date("Y", $hora_actual) . ".   " . date("h:i:sa", $hora_actual); ?>
        </p>
      </div>
    </div>
  </div>


  <div class=" cont container mt-2 rounded-3 shadow mb-4">
    <!-- Alertas de confirmacion o  error -->
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
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'deleted') {
      ?>
      <br>
      <div class=" alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>¡EXITO!</strong> El recordatorio de borro correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'add') {
      ?>
      <br>
      <div class=" alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>¡Exito!</strong> Recordatorio agregado correctamente.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

      </div>
      <?php
    }
    ?>
    <!-- Alertas de confirmacion o  error -->

    <h2 style="text-align: center; padding-top: 1rem; padding-bottom: 0.5rem;">Recordatorios</h2>

    <div class="pt-2 pb-3">
      <div class="row">
        <div class="col-md-auto align-self-start pe-2">
          <abbr title='Agregar un recordatorio'>
            <a href="assets/scripts/add_recordatorio.php" type="button" class="btn btn-outline-primary btn-lg ml-2">
              &nbsp;
              <i class="bi bi-chat-square-text">
              </i>
              &nbsp;
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
            <th>Área</th>
            <th>Nombre</th>
            <th>Descripcón</th>
            <th>Carácter</th>
            <th>Fechas</th>
            <th>Opciones</th>
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
      let url = "./assets/scripts/ajax_recordatorios.php"
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
  <!-- Alertas de edicion, borrado y error
  -->
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