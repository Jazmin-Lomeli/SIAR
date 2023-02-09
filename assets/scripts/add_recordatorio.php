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

// Define variables and initialize with empty values
$name = $area= $inicio = $fin = $descripcion = $caracter = "";
$name_err = $area_err = $inicio_err = $fin_err = $descripcion_err = $caracter_err =  "";

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
    $area = $_POST["area"];
    if ($area == 'select') {
        $area_err = "Por favor ingresa una área laboral.";
     } else {
        $param_area = trim($_POST["area"]);
        $area = $param_area;
    }
    $caracter = $_POST["caracter"];
    if ($caracter == 'select') {
        $caracter_err = "Por favor ingresa una caracter.";
     } else {
        $param_carct = trim($_POST["caracter"]);
        $caracter = $param_carct;
    }

    if(empty(trim($_POST["inicio"]))){
        $inicio_err = "Por favor ingresa una fecha.";     
    }else{
        $param_inicio = trim($_POST["inicio"]) ;  
        $inicio = $param_inicio ;
    }
    if(empty(trim($_POST["fin"]))){
        $fin_err = "Por favor ingresa una fecha.";     
    }else{
        $param_fin = trim($_POST["fin"]) ;  
        $fin = $param_fin ;
    }
    if(empty(trim($_POST["descripcion"]))){
        $descripcion_err = "Por favor ingresa una descripción.";     
    }elseif(!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ0-9.,]+$/', trim($_POST["descripcion"]))){  // Letras mayusculas y min
        $descripcion_err = " Descripción no valida.";
    }else{
        $param_desc = trim($_POST["descripcion"]) ;  
        $descripcion = $param_desc ;
    }
    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($area_err) && empty($area_err) && empty($fin_err) && empty($descripcion_err) && empty($caracter_err)){
        date_default_timezone_set("America/Mexico_City");      
        $fecha = date('Y-m-d');
        // Prepare an insert statement
        $sql = "INSERT INTO recordatorios (r_nombre, r_tipo, inicio, fin, descripcion, caracter) VALUES (?,?,?,?,?,?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "ssssss", $param_name, $param_area, $param_inicio, $param_fin, $param_desc, $param_carct);

            $param_name = $name;
            $param_area = $area;
            $param_inicio = $inicio;
            $param_fin = $fin;
            $param_carct = $caracter;

            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: ../../admin_recordatorios.php?mensaje=add");
            } else{
                header("location: ../../admin_recordatorios.php?mensaje=error");
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
    <title>Recoradatorios</title>
    <!-- CSS only -->
</head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="../css/styles.css">
<link rel="stylesheet" href="../css/root.css">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
            <div class="container-fluid dernav">
                <a class="navbar-brand">
                    <img src="../img/logo.png" width="140" height="50" alt=""> <!-- Logo -->
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
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
                            <a class="nav-link active" href="../../admin_recordatorios.php" tabindex="-1"
                                aria-disabled="true">Recoradatorios</a>
                        </li>
                        <li class="navbar-nav position-absolute end-0 " style="padding-right: 6rem;">
                            <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </a>
                            <ul class="dropdown-menu " aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"> &nbsp; Cuenta &nbsp; &nbsp;<i
                                            class="bi bi-person-circle"></i> </a></li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item " href="logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp;
                                        &nbsp;<i class="bi bi-box-arrow-right"></i></a> </li>
                            </ul>

                    </ul>
                    </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>


    <style>
        body {
            background: rgba(128, 128, 128, 0.5);
            height: 100%;
        }
        .cont {
            background: ghostwhite;
            height: 100%;
            border-radius: 10px;
            padding-bottom: 1em;
            padding-top: 0.5em;
            margin-top:1em;
        }

    </style> 

    <div class="container shadow-none mt-0">
        <div class="row text-center justify-content-center my-4">
            <div class="cont col-md-8 wrapper shadow px-5" >
            <h3 class="pt-2">Agregar recordatorio</h3>
                        <p> Por favor llena el formulario con los datos solicitados </p>
                <form method="post" id="formulario">  
                
                        <div class="row g-3 pt-2 pb-3">
                            <div class="col-sm-6 center mt-6 form-group">
                                <label for="nombre" class="espacio">Nombre del recordatorio</label>
                                <input id="nombre" type="text" name="nombre"
                                    class="form-control text-center <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $name; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $name_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-6 center mt-6 form-group">
                                <label for="area" class="espacio">Área laboral</label>
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
                            <div class="col-sm-4 center mt-4 form-group">
                                <label for="inicio" class="espacio">Fecha de inicio</label>
                                <input id="inicio" type="date" name="inicio"
                                    class="form-control text-center <?php echo (!empty($inicio_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $inicio; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $inicio_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-4 center mt-4 form-group">
                                <label for="fin" class="espacio">Fecha limite</label>
                                <input id="fin" type="date" name="fin"
                                    class="form-control text-center <?php echo (!empty($fin_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $fin; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $fin_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-4 center mt-4 form-group">
                                <label for="caracter" class="espacio">caracter</label>
                                <select name="caracter" class="form-control <?php echo (!empty($caracter_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $caracter; ?>">
                                    <option value="select">-- Seleccionar --</option>
                                    <option value="Urgente"> Urgente </option>
                                    <option value="No Urgente"> No Urgente </option>
                                        
                                        
                                    </select>
                                    <span class="invalid-feedback">
                                        <?php echo $caracter_err; ?>
                                    </span>       
                            </div>
                                                                

                            <div class="form-group">
                            <label for="descripcion" class="espacio">Descripcion del recordatorio</label>
                            <textarea id="descripcion" name="descripcion" rows="3"  col="5" class="form-control <?php echo (!empty($descripcion_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $descripcion; ?>"> 
                                </textarea>
                                <span class="invalid-feedback">
                                    <?php echo $descripcion_err; ?>
                                </span>
                            </textarea>
                        </div>
                       
 

                        

                              
                        <div class="col-xl-12 col-lg-12 col-12 form-group Botnones pt-4">
                            <input type="submit" class="btn btn-outline-success ps-5 px-5 mx-2" value="Crear">
                            <a class="btn btn-outline-danger ps-4 px-4" href="../../admin_recordatorios.php" ><i class="bi bi-x-circle"></i> &nbsp; Cancelar</a> 
                        </div>
                </form>
             
             </div>
        </div>
    </div>




 

    <!-- JavaScript Bundle with Popper -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
        crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>

    Hola mundo
</body>

</html>