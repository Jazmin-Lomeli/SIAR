<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

require_once 'assets/config/config.php';

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
  exit;
}

// Formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $area = trim($_POST["area"]);

  // Validación de entrada
  if (empty($area) || !ctype_alpha(str_replace(' ', '', $area))) {
    header("location: admin_reg.php");
    exit;
  }

  // Inserción de un nuevo departamento
  $sql = "INSERT INTO tipo_empleado (t_nombre) VALUES (?)";
  if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $area);
    if (mysqli_stmt_execute($stmt)) {
      header("location: admin_reg.php?mensaje=area");
      exit;
    } else {
      header("location: admin_reg.php?mensaje=error");
      exit;
    }
    mysqli_stmt_close($stmt);
  }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Inicio</title>
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
        <!-- Barra de navegación comprimida -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse lista_items" style="color: white;" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 Texto">
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
          $hora_actual = strtotime("-1 hour");
          echo $dia[date('w', $hora_actual)] . " " . date("d", $hora_actual) . " de " . $mes[date("m", $hora_actual) - 1] . " de " . date("Y", $hora_actual) . ".   " . date("h:i:sa", $hora_actual); ?>
        </p>
      </div>
    </div>
  </div>

   <!-- Modal -->
   <div class="modal fade pt-5" id="exampleModal" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title " id="exampleModalLabel">Agregar nuevo departamento </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <form method="post" id="formulario">
            <div class="row">
              <div class="col-xl-12 col-lg-6 col-12 form-group">
                <label for="area" class="">Nombre del nuevo departamento</label>
                <input id="area" type="text" name="area" class="form-control">
              </div>
            </div>

            <br>

            <div class="align-items-center">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->

  <!-- Alertas de confirmacion o  error -->
  <div class="cont container mt-2 rounded-3 shadow mb-4 ">

    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'error') {
      ?>
      <br>
      <div class=" alerta alert alert-danger alert-dismissible fade show  text-center" role="alert">
        <strong>ERROR!</strong> Vuelve a intentar.
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
        <strong>EXITO!</strong> Los datos fueron actualizados
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'eliminado') {
      ?>
      <br>
      <div class=" alerta alert alert-danger alert-dismissible fade show text-center" role="alert">
        <strong>¡Eliminado!</strong> El registro fue eliminado
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'area') {
      ?>
      <div class=" alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>CORRECTO!</strong> Se agrego el area correctamnete
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'add') {
      ?>
      <div class="alerta alert alert-success alert-dismissible fade show text-center" role="alert">
        <strong>¡CORRECTO!</strong> El registro fue agregado
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php
    }
    ?>
    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'error_area') {
      ?>
      <div class=" alerta alert alert-danger alert-dismissible fade show  text-center" role="alert">
        <strong>¡ERROR!</strong> No se agrego ningura área.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

      </div>
      <?php
    }
   



    ?>
    <!-- Alertas de confirmacion o  error -->
    <h2 class="pb-3" style="text-align: center; padding-top: 1rem;">Empleados</h2>
    <div class="container w-auto shadow pt-0 -pb-0">
      <nav class="navbar navbar-expand-lg navbar-light rounded-4">
        <div class="container dernav">
          <!-- Botones con opciones -->
          <div class="col-8 btn-group ">
            <abbr title='Agregar Empleado'>
              <a type="button" class="btn btn-outline-secondary btn-lg  ml-2" href="assets/scripts/admin_register.php">
                <i class="bi bi-person-plus-fill"></i></a>
            </abbr>
            <abbr title='Agregar nuevo departamento'>
              <a type="button" class="btn btn-outline-info btn-lg ml-2 mx-2" data-bs-toggle="modal"
                data-bs-target="#exampleModal">
                <i class="bi bi-folder-plus">
                </i></a>
            </abbr>
          </div>
          <div class="container d-flex flex-row-reverse">
            <!-- Abrir ventana para el PDF -->
            <abbr title='Imprimir registo de asistencia'>
              <a href="assets/scripts/reporte_empleados.php" class="navbar-brand" target="_blank"
                onclick="window.open(this.href,this.target,'width=1000,height=700,top=120,left=100,toolbar=no,location=no,status=no,menubar=no');return false;">
                <img src="./assets/img/impresora.png" width="45" height="45" alt=""> <!-- Logo -->
              </a>
            </abbr>
          </div>
        </div>
      </nav>
    </div>

    <h5 class="pt-3">Reporte de los empleados registrados en el sistema</h5>
    <!-- Barra de buscar -->
    <div class="pt-2 pb-3">
      <div class="row">
        <div class="col align-self-center">
        </div>
        <div class="col align-self-end d-flex flex-row-reverse ">
          <form class="d-flex col-md-8" role="search" action="" method="post">
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
            <th>Teléfono</th>
            <th>ID de huella</th>
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
      let url = "./assets/scripts/ajax_reg.php"
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