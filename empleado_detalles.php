<?php
// Initialize the session
session_start();
// Revisar si no se ha logeado 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
  header("location: login.php");
  // exit;
}

require_once 'assets/config/config.php';
// require_once 'assets/config/functions.php';

$conexion = $link;
if (!$conexion) {
  header('Location: login.php');
}

$id_emp = $_GET['id'];

$name = $tel = $fecha_r = $id_huella = $area = $id_huella = $f_reg = $status_huella = $area = "";

$sql_dato = "SELECT * FROM empleados LEFT JOIN tipo_empleado ON empleados.tipo=tipo_empleado.tipo  LEFT JOIN huella ON huella.id_emp=empleados.id Where empleados.id = '$id_emp'";
$result = mysqli_query($conexion, $sql_dato);
while($mostrar = mysqli_fetch_array($result)) {
  $name = $mostrar['nombre'] ." ". $mostrar['apellido'] ." ". $mostrar['seg_apellido'] ;
  $tel = $mostrar['telefono'];
  $status_huella = $mostrar['huella'];
  $f_reg = $mostrar['f_registro'];
  $area = $mostrar['t_nombre'];
  $id_huella  = $mostrar['id_huella'];
}

if($status_huella == 0){
  $id_huella = "- -";
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Detalles</title>
  <!-- CSS only -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
  <link rel="stylesheet" href="./assets/css/styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css">
<body>
    <!-- NAV BAR -->
<header>
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
              <a class="nav-link active" href="admin_users.php" tabindex="-1" aria-disabled="true">Usuarios</a>
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
   <!-- NAV BAR -->
   
  <div class="px-4 pt-3 bienvenida">
    <div class="row">
      <div class="col align-self-start">
      <h4>Bienvenido, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>.</h4>
      </div>
      <div class="col align-self-center"></div>
      <div class="col align-self-end d-flex flex-row-reverse pe-5">
        <?php
          $mes = array("enero", "febrero", "marzo", "abril", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "noviembre", "diciembre");
          $dia = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
          ?>
        <p class="d-flex">
          <?php
            /* Establecer la hora de Mexico por que por defecto manda la del server  */
            date_default_timezone_set("America/Mexico_City");
            echo $dia[date('w')] . " " . date("d") . " de " . $mes[date("m") - 1] . " de " . date("Y") . ".   " . date("h:i:sa"); ?>
        </p>
      </div>
    </div>
  </div>
<!-- Alertas de confirmacion o  error -->
  <div class="container mt-2 principal rounded-3 shadow mb-4">
    <h3 style="text-align: center; padding-top: 1rem;">Detalles del empleado </h3>


     <!-- Barra de buscar -->
    <div class="pt-2 pb-3">
      <div class="row">
        <div class="row align-items-end">
          <div class="col"> </div>
          
          <div class="col"> </div>
          <div class="col align-self-end d-flex flex-row-reverse">

            <div class="ps-2">
              <abbr title='Eliminar registro'>
                <a class="btn btn-outline-danger ml-2" href="eliminar_emp.php"><i class="bi bi-trash3-fill">
                   </i></a>
                </abbr>
              </div>

            <div class="ps-2">
              <abbr title='Editar registro '>
                  <a class="btn btn-outline-secondary ml-2" href="editar_emp.php?id=<?php echo $id_emp ?>"><i class="bi bi-pencil-square">
                      </i></a>
                </abbr>
            </div>

                <?php 
              if($status_huella == 0){
                ?>
                 <div class="ps-2">
                    <abbr title='Registrar huella'>
                        <a class="btn btn-outline-primary ml-2" href="assets/scripts/add_huella.php?id_add=<?php echo $id_emp ?>"><i class="bi bi-fingerprint">
                            </i></a>
                      </abbr>
                  </div>
                <?php 
              }
            ?>

          </div>
        </div>
      </div>

      <div class="container px-4">
        <div class="row">
         <div class="heading-layout1">


      <div class="container pt-3">
        <div class="row align-items-start">
          <div class="container">
            <div class="row pb-3">
              <div class="col-4">
                Nombre: <?php echo  $name  ?>
              </div>
              <div class="col-4">
                Teléfono:  <?php echo $tel ?>
              </div>
              <div class="col-4">
              </div>
            </div>
            <div class="row pb-3">
              <div class="col-4">
                Área laboral:  <?php echo  $area ?>
              </div>
              <div class="col-4">
                Fecha de registro:  <?php echo $f_reg ?>
              </div>
              <div class="col-2">
                ID de empleado:  <?php echo $id_emp ?>
              </div>
              <div class="col-2">
                ID de huella: <?php echo $id_huella ?>
              </div>
            </div>

          </div>
           
        </div>
      </div>
      









      <div class="row pb-2">
        <h6 style="text-align: left; padding-top: 1rem;">Historial de asistencias </h6>
        <div class="col align-self-center">
        </div>
        <div class="col align-self-end d-flex flex-row-reverse ">
          <form class="d-flex col-md-4" role="search" action="" method="post">
            <input class="form-control me-2 light-table-filter" type="search" placeholder="Buscar" aria-label="Buscar"
              name="campo" id="campo">
          </form>
        </div>
      </div>
    </div>


    <div class="table-responsive text-center">
      <table class="table table table-bordered table-hover border border-secondary">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>ID de huella</th>
            <th>Opciones</th>
          </tr>
        </thead>
        <tbody >

        </tbody>
      </table>
    </div>
  </div>


  <!--Funcion de JS para buscar en tiempo real  -->
  <script>
    /* Llamando a la función getData() */
    getData()
    /* Escuchar un evento keyup en el campo de entrada y luego llamar a la función getData. */
    document.getElementById("campo").addEventListener("keyup", getData)
    /* Peticion AJAX */
    function getData() {
      let input = document.getElementById("campo").value
      let content = document.getElementById("content")
      let url = "./assets/scripts/ajax_reg.php"
      let formaData = new FormData()
      formaData.append('campo', input)
      fetch(url, {
        method: "POST",
        body: formaData
      }).then(response => response.json())
        .then(data => {
          content.innerHTML = data
        }).catch(err => console.log(err))
    }
  </script>

 <!--Funcion de JS para borrar la alerta automaticamente  -->
  <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
  <!-- Alertas de edicion, borrado y error
  -->
  <script type="text/javascript">
    $(document).ready(function () {
      setTimeout(function () {
        $(".alerta_error").fadeOut(1500);
      }, 2500);
    });

    $(document).ready(function () {
      setTimeout(function () {
        $(".alerta_edit").fadeOut(1500);
      }, 2500);
    });

    $(document).ready(function () {
      setTimeout(function () {
        $(".alerta_delete").fadeOut(1500);
      }, 2500);
    });
  </script>


  <!-- Bootstrap core JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
    crossorigin="anonymous"></script>
</body>

</html>