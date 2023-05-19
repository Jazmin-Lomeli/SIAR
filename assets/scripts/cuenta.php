<?php
/* Seguridad de Sesiones */
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../login.php");
    // exit;
}

$nombre_usuario = $_SESSION['username'];
require '../config/config.php';

$usuario = $_SESSION["username"];
// Detalles empleado 
$sql_cuenta = "SELECT * FROM users Where username = '$usuario'";
$result_cuenta = mysqli_query($link, $sql_cuenta);
while ($row = mysqli_fetch_array($result_cuenta)) {
    $ult_log = $row['ultimo_log'];
    $f_ingreso = $row['f_ingreso'];
    $cambio_contra = $row['cambio_contrasena'];
    $id = $row['id'];
}
if ($cambio_contra == NULL) {
    $cambio_contra = "- - -";
}
$name = $pass = $pass2 = '';
$name_err = $pass_err = $pass2_err = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'add') {

        if (empty(trim($_POST["nombre"]))) {
            $name_err = "Ingresa un Usuario.";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["nombre"]))) {
            $name_err = "Solo puede contener letras, numeros y guión bajo.";
        } else {
            // Prepare a select statement
            $sql = "SELECT id FROM users WHERE username = ?";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_username);

                // Set parameters
                $param_username = trim($_POST["nombre"]);

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    /* store result */
                    mysqli_stmt_store_result($stmt);

                    if (mysqli_stmt_num_rows($stmt) == 1) {
                        $name_err = "El nombre de usuario no está disponible.";
                    } else {
                        $name = trim($_POST["nombre"]);
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }

        if (empty(trim($_POST["pass"]))) {
            $pass_err = "Ingresa una Contraseña.";
        } elseif (strlen(trim($_POST["pass"])) < 6) { // contraseña mayor a 6 caracteres 
            $pass_err = "La Contaseña debe tener minímo 6 caracteres.";
        } else {
            $pass = trim($_POST["pass"]);
        }
        // Validate confirm password
        if (empty(trim($_POST["pass2"]))) {
            $pass2_err = "Confirma tu Contraseña.";
        } else {
            $pass2 = trim($_POST["pass2"]);
            if (empty($pass2_err) && ($pass != $pass2)) {
                $pass2_err = "La Contraseña no Coincide.";
                $pass_err = "La Contraseña no Coincide.";
            }
        }

        if (empty($name_err) && empty($pass2_err) && empty($pass_err)) {
            date_default_timezone_set("America/Mexico_City");
            $f_ingreso = date('Y-m-d');

            $sql = "INSERT INTO users (username, password, f_ingreso) VALUES (?,?,?)";

            if ($stmt = mysqli_prepare($link, $sql)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_password, $f_ingreso);

                $param_username = $name;


                $param_password = password_hash($pass, PASSWORD_DEFAULT); // Creates a password hash

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to login page
                    header("location: cuenta.php?mensaje=correcto");
                } else {
                    header("location:  cuenta.php?mensaje=error");
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }

        }
    } else {
        if (empty(trim($_POST["pass1"])) && empty(trim($_POST["pass2"]))) {
            header("location:  cuenta.php");
        } elseif (empty(trim($_POST["pass1"])) || empty(trim($_POST["pass2"]))) {
            header("location:  cuenta.php?mensaje=pass_err");
        } elseif (strlen(trim($_POST["pass1"])) < 6) {
            header("location:  cuenta.php?mensaje=pass_length");
        } else {
            $pass = trim($_POST["pass1"]);
            $confirm_pass = trim($_POST["pass2"]);
            if ($pass == $confirm_pass) {

                $param_password = password_hash($pass, PASSWORD_DEFAULT); /*  Agregar el HASH  a la contraseña */
                date_default_timezone_set("America/Mexico_City");

                $f_change = date('Y-m-d');

                $a = mysqli_query($link, "UPDATE users SET password = '$param_password', cambio_contrasena = '$f_change' WHERE id = '$id'");
                if ($a == TRUE) {
                    header("location:  cuenta.php?mensaje=new_pass");
                } else {
                    header("location:  cuenta.php?mensaje=failed");
                }
            } else {
                header("location:  cuenta.php?mensaje=no");
            }

        }

    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Cuenta</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/root.css">
    <link rel="shortcut icon" href="../img/icono.png">


</head>

<body>
    <style>
        body {
            background: rgba(128, 128, 128, 0.2);
            height: 100%;
        }

        .cont {
            background: ghostwhite;
            height: 100%;
            border-radius: 10px;
            padding-bottom: 1em;
            padding-top: 0.5em;
            margin-top: 1em;
        }
    </style>

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

    <?php
    if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'add') {
        ?>
        <!--
                                            <div class="d-flex align-items-end flex-column ">
                                                <div class="mt-auto p-2">
                                                    <a class="btn" data-bs-toggle="modal" data-bs-target="#password">
                                                        <img src="../img/pregunta.png" width="50px">
                                                    </a>
                                                </div>
                                        -->
        <div class="container rounded mt-5">
            <div class="row justify-content-center">
                <div class="col-sm-10 col-md-10 col-lg-9 wrapper pt-3 pb-4 ps-2">
                    <div class="card text-center ">
                        <div class="card-header">
                            Usuarios
                        </div>
                        <div class="m-0 row align-items-center justify-content-center">
                            <div class="row px-2 col-8  ">
                                <div class="card-body">
                                    <h5 class="card-title">Crear nuevo usuario</h5>
                                    <div class="container justify-content-center align-items-center">

                                        <form class="col-md-12 col-xl-12 pb-3 pt-2" method="post" id="formulario">
                                            <div class="row justify-content-center align-items-center">
                                                <div class="col-xl-6 col-lg-10 col-sm-10 form-group pb-2">
                                                    <label for="nombre" class="">Nombre de usuario</label>
                                                    <input id="nombre" type="text" name="nombre"
                                                        class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                                        value="<?php echo $name; ?>">
                                                    <span class="invalid-feedback">
                                                        <?php echo $name_err; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center align-items-center">
                                                <div class="col-xl-6 col-lg-6 col-sm-10  form-group pb-2">
                                                    <label for="pass" class="">Contraseña</label>
                                                    <input id="pass" type="password" name="pass"
                                                        class="form-control <?php echo (!empty($pass_err)) ? 'is-invalid' : ''; ?>"
                                                        value="<?php echo $pass; ?>">
                                                    <span class="invalid-feedback">
                                                        <?php echo $pass_err; ?>
                                                    </span>
                                                </div>
                                                <div class="col-xl-6 col-lg-6 col-sm-10 form-group pb-2">
                                                    <label for="pass2" class="">Contraseña</label>
                                                    <input id="pass2" type="password" name="pass2"
                                                        class="form-control <?php echo (!empty($pass2_err)) ? 'is-invalid' : ''; ?>"
                                                        value="<?php echo $pass2; ?>">
                                                    <span class="invalid-feedback">
                                                        <?php echo $pass2_err; ?>
                                                    </span>
                                                </div>

                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-12 form-group pt-4">
                                                <input type="submit" class="btn btn-outline-success ps-5 px-5 mx-2"
                                                    value="Crear">
                                                <a class="btn btn-outline-danger ps-4 px-4" href="cuenta.php"><i
                                                        class="bi bi-x-circle"></i> Cancelar</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted">
                            <?php
                            $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
                            $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");

                            /* Establecer la hora de Mexico por que por defecto manda la del server  */
                            date_default_timezone_set("America/Mexico_City");
                            echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y");
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        </div>






        <?php
    } else {
        ?>
        <div class="d-flex align-items-end flex-column ">
            <div class="mt-auto">
                <a class="btn" data-bs-toggle="modal" data-bs-target="#password">
                    <img src="../img/pregunta.png" width="50px">
                </a>
            </div>



            <div class="container shadow-none mt-0">
                <div class="row text-center justify-content-center  ">
                    <div class="cont col-md-8 wrapper shadow px-5">
                        <h3 class="pt-2">Cuenta</h3>
                        <h5 class="pb-2"> Información general se la sesión actual </h5>
                        <!-- Alertas -->
                        <?php
                        if ($cambio_contra == 0) {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-info alert-dismissible fade show text-center" role="alert">
                                    <strong>Actualiza</strong> tu contraseña lo más pronto posible
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>

                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'correcto') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-success alert-dismissible fade show text-center" role="alert">
                                    <strong>¡Éxito!</strong> Usuario creado con éxito.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>

                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'error') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-danger alert-dismissible fade show text-center" role="alert">
                                    <strong>¡Error!</strong> No se pudo crear el usuario.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>

                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'pass_err') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-danger alert-dismissible fade show text-center" role="alert">
                                    <strong>¡Error!</strong> El formulario no se lleno en su totalidad.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>

                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'no') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-danger alert-dismissible fade show text-center" role="alert">
                                    <strong>¡Error!</strong> Las contraseñas no coinciden.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'pass_length') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-danger alert-dismissible fade show text-center" role="alert">
                                    <strong>¡Error!</strong> Las contraseña es muy corta, deben ser <strong>minímo 6
                                        caracteres</strong>.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'new_pass') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-success alert-dismissible fade show text-center" role="alert">
                                    <strong>¡ÉXITO!</strong> La contraseña fue actualizada.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <?php
                        if (isset($_GET['mensaje']) and $_GET['mensaje'] == 'failed') {
                            ?>
                            <div class="row justify-content-center pt-2 px-5">
                                <div class="alerta alert alert-danger alert-dismissible fade show text-center" role="alert">
                                    <strong>¡ERROR!</strong> No se puedo actualizar la contraseña.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <!-- Alertas -->
                        <div class="row px-1 text-start">
                            <div class="col-sm-4 col-md-3 col-lg-3 rounded ">
                                <img src="../img/user.png" class="img-thumbnail" alt="...">
                            </div>
                            <div class="col-sm-8 col-md-8 col-lg-8 ">
                                <h6 class="lead">
                                    <strong>Nombre de usuario: </strong>
                                    <small class="text-muted">
                                        <?php echo $_SESSION["username"]; ?>
                                    </small>
                                </h6>
                                <br>
                                <h6 class="lead">
                                    <strong>Fecha de ingreso: </strong>
                                    <small class="text-muted">
                                        <?php echo $f_ingreso; ?>
                                    </small>
                                </h6>
                                <br>
                                <h5 class="lead">
                                    <strong>Última contraseña: </strong>
                                    <small class="text-muted">
                                        <?php echo $cambio_contra; ?>
                                    </small>
                                </h5>
                            </div>

                            <div class="d-flex align-items-end flex-column ">

                                <div class="mt-auto p-2 px-2 border rounded" style="background-color: ;">
                                    <div class="btn-group">
                                        <abbr title='Volver a inicio'>
                                            <a href="../../admin_reg.php" type="button"
                                                class="btn btn-outline-primary btn-lg mx-1"><i
                                                    class="bi bi-house-door-fill"></i></a>

                                        </abbr>
                                        <abbr title='Agregar un nuevo usuario'>
                                            <a href="cuenta.php?mensaje=add" type="button"
                                                class="btn btn-outline-secondary btn-lg mx-1"><i
                                                    class="bi bi-person-plus-fill"></i></a>
                                        </abbr>

                                        <abbr title='Cambiar contraseña'>

                                            <a data-bs-toggle="modal" data-bs-target="#change_password" type="button"
                                                class="btn btn-outline-success btn-lg mx-1"><i
                                                    class="bi bi-key-fill"></i></a>
                                        </abbr>
                                    </div>
                                </div>
                            </div>



                        </div>
                    </div>
                </div>

                <!-- Modal informativo -->
                <div class="modal fade pt-5" id="password" data-bs-keyboard="false" tabindex="-1"
                    aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header text-center">
                                <h5 class="modal-title " id="staticBackdropLabel">Información</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <?php
                            if ($cambio_contra == 0) {
                                ?>
                                <div class="modal-body text-center pt-2">
                                    <h5>Contraseña por defecto</h5>
                                    <h6> Actualmente la cuenta tiene la contraseña por defecto, por seguridad cambiala lo más
                                        pronto
                                        posible</h6>
                                    <img src="../img/contrasena.png" class="rounded mx-auto d-block" alt="...">
                                </div>

                                <?php
                            } else {
                                ?>
                                <div class="modal-body text-center pt-2">
                                    <h5>Seguridad de la cuenta</h5>
                                    <h6> Para mantener tu cuenta segura, cambia tu contraseña con regularidad.</h6>
                                    <img src="../img/contrasena.png" class="rounded mx-auto d-block" alt="...">
                                </div>
                                <?php
                            }
                            ?>
                            <div class="modal-footer justify-content-center">
                                <button type="button" class="btn btn-outline-primary px-4"
                                    data-bs-dismiss="modal">Entendido</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal informativo -->

                <div class="modal fade pt-5" id="change_password" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header text-center ">
                                <h5 class="modal-title " id="staticBackdropLabel">Cambiar contraseña</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center pt-2">
                                <h5>Escribe tu nueva contraseña</h5>
                                <form method="post" id="formulario" class="">
                                    <div class="row justify-content-center align-items-center">
                                        <div class="col-xl-10 col-lg-10 col-sm-8 form-group pt-2">
                                            <label for="pass1" class="">Contraseña</label>
                                            <input id="pass1" type="password" name="pass1" class="form-control">
                                        </div>
                                        <div class="row justify-content-center align-items-center pt-3">
                                            <div class="col-xl-10 col-lg-10 col-sm-8 form-group">
                                                <label for="pass2" class="">Repite tu contraseña</label>
                                                <input id="pass2" type="password" name="pass2" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="col-xl-10 col-lg-10 col-sm-8 form-group pt-3 pb-4">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Cerrar</button>
                                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                        </div>
                                </form>
                            </div>
                            <div class="modal-footer justify-content-center">

                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal informativo -->


                <!--Funcion de JS para borrar la alerta automaticamente  -->
                <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
                <!-- Alertas de edicion, borrado y error
              -->
                <script type="text/javascript">
                    $(document).ready(function () {
                        setTimeout(function () {
                            $(".alerta").fadeOut(1500);
                        }, 2500);
                    });
                </script>


                <?php
    }
    ?>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
                integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
                crossorigin="anonymous"></script>

</body>

</html>