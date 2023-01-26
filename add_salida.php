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
  header('Location: assets/config/login.php');
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

date_default_timezone_set("America/Mexico_City");      
$fecha_hoy = date("Y-m-d");  
$id = $_GET['id'];/* Extraemos el ID*/ 

$name =  $last_name =  $last_name2 = $entrada = $hora = '';
$hora_err = '';

$sql = "SELECT * FROM asistencia INNER JOIN users WHERE id_emp = '$id' AND fecha ='$fecha_hoy'";
$result = mysqli_query($conexion, $sql);
/* Extarer los datos del registro */ 
while($mostrar = mysqli_fetch_array($result)) {
       $name = $mostrar['name'];
       $last_name = $mostrar['last_name'];
       $last_name2 = $mostrar['last_name2'];
       $entrada = $mostrar['entrada'];
       $fecha = $mostrar['fecha'];
}

/* Validar los nuevos datos */ 
if($_SERVER["REQUEST_METHOD"] == "POST"){
  // Validar primer apellido
  if(empty(trim($_POST["hora"]))){
     $hora_err = "Por favor ingresa una Hora de salida.";     
 }else{
     $param_hora = trim($_POST["hora"]) ;  
     $hora = $param_hora ;
 }
date_default_timezone_set("America/Mexico_City");      
$fecha_hoy = date("Y-m-d");   
/* Hacer la insercion de los nuevis datos */ 
 if(empty($hora_err)){
$a =  mysqli_query($link, "UPDATE asistencia SET salida = '$hora' WHERE id_emp= '$id' AND fecha='$fecha_hoy'");
    
  if($a == TRUE){
   header('Location: admin_asistencia.php?mensaje=editado');
  }else{
   die(" No se puede Modificar el registro ");
   header('Location: admin_asistencia.php?mensaje=error');
   exit();
  }
}

}



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add salida</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
   <link rel="stylesheet" href="./assets/css/styles.css">
 
</head>

<body>
 <!--  Nav Bar -->
    <header >
      <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow " >
        <div class="container-fluid dernav">
          <a class="navbar-brand"> 
            <img src="./assets/img/logo.png" width="120" height="40" alt="">   <!-- Logo -->
          </a>
              <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
                <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item"  href="#"> &nbsp; Cuenta &nbsp; &nbsp;<i class="bi bi-person-circle"></i> </a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item " href="./assets/scripts/logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp; &nbsp;<i class="bi bi-box-arrow-right"></i></a> </li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Something</a></li>
                </ul>  
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>
 <!--  Nav Bar -->

    <div class="px-4 pt-3 pb-5 bienvenida">
        <div class="row">
            <div  class="col align-self-start">
                <h4>Registrar salida manualmente</h4>
            </div>
            <div class="col align-self-center"></div>
            <div class="col align-self-end d-flex flex-row-reverse pe-5"> 
            <?php   
             /* Arreglo para mostrar la fecha y hora de manera amigable  */  
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
    
        <div class="container">
      <div class="row justify-content-center my-5">
        <div class="row justify-content-center justify-content-md-center">
            <div class="card w-50 shadow text-center my-4">
                <div class="card-body pt-2">
                    
                    <div class="container-fluid">
                            <img src="./assets/img/logo.png" alt="" class="img-fluid ">
                    </div>

                        <h2 class="card-title">Bievenido</h2>
                        <div class="container pe-5 ps-5">
                        <?php
                            if (!empty($login_err)) {
                                echo '<div class="alert alert-danger">' . $login_err . '</div>';
                            }
                        ?>

                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-group">
                                    <label>Usuario</label>
                                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                                </div>
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                                </div>
                                <div class="form-group botnones">
                                    <!--  <input type="submit" class="btn btn-primary " value="Ingresar">-->
                                    
 
                                    <input type="submit" class="btn btn-outline-primary ps-4 px-4 mx-2" value="Ingresar">
                                    &ensp;
                                    <a class="btn btn-outline-secondary ps-4 px-4 " href="login.php" ><i class="bi bi-x-circle"></i> &nbsp; Limpiar</a> 

                                </div>
                                
                            </form>

                        

                            </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>



      
   
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>