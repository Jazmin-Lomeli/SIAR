<?php

session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../../login.php");
    // exit;
}

$nombre_usuario = $_SESSION['username'];
require '../config/config.php';

$sql_dato = "SELECT * FROM arduino";
$result = mysqli_query($link, $sql_dato);
while ($mostrar = mysqli_fetch_array($result)) {
    $estatus = $mostrar['finger_err'];
    ;
    $estado = $mostrar['finger_status'];

}

if ($estado == "ENROLL") {
    $estado = "Registrar huella";
} elseif ($estado == "REGISTER") {
    $estado = "Esperando huella";
}

if ($estatus == "Todo_bien") {
    $estatus = "Conectado";
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
                                <li><a class="dropdown-item" href="#"> &nbsp; Cuenta &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp;
                                        <i class="bi bi-person-circle"></i> </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item " href="#">&nbsp; Sistema &nbsp; &nbsp; &nbsp;
                                        &nbsp; &nbsp; &nbsp; &nbsp;
                                        <i class="bi bi-gear"></i></a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item " href="logout.php">&nbsp; Salir &nbsp; &nbsp; &nbsp;
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



    <div class="d-flex align-items-end flex-column">
        
        <div class="container rounded mt-5">
            <div class="row justify-content-center pt-4">
                <div class="col-sm-11 col-md-12 col-lg-10 wrapper shadow pb-4 ps-2 pt-2">
                    <h3 class="text-center pb-3 px-3 pt-2">Información del sistema</h3>

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

                    <div class="row mx-3 mb-5 mt-2 justify-content-center">
                        <div class="col-sm-10 col-lg-11 co-xl-11">
                            <h5 class="text-start pb-3 px-3">Resumen del estado del sistema</h5>

                            <div class="table-responsive text-center">
                                <table class="table table table-bordered table-hover border border-secondary">
                                    <thead>
                                        <tr>
                                            <th>Estatus</th>
                                            <th>Estado</th>
                                            <th>Lector de huella</th>
                                            <th>Display</th>

                                        </tr>
                                    </thead>
                                    <tbody class="fw-normal">
                                        <th>
                                            <?php echo $estatus ?>
                                        </th>
                                        <th>
                                            <?php echo $estado ?>
                                        </th>
                                        <th>
                                            <?php //echo $estado ?>
                                        </th>
                                        <th>
                                            <?php //echo $estado ?>
                                        </th>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-end flex-column " style="margin-top: -3em;">
                        
                            <div class="mt-auto p-2 px-3">


                                <div class="btn-group">
                                
                                    <abbr title='Cambiar estado del sistema'>
                                        <a  data-bs-toggle="modal" data-bs-target="#password" type="button"
                                            class="btn btn-outline-primary btn-lg mx-1"><i class="bi bi-wrench-adjustable"></i></a>
                                    </abbr>
                                </div>
                            </div>
                


                    </div>
                </div>

            </div>
        </div>
    </div>
 <!-- Modal -->
 <div class="modal fade pt-5" id="password" data-bs-backdrop="static" tabindex="-1"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title " id="exampleModalLabel"> Cambiar estado del sistema</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center pt-3 pb-3">
        <h5> El estado actual del sistema es </h5>
        <h5 class="fst-italic"> <?php echo $estado; ?></h5>


          <form method="post" id="formulario" class="pt-3 pb-1">
            <div class="row justify-content-center">

              <div class="col-xl-10 col-lg-10 col-10 form-group text-center pb-4">
                <label for="fecha">Cambiar estado del sistema</label>
                <select name="area" class="form-control text-center">
                    <option value="ENROLL">Registrar una huella</option>
                    <option value="REGISTER">Esperar una huella</option>
                    <option value="DELETE">Borrar una huella</option>
                 </select>
              
                </div>
                
                <div class="row border"></div>
                
                <div class="pt-3">
                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal"> Cancelar </button>
                    <button type="submit" class="btn btn-primary"> Guardar </button>
                    </div>
 
             </div>
            
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->   


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




    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>

</body>

</html>