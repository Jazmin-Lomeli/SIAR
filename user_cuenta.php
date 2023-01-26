<?php
// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  exit;
}

require_once 'assets/config/config.php';
// require_once 'assets/config/functions.php';

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}
/* Extraer el tipo de usuario y el status de la huella */
$query = "SELECT Rol, huella, username FROM users WHERE id= " . $_SESSION["id"] . " LIMIT 1";
$resultado = mysqli_query($link, $query);

foreach ($resultado as $row) {
  $rol = $row['Rol'];
  $status_huella = $row['huella'];
  $usermame = $row['username'];
}
$_SESSION["rol"] = $rol;

if ($rol != 'user') { /* Si no es user redireccionar */
  header('Location: login.php');
} elseif ($status_huella == 0) { /* Asegurar que ya tenga registrada por lo menos una huella */
  header('Location: reg_huella.php');
}

$id_user = $_SESSION['id'];
/* Que ya haya   */
$conexion = $link;
if (!$conexion) { /* si falla la conexion */
  header('Location: login.php');
  exit;
}
?>
<!-- Parte visual de la pagina web -->
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



  <style>

  </style>
</head>

<body>

  <header>
    <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
      <div class="container-fluid dernav">
        <a class="navbar-brand">
          <img src="./assets/img/logo.png" width="120" height="40" alt=""> <!-- Logo -->
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse lista_items" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
            <li class="nav-item ">
              <a class="nav-link active" aria-current="page" href="#">Tag</a>
            </li>
            <li class="nav-item px-2">
              <a class="nav-link active" href="#">Recordatorios</a>
            </li>
            <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
              <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
              </a>
              <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item " href="./assets/scripts/logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp;
                    &nbsp;<i class="bi bi-box-arrow-right"></i></a> </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#"> &nbsp; Cuenta &nbsp; &nbsp;<i class="bi bi-person-circle"></i>
                  </a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#">Something</a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <div class="container mt-5">
    <div class="row col-12">
      <div class="card text-center">
        <div class="card-header">
          Información de Mi cuenta
        </div>
        <div class="row pt-2">

          <div class="col-sm-auto">
            <div class="card justify-content align-items-center">
              <div class="card-body">
                <div style="height: 140px; ">
                  <div style="width: 140px; ">
                    <img src="assets/img/user.png" alt="..." class="img-fluid img-thumbnail ">
                  </div>
                </div>
                <p class="pt-2 pb-0"> <b>Username: </b> <br>
                  <?php echo $usermame ?>
                </p>
              </div>
            </div>
          </div>


          <div class="col-sm-auto">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Go somewhere</a>
              </div>
            </div>
          </div>
        </div>




        <div class="card-footer text-muted mt-2">
          2 days ago
        </div>
      </div>
    </div>
  </div>










  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
    crossorigin="anonymous"></script>
</body>

</html>