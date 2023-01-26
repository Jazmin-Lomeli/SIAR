<?php
 // Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  //exit;
}

require_once 'assets/config/config.php';
// require_once 'assets/config/functions.php';

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}
 
$query = "SELECT Rol, huella FROM users WHERE id= " . $_SESSION["id"] . " LIMIT 1";
 
$resultado = mysqli_query($link, $query);

foreach ($resultado as $row) {
  $rol = $row['Rol']; 
  $huella = $row['huella'];
}
$_SESSION["rol"] = $rol;
$status_huella = $huella;

 if ($rol != 'user') {
  
  header('Location: login.php');
}

$id_user = $_SESSION['id'];

$conexion = $link;
if (!$conexion) {
    exit;
    header('Location: login.php');
}


?>
<!-- Parte visual de la pagina web -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Huella</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body>
<body>
<header >
      <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow " >
        <div class="container-fluid dernav">
          <a class="navbar-brand"> 
            <img src="./assets/img/logo.png" width="120" height="40" alt="">   <!-- Logo -->
          </a>
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse lista_items" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
              <li class="nav-item ">
                <a class="nav-link active" aria-current="page" href="#" >Tag</a>
              </li>
              <li class="nav-item px-2">
                <a class="nav-link active" href="#">Recordatorios</a>
              </li>
              <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
                <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item " href="./assets/scripts/logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp; &nbsp;<i class="bi bi-box-arrow-right"></i></a> </li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item"  href="#"> &nbsp; Cuenta &nbsp; &nbsp;<i class="bi bi-person-circle"></i> </a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Something</a></li>
                </ul>  
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

  <br>
  
    <div class="container  mt-5">
      <div class="card text-center">
        <div class="card-header">
          Registrar Huella Dactilar
        </div>
        <div class="card-body">
          <h5 class="card-title pt-2 pb-3">Aun no has agregado tus huellas dactilares</h5>
          <div class="ms-3">
              <div class="card mx-auto" style="width: 18rem;">
                <img src="./assets/img/huella.png" class="card-img-top" alt="...">
              </div>
            </div>
           
          <p class="card-text pt-4 pb-2">Por favor dirigete al lugar donde se encuentra el módulo lector de huella. </br>
            Coloca tu dedo indice o pulgar en el lector hasta que se enciende una luz azul, espera 3 segundos vuelve a colocar el mismo dedo hasta que la luz azul vuelva a encenderse
          </p>
          <a href="assets/scripts/add_huella.php?id_add=<?php echo $id_user ?>" class="btn btn-outline-primary ">Agregar Huella </a>  <!-- Aqui es donde manda a llamar al arduino -->
         </div>
        <div class="card-footer text-muted mt-3">
        <?php 
            $mes = array("enero","febrero", "marzo", "abril", "marzo","abril","mayo", "junio","julio", "agosto","septiembre","noviembre","diciembre");
            $dia = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
            /* Establecer la hora de Mexico por que por defecto manda la del server  */  
            date_default_timezone_set("America/Mexico_City");      
            echo$dia[date('w')]. " ". date("d"). " de ". $mes[date("m")-1]. " de ". date("Y");?> 
        </div>
      </div>
    </div>
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>