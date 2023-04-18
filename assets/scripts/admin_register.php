<?php
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../login.php");
    // exit;
}

require '../config/config.php';

//  Consulta para extaer el area o tipo de empleado 
$query = "SELECT * FROM tipo_empleado";
$resultado = $link->query($query);
$conexion = $link;

if (!$conexion) {
    header('Location: login.php');
}

// Define variables and initialize with empty values
$name = $last_name = $last_name2 = $tel = $area = $jornada = "";
$name_err = $last1_err = $last2_err = $tel_err = $area_err = $jornada_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar primer apellido
    if (empty(trim($_POST["nombre"]))) {
        $name_err = "Por favor ingresa un nombre.";
    } elseif (!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["nombre"]))) { // Letras mayusculas y min
        $name_err = " El nombre solo puede contener letras.";
    } else {
        $param_name = trim($_POST["nombre"]);
        $name = $param_name;
    }
    // Validar primer apellido
    if (empty(trim($_POST["ape1"]))) {
        $last1_err = "Por favor ingresa un apellido.";
    } elseif (!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["ape1"]))) { // Letras mayusculas y min
        $last1_err = " El apellido solo puede contener letras.";
    } else {
        $param_last1 = trim($_POST["ape1"]);
        $last_name = $param_last1;
    }
    // Validar primer apellido
    if (empty(trim($_POST["ape2"]))) {
        $last2_err = "Por favor ingresa un apellido.";
    } elseif (!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["ape2"]))) { // Letras mayusculas y min
        $last2_err = " El apellido solo puede contener letras.";
    } else {
        $param_last2 = trim($_POST["ape2"]);
        $last_name2 = $param_last2;
    }

    // Validar primer apellido
    if (empty(trim($_POST["tel"]))) {
        $tel_err = "Por favor ingresa un telefono.";
    } elseif (!preg_match('/^[0-9]+$/', trim($_POST["tel"])) && strlen(trim($_POST["tel"])) != 10) {
        $tel_err = " El telefono solo puede contener números.";
    } else {
        $param_tel = trim($_POST["tel"]);
        $tel = $param_tel;
    }

    $area = $_POST["area"];
    if ($area == 'select') {
        $area_err = "Por favor ingresa una área laboral.";
    } else {
        $param_area = trim($_POST["area"]);
        $area = $param_area;

    }
    $jornada = $_POST["jornada"];
    if ($jornada == '0') {
        $jornada_err = "Por favor ingresa un número valido";
    } else {
        $param_jornada = trim($_POST["jornada"]);
        $jornada = $param_jornada;

    }

    // jornada_err
    // Check input errors before inserting in database
    if (empty($name_err) && empty($last1_err) && empty($last2_err) && empty($tel_err) && empty($area_err) && empty($jornada_err)) {
        date_default_timezone_set("America/Mexico_City");
        $fecha = date('Y-m-d');
        // Prepare an insert statement
        $sql = "INSERT INTO empleados (nombre, apellido, seg_apellido, telefono, tipo, f_registro, jornada) VALUES (?,?,?,?,?,?,?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $param_name, $param_last1, $last_name2, $param_tel, $param_area, $fecha, $param_jornada);
            //, $param_password, $param_name, $param_last1, $param_last2, $param_tel 
            $param_name = $name;
            $param_last1 = $last_name;
            $param_last2 = $last_name2;
            $param_tel = $tel;
            $param_area = $area;
            $param_jornada = $jornada;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: ../../admin_reg.php?mensaje=add");
            } else {
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="../css/styles.css" />

    <link rel="shortcut icon" href="../img/icono.png">


</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light pl-5 shadow ">
            <div class="container-fluid dernav">
                <a class="navbar-brand">
                    <img src="../img/logo_3.png" width="140" height="50" alt=""> <!-- Logo -->
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
                                <li><a class="dropdown-item" href="cuenta.php"> &nbsp; Cuenta &nbsp;
                                        &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp;
                                        <i class="bi bi-person-circle"></i> </a></li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item " href="sistema.php">&nbsp; Sistema &nbsp;
                                        &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp;
                                        <i class="bi bi-gear"></i></a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item " href="logout.php">&nbsp; Salir &nbsp;
                                        &nbsp; &nbsp;
                                        &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp;
                                        <i class="bi bi-box-arrow-right"></i></a> </li>

                            </ul>


                    </ul>
                    </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <style>
        .cont {
            background: ghostwhite;
            height: 100%;
            border-radius: 10px;
            padding-bottom: 1em;
            padding-top: 0.5em;
            margin-top: 1em;
        }
    </style>
    <div class="d-flex align-items-end flex-column ">
        <div class="mt-auto">
            <a class="btn" data-bs-toggle="modal" data-bs-target="#departamento">
                <img src="../img/pregunta.png" width="50px">
            </a>
        </div>

        <!-- Agregar empleado -->
        <div class="container shadow-none" style="margin-top: -2em; ">
            <div class="row text-center justify-content-center my-2">
                <div class="cont col-md-8 wrapper shadow px-5 pt-3">
                    <h3>Registrar Empleado</h3>
                    <p> Por favor llena el formulario con los datos solicitados </p>
                    <form method="post" id="formulario" class="pb-3">
                        <div class="row g-3 pt-2 pb-3">
                            <div class="col-sm-6 center mt-6 form-group"><!-- Nombre -->
                                <label for="nombre" class="">Nombre</label>
                                <input id="nombre" type="text" name="nombre"
                                    class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $name; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $name_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-6 center mt-6 form-group"> <!-- Apellido -->
                                <label for="ape1">Apellido Paterno</label>
                                <input id="ape1" type="text" name="ape1"
                                    class="form-control <?php echo (!empty($last1_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $last_name; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $last1_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-6 center mt-6 form-group"> <!-- Apellido 2  -->
                                <label for="ape2">Apellido Materno</label>
                                <input id="ape2" type="text" name="ape2"
                                    class="form-control <?php echo (!empty($last2_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $last_name2; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $last2_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-6 center mt-6 form-group"> <!--  Telefono  -->
                                <label for="tel">Teléfono</label>
                                <input id="tel" type="text" name="tel" maxlength="10"
                                    class="form-control <?php echo (!empty($tel_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $tel; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $tel_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-6 center mt-6 form-group"> <!--  Joranada  -->
                                <label for="area"> Departamento </label>
                                <select name="area"
                                    class="form-control <?php echo (!empty($area_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $area; ?>">
                                    <option value="select">-- Seleccionar --</option>
                                    <?php
                                    while ($row = $resultado->fetch_assoc()) {
                                        if ($row['tipo'] > 1) {
                                            echo '<option value="' . $row['tipo'] . '">' . $row['t_nombre'] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <span class="invalid-feedback">
                                    <?php echo $area_err; ?>
                                </span>
                            </div>
                            <div class="col-sm-6 center mt-6 form-group"> <!--  Joranada  -->
                                <label for="jornada">Jornada semanal (Hrs)</label>

                                <input id="jornada" type="number" name="jornada" min="1" max="100" value="0"
                                    class="form-control <?php echo (!empty($jornada_err)) ? 'is-invalid' : ''; ?>"
                                    value="<?php echo $jornada; ?>">
                                <span class="invalid-feedback">
                                    <?php echo $jornada_err; ?>
                                </span>
                            </div>
                            <!-- Botones -->
                            <div class="col-xl-12 col-lg-12 col-12 form-group Botnones pt-4">
                                <input type="submit" class="btn btn-outline-success ps-5 px-5 mx-2" value="Crear">
                                <a class="btn btn-outline-danger ps-4 px-4" href="../../admin_reg.php"><i
                                        class="bi bi-x-circle"></i> &nbsp; Cancelar</a>
                            </div>

                        </div>
                    </form>

                </div>
            </div>
        </div>

        <!-- Modal informativo -->
        <div class="modal fade pt-5" id="departamento" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title " id="staticBackdropLabel">Departamento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center pt-2">
                        <h5>No encuentro el departemento</h5>
                        <h6>
                            Si el dapartemento al que pertence el empleado no aparece, deberás agregarlo a la base de
                            datos.
                            <br>
                            <br>
                            Para hecerlo da clic al boton de agregar y despues en el boton de
                        </h6>

                        <a type="button" class="btn btn-outline-info btn-lg ml-2 mx-2">
                            <i class="bi bi-folder-plus">
                            </i></a>
                        <h6>

                        </h6>
                    </div>

                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-primary px-4"
                            data-bs-dismiss="modal">Entendido</button>

                        <a type="button" class="btn btn-outline-success px-4" href="../../admin_reg.php">Agregrar</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal informativo -->






        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
            crossorigin="anonymous"></script>
</body>

</html>
</script>