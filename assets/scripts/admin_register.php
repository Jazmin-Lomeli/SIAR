<?php
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: ../../login.php");
  // exit;
}

require '../config/config.php';

//  Consulta para extaer el area o tipo de empleado 
$query ="SELECT * FROM tipo_empleado";
$resultado = $link->query($query); 
$conexion = $link;

if (!$conexion) {
  header('Location: login.php');
}
 
// Define variables and initialize with empty values
$name = $last_name = $last_name2 = $tel= $area = "";
$name_err = $last1_err = $last2_err = $tel_err = $area_err = "";
 
// Processing form data when form is submitted
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
  
    $area = $_POST["area"];
    if ($area == 'select') {
        $area_err = "Por favor ingresa una área laboral.";
     } else {
        $param_area = trim($_POST["area"]);
        $area = $param_area;
       
    }
    // Check input errors before inserting in database
    if(empty($name_err) && empty($last1_err) && empty($last2_err) && empty($tel_err) && empty($area_err)){
        date_default_timezone_set("America/Mexico_City");      
        $fecha = date('Y-m-d');
        // Prepare an insert statement
        $sql = "INSERT INTO empleados (nombre, apellido, seg_apellido, telefono, tipo, f_registro) VALUES (?,?,?,?,?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_name, $param_last1, $last_name2, $param_tel, $param_area, $fecha);
//, $param_password, $param_name, $param_last1, $param_last2, $param_tel 
            $param_name = $name;
            $param_last1 = $last_name;
            $param_last2 = $last_name2;
            $param_tel = $tel;
            $param_area = $area;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: ../../admin_reg.php?mensaje=add");
            } else{
                header("location: ../../admin_reg.php?mensaje=error");
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($link);
}
 
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Registrar</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
   <link rel="stylesheet" href="../css/styles.css">
  <link rel="stylesheet" href="../css/root.css">
 
</head>
<body>

    <header >
        <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
        <div class="container-fluid dernav">
            <a class="navbar-brand">
            <img src="../img/logo.png" width="140" height="50" alt=""> <!-- Logo -->
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse lista_items" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ">
                <li class="nav-item ">
                <a class="nav-link active" aria-current="page" href="../../admin_reg.php">Registros</a>
                </li>
                <li class="nav-item px-2">
                <a class="nav-link active" href="../../admin_asistencia.php">Asistencia</a>
                </li>

                <li class="nav-item">
                <a class="nav-link active" href="../../admin_recordatorios.php" tabindex="-1" aria-disabled="true">Recoradatorios</a>
                </li>
                <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo htmlspecialchars($_SESSION["username"]); ?>
                </a>
                <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item"  href="cuenta.php"> &nbsp; Cuenta &nbsp; &nbsp;<i class="bi bi-person-circle"></i> </a></li>

                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item " href="logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp; &nbsp;<i class="bi bi-box-arrow-right"></i></a> </li>
                    </ul>  

                </ul>
                </li>
            </ul>
            </div>
        </div>
        </nav>
    </header>

    <div class="px-4 pt-3 pb-0 bienvenida">
        <div class="row">
            <div  class="col-md-6 align-self-start pb-2">
                <h4>Registrar un empleado a la base de datos.</h4>
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
                echo$dia[date('w')]. " ". date("d"). " de ". $mes[date("m")-1]. " de ". date("Y");?> 
            </p>
            </div>
        </div>
    </div>  
    
    <div class="container shadow-none mt-0">
        <div class="row text-center justify-content-center my-4">
            <div class="col-md-6 wrapper shadow pt-3 pb-4 px-4" >
                <form method="post" id="formulario">  
                  <h3>Registrar Empleado</h3>
                    <div class="row pt-2" >
                        <div class="col-xl-12 col-lg-6 col-12 form-group pb-2">  
                            <label for= "nombre" class="">Nombre</label>
                            <input id="nombre" type="text" name="nombre" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-12 form-group pb-2">
                            <label for= "ape1">Apellido Paterno</label>
                            <input id= "ape1"type="text" name="ape1" class="form-control <?php echo (!empty($last1_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>">
                            <span class="invalid-feedback"><?php echo $last1_err; ?></span>   
                        </div>
                        <div class="col-xl-6 col-lg-6 col-12 form-group pb-2">
                            <label for= "ape2">Apellido Materno</label>
                            <input id= "ape2" type="text" name="ape2" class="form-control <?php echo (!empty($last2_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name2; ?>">
                            <span class="invalid-feedback"><?php echo $last2_err; ?></span>             
                        </div>
                        <div class="col-xl-6 col-lg-6 col-12 form-group pb-2">
                            <label for= "tel">Teléfono</label>
                            <input id= "tel" type="text" name="tel" maxlength="10" class="form-control <?php echo (!empty($tel_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $tel; ?>">
                            <span class="invalid-feedback"><?php echo $tel_err; ?></span>   
                        </div>

                        <div class="col-xl-6 col-lg-6 col-12 form-group">
                            <label for="area">Puesto</label>
                                <select name="area" class="form-control <?php echo (!empty($area_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $area; ?>">
                                    <option value="select">-- Seleccionar --</option>
                                        <?php                              
                                            while ($row = $resultado->fetch_assoc()) {
                                                echo '<option value="'.$row['tipo'].'">'.$row['t_nombre'].'</option>';
                                            }
                                        ?>
                                    </select>
                                    <span class="invalid-feedback">
                                        <?php echo $area_err; ?>
                                    </span>                
                        </div>

                              
                        <div class="col-xl-12 col-lg-12 col-12 form-group Botnones pt-4">
                            <input type="submit" class="btn btn-outline-success ps-5 px-5 mx-2" value="Crear">
                            <a class="btn btn-outline-danger ps-4 px-4" href="../../admin_reg.php" ><i class="bi bi-x-circle"></i> &nbsp; Cancelar</a> 
                        </div>
                </form>
             
             </div>
        </div>
    </div>
   
  <!-- JavaScript Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8" crossorigin="anonymous"></script>
</body>

</html>
</script>