<?php

require_once '../config/config.php';
// Initialize the session
session_start();

// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
}
// Conexion exitosa
$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}
// consultar el rol del usuario de la sesion
$query = "SELECT Rol, huella FROM users WHERE id= " . $_SESSION["id"] . " LIMIT 1";
$resultado = mysqli_query($link, $query);

foreach ($resultado as $row) {
  $rol = $row['Rol'];
  $hola2 = $row['huella'];
}
$_SESSION["rol"] = $rol;
$status_huella = $hola2;
// Si NO es admin redirigir al login 
if ($rol != 'admin') {
  exit;
  header('Location: login.php');
}

$id_user = $_SESSION['id'];
/* Inicializar variables necesarias para el registro */
$id = $hora = $fecha = '';
$id_err = $hora_err = $fecha_err = '';
/* Establecer la hora */
date_default_timezone_set("America/Mexico_City");
$fecha = date("Y-m-d");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  /* Validar ID del usuario  */
  if (empty(trim($_POST["id"]))) {
    $id_err = "Por favor ingresa un ID de empleado.";
  } elseif (!preg_match('/^[0-9]+$/', trim($_POST["id"]))) { // Letras mayusculas y min
    $id_err = " El ID solo puede contener números.";
  } else {
    $param_id = trim($_POST["id"]);
    $id = $param_id;
  }

  /* Validar HORA del registro  */
  if (empty(trim($_POST["hora"]))) {
    $hora_err = "Por favor ingresa una hora de entrada.";
  } else {
    $param_hora = trim($_POST["hora"]);
    $hora = $param_hora;
  }

  // Si no hay errores proseguimos a hacer la insercion en la tabla de aisistencia
  if (empty($id_err) && empty($hora_err)) {
    /* Consulta */
    $sql = "INSERT INTO asistencia (id_emp, entrada, fecha) VALUES (?,?,?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
      /* Agregamos los parametros */
      mysqli_stmt_bind_param($stmt, "sss", $param_id, $param_hora, $fecha);
      // Esteblecemos los parametros en los inpus, si hay un error no se borre lo que esta correcto  
      $param_id = $id;
      $param_hora = $hora;
      // si la insercion se llevo a cabo de manera correcta 
      if (mysqli_stmt_execute($stmt)) {
        // Redirect to login page
        header("location: ../../admin_asistencia.php?mensaje=agregado");
      } else {
        header("location: ../../admin_asistencia.php?mensaje=error");
      }
      // Close statement
      mysqli_stmt_close($stmt);
    }
  }
  // Close connection
  mysqli_close($link);
}

?>
<!-- PARTE VISUAL DE LA PAGINA WEB -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Asistencia</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/root.css">

</head>

<body>
  <!-- NAV BAR -->
  <header>
    <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
      <div class="container-fluid dernav">
        <a class="navbar-brand">
          <img src="../img/logo.png" width="130" height="50" alt=""> <!-- Logo -->
        </a>

        <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button"
            data-bs-toggle="dropdown" aria-expanded="false">
            <?php echo htmlspecialchars($_SESSION["username"]); ?>
          </a>
          <!-- Boton desplegable -->
          <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
            <li>
              <a class="dropdown-item" href="#"> &nbsp; Cuenta &nbsp; &nbsp;<i class="bi bi-person-circle"></i></a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item " href="logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp; &nbsp; <i
                  class="bi bi-box-arrow-right"></i></a>
            </li>
            <!-- Boton desplegable
                <li>
                  <hr class="dropdown-divider">
                </li>
                 
                <li><a class="dropdown-item" href="#">Something</a></li>
                 -->
          </ul>
        </li>
        </ul>
      </div>
      </div>
    </nav>
  </header>

  <div class="px-4 pt-3 pb-5 bienvenida">
    <div class="row">
      <div class="col align-self-start">
        <h4>Registro manual de asistencia </h4>
      </div>
      <!-- Mostrar la hora actual -->
      <div class="col align-self-center"></div>
      <div class="col align-self-end d-flex flex-row-reverse pe-5">
        <?php
        /* Arreglo para mostrar la hora y fecha de manera amigable */
        $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
        $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
        ?>
        <p class="d-flex">
          <?php
          /* Establecer la hora de Mexico por que por defecto manda la del server  */
          date_default_timezone_set("America/Mexico_City");
          echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y") . ".   " . date("h:i:sa"); ?>
        </p>
      </div>
    </div>
  </div>

  <div class="container mt-2 rounded-3 shadow">
    <div class="row text-center justify-content-center my-4">
      <div class="col-md-8 wrapper p-3">
        <h2 class="card-title pt-2">Asistencia</h2>
        <!-- Formulario -->
        <form method="post" id="formulario">
          <div class="container pt-4">
            <div class="row">
              <div class="col">
                <label for="id">ID Empleado</label>
                <input type="text" name="id" class="form-control <?php echo (!empty($id_err)) ? 'is-invalid' : ''; ?>"
                  value="<?php echo $id; ?>">
                <span class="invalid-feedback">
                  <?php echo $id_err; ?>
                </span>
              </div>
              <div class="col">
                <label for="fecha">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $fecha; ?>" readonly>
              </div>
              <div class="col">
                <label>Hora de entrada </label>
                <input type="time" name="hora"
                  class="form-control <?php echo (!empty($hora_err)) ? 'is-invalid' : ''; ?>"
                  value="<?php echo $hora; ?>">
                <span class="invalid-feedback">
                  <?php echo $hora_err; ?>
                </span>
              </div>
            </div>
          </div>
          <div class="form-group col-md-12 p-4 ">
            <input type="submit" class="btn btn-outline-success ps-5 px-5" value="Registrar">
            &nbsp;
            <a class="btn btn-outline-danger ps-4 px-4" href="../../admin_asistencia.php"><i class="bi bi-x-circle"></i>
              &nbsp; Cancelar</a>
          </div>
        </form>
        <!-- Formulario -->

      </div>
    </div>
  </div>

  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
    crossorigin="anonymous"></script>
</body>

</html>