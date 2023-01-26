<?php
// Inicializar la sesion
session_start();

// Revisar si no se ha logeado 
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

require_once "assets/config/config.php";


// Inicializamos las variables para guardar los datos 
$username = $password = $name = $last_name = $last_name2 = $confirm_password = $tel = "";
$username_err = $password_err = $name_err = $last1_err = $last2_err = $confirm_password_err = $tel_err = "";

/* Cuando se le da clic a el bonton de crear */
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validar Nombre
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
    // Validar segundo apellido
    if (empty(trim($_POST["ape2"]))) {
        $last2_err = "Por favor ingresa un apellido.";
    } elseif (!preg_match('/^[ a-zA-ZáéíóúñÑÁÉÍÓÚ]+$/', trim($_POST["ape2"]))) { // Letras mayusculas y min
        $last2_err = " El apellido solo puede contener letras.";
    } else {
        $param_last2 = trim($_POST["ape2"]);
        $last_name2 = $param_last2;
    }
    // Validar telefono apellido
    if (empty(trim($_POST["tel"]))) {
        $tel_err = "Por favor ingresa un telefono.";
    } elseif (!preg_match('/^[0-9]+$/', trim($_POST["tel"])) && strlen(trim($_POST["tel"])) != 10) {
        $tel_err = " El telefono solo puede contener números.";
    } else {
        $param_tel = trim($_POST["tel"]);
        $tel = $param_tel;
    }
    // Validar username, el nombre se usuario no se puede repetir 
    if (empty(trim($_POST["username"]))) {
        $username_err = "Ingresa un Usuario.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))) {
        $username_err = "Solo puede contener letras, numeros y guión bajo.";
    } else {
        // Hacermos la consulta 
        $sql = "SELECT id FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                /* store result */
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $username_err = "Nombre de Usuario no disponible.";
                } else {
                    $username = trim($_POST["username"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Validar password Y aplicar el HASING MD5
    if (empty(trim($_POST["password"]))) {
        $password_err = "Ingresa una Contraseña.";
    } elseif (strlen(trim($_POST["password"])) < 6) { // contraseña mayor a 6 caracteres 
        $password_err = "La Contaseña debe tner minimo 6 caracteres.";
    } else {
        $password = trim($_POST["password"]);
    }
    // Confirmamos la contraseña 
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Confirma tu Contraseña.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "La Contaseña no Coincide."; // Las contraseñas no coinciden 
        }
    }
    /* Si no hay error en los datos procedemos a hacer la insercion */
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($name_err) && empty($last1_err) && empty($last2_err) && empty($tel_err)) {

        $sql = "INSERT INTO users (name, last_name, last_name2, telefono, username, password, f_ingreso) VALUES (?,?,?,?,?,?,?)";

        $fecha = date('Y-m-d');
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssss", $param_name, $param_last1, $last_name2, $param_tel, $param_username, $param_password, $fecha);
            // Establecemos los datos en los Inputs
            $param_name = $name;
            $param_last1 = $last_name;
            $param_last2 = $last_name2;
            $param_username = $username;
            $param_tel = $tel;
            // Hasing para la contaseña
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redireccionamos al login 
                header("location: login.php");
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    // Close connection
    mysqli_close($link);
}
?>
<!-- Parte visble de la pagina web -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./assets/css/styles.css">
    <link rel="stylesheet" href="./assets/css/root.css">

</head>


<body>
<!--  Nav Bar -->
<header >
      <nav class="navbar navbar-expand-lg navbar-light 5 shadow " >
        <div class="container-fluid dernav">
          <a class="navbar-brand"> 
            <img src="./assets/img/logo.png" width="130" height="45" alt="">   <!-- Logo -->
          </a>
               
          </div>
        </div>
      </nav>
    </header>
 <!--  Nav Bar -->

    <div class="container">
        <div class="row justify-content-center my-4">
            <div class="row justify-content-center justify-content-md-center">
                <div class="card shadow text-center my-2" style="width: 45rem;">
                    <div class="card-body pt-2">
                        <form method="post" id="formulario">
                            <h2>Registro</h2>
                            <div class="row">
                                <div class="col-sm-12 text-start form-group">
                                    <label for="nombre" class="">Nombre</label>
                                    <input id="nombre" type="text" name="nombre"
                                        class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $name; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $name_err; ?>
                                    </span>
                                </div>
                                <div class="col-sm-6 text-start form-group">
                                    <label for="ape1">Apellido Paterno</label>
                                    <input id="ape1" type="text" name="ape1"
                                        class="form-control <?php echo (!empty($last1_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $last_name; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $last1_err; ?>
                                    </span>
                                </div>
                                <div class="col-sm-6 text-start form-group">
                                    <label for="ape2">Apellido Materno</label>
                                    <input id="ape2" type="text" name="ape2"
                                        class="form-control <?php echo (!empty($last2_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $last_name2; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $last2_err; ?>
                                    </span>
                                </div>
                                <div class="col-sm-12 text-start form-group">
                                    <label for="tel">Teléfono</label>
                                    <input id="tel" type="text" name="tel"
                                        class="form-control <?php echo (!empty($tel_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $tel; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $tel_err; ?>
                                    </span>
                                </div>

                                <div class="col-sm-12 text-start form-group">

                                    <label>Username</label>
                                    <input type="text" name="username"
                                        class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $username; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $username_err; ?>
                                    </span>
                                </div>
                                <div class="col-sm-6 text-start form-group">
                                    <label>Contraseña</label>
                                    <input type="password" name="password"
                                        class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $password; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $password_err; ?>
                                    </span>
                                </div>
                                <div class="col-sm-6 text-start form-group">
                                    <label>Repite tu contraseña</label>
                                    <input type="password" name="confirm_password"
                                        class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $confirm_password; ?>">
                                    <span class="invalid-feedback">
                                        <?php echo $confirm_password_err; ?>
                                    </span>
                                </div>

                                <div class="form-group col-md-12 botnones pt-1">
                                    <input type="submit" class="btn btn-outline-success ps-4 px-4"
                                        value="&nbsp;&nbsp; Crear &nbsp;&nbsp;">
                                    <a class="btn btn-outline-danger ps-4 px-4" href="login.php"><i
                                            class="bi bi-box-arrow-right p-1"></i>Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
        crossorigin="anonymous"></script>

    <!-- <div class="footer text-center">
        <small class="text-muted">Footer</small>
    </div> -->

</body>

</html>