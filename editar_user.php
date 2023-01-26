<?php
// Initialize the session
session_start();

// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  // exit;
}
 
require_once 'assets/config/config.php';
  
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

if (!$conexion) {
  exit;
  header('Location: login.php');
}   
$id = $_GET['id'];/* Extraemos el ID*/ 

$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err ="";
 
$sql = "SELECT * FROM users wHERE id = '$id'";
$result = mysqli_query($conexion, $sql);
/* Extarer los datos del registro */ 
while($mostrar = mysqli_fetch_array($result)) {
       $username = $mostrar['username'];      /* Extraer nombre de usuario */ 
 }
/* No se podra cambiar el nombre de usuario xq si se valida que no haya uni igual y ese no lo quiere  cambiar dará error */ 

/* Validar los nuevos datos */ 
if($_SERVER["REQUEST_METHOD"] == "POST"){


    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Ingresa una Contraseña.";     
    } elseif(strlen(trim($_POST["password"])) < 6){     // contraseña mayor a 6 caracteres 
        $password_err = "La Contaseña debe tner minimo 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirma tu Contraseña.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "La Contaseña no Coincide.";
        }
    }
     
    
/* Hacer la insercion de los nuevis datos */
    if(empty($password_err) && empty($confirm_password_err)){

        $param_password = password_hash($password, PASSWORD_DEFAULT); /*  Agregar el HASH  a la contraseña */

        $a = mysqli_query($link, "UPDATE users SET password = '$param_password' WHERE id = '$id'");
        if($a == TRUE){
          header('Location: admin_users.php?mensaje=editado');
         }else{
          die(" No se puede Modificar el registro ");
          header('Location: admin_users.php?mensaje=error');
          exit();
         }
     }
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Inicio</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/styles.css">
<!-- CSS only -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./assets/css/registros.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.9/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <style>
    .bienvenida{font-style: oblique; }
     body{ font: 14px sans-serif; background: rgb(247, 245, 245);}
     label{font-size: 1.1em; }
  </style>
</head>

<body>

<header >
      <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow " style="background-color:  #65C27C">
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
                <a class="nav-link active" href="#" tabindex="-1" aria-disabled="true">Usuarios</a>
              </li>
              <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
                <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                  <li><a class="dropdown-item " href="logout.php">Salir</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item">Id: <?php echo htmlspecialchars($id_user); ?></a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item" href="#">Something</a></li>
                </ul>  
              </li>
            </ul>
          </div>
        </div>
      </nav>
</header>
 


    <div class="container pb-5 mt-5">
    <!-- Formulario para cambiar los datos -->       
        <div class="container shadow-none p-2">
            <div class="row text-center justify-content-center my-4">
                <div class="col-md-6 wrapper shadow p-3 " >
                <form method="post" id="formulario">
                        <h2>Editar registro de usuario</h2>
                        <p> Por favor edita los campos erroneos </p>
                        
                        <div class="row g-2">
                            <div class="col-sm-12 text-start mt-2 form-group">  
                                <label for= "nombre" class= "espacio">Nombre de Usuario</label>
                                <input id="nombre" type="text" name="nombre" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?> " readonly>
                                <span class="invalid-feedback"><?php echo $username_err; ?></span>
                            </div>
                            <h6 class="pt-2" > Cambiar contraseña </h6>
                            <div class="col-sm-6 text-start form-group">
                                <label for= "ape1">Contraseña</label>
                                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                                <span class="invalid-feedback"><?php echo $password_err; ?></span>         
                            </div>
                            <div class="col-sm-6 text-start form-group">
                                <label>Repite tu contraseña</label>
                                <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                                <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>               
                            </div>
                            <div class="form-group col-md-12 botnones pt-3">
                                <input type="submit" class="btn btn-outline-success" value="Editar" onclick="return confirm('Estás seguro de editar el registro?');">
                                <a class="btn btn-outline-danger ml-2" href="admin_users.php">Cancelar</a> 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>    


  <!-- JavaScript Bundle with Popper -->
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>



</body>

</html>