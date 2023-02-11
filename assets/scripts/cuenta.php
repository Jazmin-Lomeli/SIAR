<?php
session_start();
// Revisar si no se ha logeado 
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
    $cambio_contra = $row['cambio_contraseña'];
}
if ($cambio_contra == NULL) {
    $cambio_contra = 0;
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
<?php
if(isset($_GET['mensaje']) and $_GET['mensaje'] == 'add'){
 ?>

<div class="d-flex align-items-end flex-column ">
        <div class="mt-auto p-2">
            <a class="btn" data-bs-toggle="modal" data-bs-target="#password">
                <img src="../img/pregunta.png" width="50px">
            </a>
        </div>

        <div class="container rounded mt-0">
            <div class="row justify-content-center">
                <div class="col-sm-11 col-md-12 col-lg-10 wrapper shadow pt-3 pb-4 ps-2">
                        
                </div>
            </div>
        </div>
    </div>
<?php    
}else{
?>
    <div class="d-flex align-items-end flex-column ">
        <div class="mt-auto p-2">
            <a class="btn" data-bs-toggle="modal" data-bs-target="#password">
                <img src="../img/pregunta.png" width="50px">
            </a>
        </div>

        <div class="container rounded mt-0">
            <div class="row justify-content-center">
                <div class="col-sm-11 col-md-12 col-lg-10 wrapper shadow pt-3 pb-4 ps-2">
                    <h3 class="text-center">Información de la cuenta</h3>
                    <?php
                    if ($cambio_contra == 0) {
                        ?>
                        <div class="row justify-content-center pt-2 px-5">
                            <div class="alerta alert alert-info alert-dismissible fade show text-center" role="alert">
                                <strong>Actualizá</strong> tu contraseña lo más pronto posible
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>

                        <?php
                    }
                    ?>
                    <div class="row px-2">
                        <div class="col-sm-3 col-md-3 col-lg-3 rounded ">
                            <img src="../img/user.png" class="img-thumbnail" alt="...">
                        </div>
                        <div class="col-sm-4 col-md-4 col-lg-5 pt-4 ">
                            <h5 class="lead">
                                <strong>Nombre de usuario: </strong>
                                <small class="text-muted">
                                    <?php echo $_SESSION["username"]; ?>
                                </small>
                            </h5>
                            <br>
                            <h5 class="lead">
                                <strong>Fecha de ingreso: </strong>
                                <small class="text-muted">
                                    <?php echo $f_ingreso; ?>
                                </small>
                            </h5>
                        </div>
                        <div class="col-sm-4 col-md-5 col-lg-4 pt-4 ">
                            <h5 class="lead">
                                <strong>Última sesión: </strong>
                                <small class="text-muted">
                                    <?php echo $ult_log; ?>
                                </small>
                            </h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-end flex-column " style="margin-top: -3em;">

                        <div class="mt-auto p-2">
                       
                            <a href="../../admin_reg.php" type="button"
                                class="btn btn-outline-primary btn-lg "><i class="bi bi-house-door-fill"></i></a>
              
                            <abbr title='Agregar un nuevo usuario'>
                                <a href="../../admin_reg.php" type="button"
                                class="btn btn-outline-secondary btn-lg mx-1"><i class="bi bi-person-plus-fill"></i></a>
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
                        <h6> Actualmente la cuenta tiene la contraseña por defecto, por seguridad cambiala lo más pronto
                            posible</h6>
                        <img src="../img/contrasena.png" class="rounded mx-auto d-block" alt="...">
                    </div>

                    <?php
                } else {
                    ?>
                    <div class="modal-body text-center pt-2">
                        <h5>Seguridad de la cuenta</h5>
                        <h6> Para mentener tu cuenta segura, cambia tu contraseña con regularidad.</h6>
                        <img src="../img/contrasena.png" class="rounded mx-auto d-block" alt="...">
                    </div>
                    <?php
                }
                ?>   
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal informativo -->


<?php
}
?>




    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>