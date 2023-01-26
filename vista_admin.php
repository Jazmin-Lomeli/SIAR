<?php
// Initialize the session
session_start();

// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  // exit;
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
  $hola = $row['Rol']; 
}
$_SESSION["rol"] = $hola;

      
$id_user = $_SESSION['id'];

$query2 = "SELECT huella FROM users WHERE id= " . $id_user. " LIMIT 1";
$resultado2 = mysqli_query($link, $query2);

foreach ($resultado2 as $row) {
  $hola2 = $row['huella']; 
}
$status_huella = $hola2;

 
$conexion = $link;

if (!$conexion) {
  exit;
  header('Location: login.php');
}
$nombre = $ape = $ape2 = $id = $n_user = $nombre_c = " ";
 
 


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Home</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/root.css">
  <style>
      .lista_items ul li a:hover {  cursor: point; border-radius: 10%; background-color: powderblue; transition: background-color .5s; }
      /*.navbar{ font-size: 1.3em;  }*/
  </style>
</head>

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
              <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="admin_reg.php">Registros</a>
              </li>
              <li class="nav-item">
                <a class="nav-link active" href="admin_asistencia.php">Asistencia</a>
              </li>

              <li class="nav-item">
                <a class="nav-link active" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
              </li>
              <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
                <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item " href="logout.php">Salir</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item">ID usuario: <?php echo htmlspecialchars($id_user); ?></a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Something</a></li>
                </ul>  
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
 

 
    <div class="container mt-5 ">
      
      <h2 class="text-center"> Empleados Registrados</h2>
      <p></p>            
      <table class="table table-striped">
        <thead class="text-center" >
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th> </th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?php echo htmlspecialchars($id); ?></td>
            <td><?php echo htmlspecialchars($nombre_c); ?></td>
            <td><?php echo htmlspecialchars($n_user); ?></td>
            <td></td>
          </tr>
           

        </tbody>
      </table>
    </div>









 


 

    


   
       AQUI poner la tabla de asistencias 

      <a href="logout.php" class="btn btn-danger ml-3">Cerrar sesion</a>


  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>
