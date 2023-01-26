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
  $hola2 = $row['huella'];
}
$_SESSION["rol"] = $rol;
$status_huella = $hola2;

 if ($rol != 'admin') {
  
  header('Location: login.php');
}

$id_user = $_SESSION['id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Usuarios</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  

<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/styles.css">


  <style>
   
  </style>
</head>

<body>
 
<!-- NAV BAR -->
   <!-- NAV BAR -->
   <header>
    <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
      <div class="container-fluid dernav">
        <a class="navbar-brand">
          <img src="./assets/img/logo.png" width="140" height="50" alt=""> <!-- Logo -->
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
              <a class="nav-link active" href="admin_users.php" tabindex="-1" aria-disabled="true">Usuarios</a>
            </li>
            <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
              <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
              </a>
              <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item"  href="#"> &nbsp; Cuenta &nbsp; &nbsp;<i class="bi bi-person-circle"></i> </a></li>

                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item " href="./assets/scripts/logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp; &nbsp;<i class="bi bi-box-arrow-right"></i></a> </li>
<!--
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Something</a></li>
-->
                </ul>  

              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
   <!-- NAV BAR -->


    <div class="px-4 pt-3  bienvenida">
    <div class="row">
      <div  class="col align-self-start">
        <h4>Bienvenido, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</h4>
        </div>
        <div class="col align-self-center"></div>
        <div class="col align-self-end d-flex flex-row-reverse pe-5"> 
          <?php   
            $mes = array("enero","febrero", "marzo", "abril", "marzo","abril","mayo", "junio","julio", "agosto","septiembre","noviembre","diciembre");
            $dia = array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sábado");
         ?>
          <p  class="d-flex">
            <?php 
             /* Establecer la hora de Mexico por que por defecto manda la del server  */  
               date_default_timezone_set("America/Mexico_City");      
              echo$dia[date('w')]. " ". date("d"). " de ". $mes[date("m")-1]. " de ". date("Y"). ".   ". date("h:i:sa");?> 
          </p>
        </div>
     </div>
   </div>
<!-- Alertas de confirmacion o  error -->
    <div class="container mt-2 principal rounded-3 shadow pt-3">
    <?php
            if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'error'){
            ?>
            <div class=" alerta_error alert alert-danger alert-dismissible fade show  text-center" role="alert">
                <strong>ERROR!</strong> Vuelve a intentar.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

            </div>
            <?php
                }
            ?>

            <?php
                if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'editado'){
            ?>
            <div class=" alerta_edit alert alert-success alert-dismissible fade show text-center" role="alert">
                <strong>EXITO!</strong> La contraseña fue actualizada
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
                }
            ?>   
<!-- Alertas de confirmacion o  error -->
  

            <h2 style="text-align: center;">Usuarios</h2>
            <p>Reporte de los usuarios registrados en el sistema.</p>  
            <!-- Barra de buscar -->
              <div class="row">
                  <nav class="navbar navbar ">
                    <div class="container-fluid">
                      <a class="navbar-brand"> </a>
                        <form class="d-flex col-md-3" role="search" action="" method="post">
                          <input class="form-control me-2 light-table-filter" type="search" placeholder="Buscar" aria-label="Buscar" name="campo" id="campo">
                        </form>
                    </div>
                  </nav>      
               </div>


          <div class="table-responsive text-center" >        
                <table class="table table table-bordered table-hover border border-secondary">
                  <thead>
                    <tr>
                     <tr>
                      <th>ID</th>
                      <th>Username</th>
                      <th>Rol</th>
                      <th>Fecha de Ingreso</th>
                      <th>Ultimo log</th>
                      <th>Contraseña</th>
                    </tr>
                    </tr>
                  </thead>
                  <tbody id="content">
                  
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
            let url = "./assets/scripts/ajax_users.php"
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
  <!-- 
    Alertas de edicion, borrado y error
  -->
        <script type="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                $(".alerta_error").fadeOut(1500);
            },2500);    
        });

        $(document).ready(function() {
            setTimeout(function() {
                $(".alerta_edit").fadeOut(1500);
            },2500);    
        });
      
        $(document).ready(function() {
            setTimeout(function() {
                $(".alerta_delete").fadeOut(1500);
            },2500);    
        });
        </script>


    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

</body>

</html>