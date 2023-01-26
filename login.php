<?php

require_once "assets/config/config.php";

// Inicializa una sesion
session_start();

//  Revisa si esta logeado te mada a welcome
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
}

// Define las variables para los inputs
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Cuando se presiona el boton 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Checa si el username esta vacio
    if (empty(trim($_POST["username"]))) {    // si esta vacio  manda error
        $username_err = "Porfavor ingrese su nombre de usuario.";
    } else {
        $username = trim($_POST["username"]);       // si es correcto guardarlo en username 
    }
    // Checa si la contraseña esta vacia
    if (empty(trim($_POST["password"]))) {           // checa que la contraseña no este vacia 
        $password_err = "Porfavor ingrese su contraseña";
    } else {
        $password = trim($_POST["password"]);       // si es correcta la guarda en password 
    }

    // Validamos que no exista error
    if (empty($username_err) && empty($password_err)) {           // si ambos casos son correctos
        // Prepara la consulta
        $sql = "SELECT username, password FROM users WHERE username = ?";    // consulta que si exista usuario y la contaseña coincida 

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Asigna el nombre de usuario
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Checa si el usuario existe
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $username, $hashed_password);
                    if (mysqli_stmt_fetch($stmt)) {
                        
                        if (password_verify($password, $hashed_password)) {

                            // Password is correct, so start a new session
                            session_start();

                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                             $_SESSION["username"] = $username;

                            // Redirect user to welcome page
                           
                            header("location: welcome.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $login_err = "Invalid username or password.";    // aquiii
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $login_err = "Contraseña o usuario invalidos.";
                }
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

<!-- Parte visual de la pagina web -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/login.css">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
    <style>
         body{ font: 14px sans-serif; background: rgb(247, 245, 245);  overflow: hidden;}
        
        .botnones{ display: flex;  align-items: center;  justify-content: center;}   
        
    </style>

</head>
 
<body>
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
 
                                    <input type="submit" class="btn btn-outline-primary ps-4 px-4 mx-2" value="Ingresar">
                                    &ensp;
                                    <a class="btn btn-outline-secondary ps-4 px-4 " href="login.php" ><i class="bi bi-x-circle"></i> Limpiar</a> 

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

    <!-- <div class="footer text-center">
        <small class="text-muted">Footer</small>
    </div> -->

</body>

</html>