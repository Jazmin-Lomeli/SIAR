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
 
$id = $_GET['id'];/* Extraemos el ID*/ 

$name = $last_name = $last_name2 =  $tel= "";
$name_err = $last1_err = $last2_err = $tel_err= "";
 
$sql = "SELECT * FROM empleados WHERE id = '$id'";
$result = mysqli_query($conexion, $sql);
/* Extarer los datos del registro */ 
while($mostrar = mysqli_fetch_array($result)) {
       $name = $mostrar['nombre'];
       $last_name = $mostrar['apellido'];
       $last_name2 = $mostrar['seg_apellido'];
       $tel = $mostrar['telefono'];
}
/* Validar los nuevos datos */ 
if($_SERVER["REQUEST_METHOD"] == "POST"){
     // Validar primer apellido
     if(empty(trim($_POST["nombre"]))){
        $name_err = "Por favor ingresa un nombre.";     
    }elseif(!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["nombre"]))){  // Letras mayusculas y min
        $name_err = " El nombre solo puede contener letras.";
    }else{
        $param_name = trim($_POST["nombre"]) ;  
        $name = $param_name ;
    }
    // Validar primer apellido
    if(empty(trim($_POST["ape1"]))){
        $last1_err = "Por favor ingresa un apellido.";     
    }elseif(!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["ape1"]))){  // Letras mayusculas y min
        $last1_err = " El apellido solo puede contener letras.";
    }else{
        $param_last1 = trim($_POST["ape1"]) ;  
        $last_name = $param_last1 ;
    }
     // Validar primer apellido
     if(empty(trim($_POST["ape2"]))){
        $last2_err = "Por favor ingresa un apellido.";     
    }elseif(!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["ape2"]))){  // Letras mayusculas y min
        $last2_err = " El apellido solo puede contener letras.";
    }else{
        $param_last2 = trim($_POST["ape2"]) ;  
        $last_name2 = $param_last2 ;
    }
     
    // Validar primer apellido
    if(empty(trim($_POST["tel"]))){
        $tel_err = "Por favor ingresa un telefono.";     
    }elseif(!preg_match('/^[0-9]+$/', trim($_POST["tel"])) && strlen(trim($_POST["tel"])) != 10){  
        $tel_err = " El telefono solo puede contener números.";
    }else{
        $param_tel= trim($_POST["tel"]) ;  
        $tel = $param_tel;
    }
/* Hacer la insercion de los nuevis datos */ 
    if(empty($name_err) && empty($last1_err) && empty($last2_err) && empty($tel_err)){
        echo $name. " ". $param_last1. " ". $param_last2 . " " .$id;

   $a =  mysqli_query($link, "UPDATE users SET name = '$name',  last_name = '$last_name',   last_name2 = '$last_name2',   telefono = '$tel' WHERE id = '$id'");
       
     if($a == TRUE){
      header('Location: admin_reg.php?mensaje=editado');
     }else{
      die(" No se puede modificar el registro ");
      header('Location: admin_reg.php?mensaje=error');
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
              <a class="nav-link active" href="admin_users.php" tabindex="-1" aria-disabled="true">Recoradatorios</a>
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
                </ul>  

              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>
</header>
 


<div class="container">
 <!-- Formulario para cambiar los datos -->       
  <div class="container shadow-none p-1">
        <div class="row text-center justify-content-center my-4">
            <div class="col-md-6 wrapper shadow p-3" >
                <form method="post" id="formulario">
                  <h2 class="pb-2">Editar registro</h2>
                  <p> Por favor modifica solo los campos erroneos </p>
                    
                    <div class="row g-3 pt-2">
                        <div class="col-sm-12 center mt-2 form-group">  
                            <label for= "nombre" class= "espacio">Nombre</label>
                            <input id="nombre" type="text" name="nombre" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="col-sm-6 centert form-group">
                            <label for= "ape1">Apellido Paterno</label>
                            <input id= "ape1"type="text" name="ape1" class="form-control <?php echo (!empty($last1_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>">
                            <span class="invalid-feedback"><?php echo $last1_err; ?></span>   
                        </div>
                        <div class="col-sm-6 centert form-group">
                            <label for= "ape2">Apellido Materno</label>
                            <input id= "ape2" type="text" name="ape2" class="form-control <?php echo (!empty($last2_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name2; ?>">
                            <span class="invalid-feedback"><?php echo $last2_err; ?></span>             
                        </div>
                        <div class="col-sm-6 center form-group">
                            <label for= "tel">Telefono</label>
                            <input id= "tel" type="text" name="tel" class="form-control <?php echo (!empty($tel_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tel; ?>">
                            <span class="invalid-feedback"><?php echo $tel_err; ?></span>   
                        </div>
                        <div class="col-sm-6 text-center form-group">
                            <label for= "tel">Telefono</label>
                            <input id= "tel" type="text" name="tel" class="form-control <?php echo (!empty($tel_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tel; ?>">
                            <span class="invalid-feedback"><?php echo $tel_err; ?></span>   
                        </div>
                    <div class="col-xl-12 col-lg-12 col-12 form-group Botnones pt-4">
                      <input type="submit" class="btn btn-outline-success ps-5 px-5 mx-2" value="Crear">
                      <a class="btn btn-outline-danger ps-4 px-4" href="admin_reg.php" ><i class="bi bi-x-circle"></i> &nbsp; Cancelar</a> 
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