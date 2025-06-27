<?php
require_once "vistas/db.php";
//define('DB_NAME', 'corpo_deimos');  
require_once "vistas/security.php";
require_once "classes/Login.php";
$login = new Login();
if ($login->isUserLoggedIn() == true) {
    header("location: vistas/html/principal.php");
} else {
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="">
  <title>
   Sistema de Ventas
  </title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <link href="assets/css/nucleo-svg.css" rel="stylesheet" />
  <link id="pagestyle" href="assets/css/soft-ui-dashboard.css?v=1.0.6" rel="stylesheet" />
</head>
<body class="">
  <div class="container position-sticky z-index-sticky top-0">
    <div class="row">
      <div class="col-12">
      </div>
    </div>
  </div>
  <main class="main-content  mt-0">
    <section>
      <div class="page-header min-vh-75">
        <div class="container">
          <div class="row">
            <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column mx-auto">
              <div class="card card-plain mt-6">
                <div class="card-header pb-0 text-left bg-transparent">
                  <img src="assets/img/sign-in/a.jpg" style="display:block; margin-left:auto; margin-right:auto; margin-bottom: 1em;" width="100%">
                  <h3 class="font-weight-bolder text-info text-gradient">Bienvenido</h3>               
                </div>
                <div class="card-body">
                  <form role="form" method="post" accept-charset="utf-8" action="login.php" name="loginform">
                  <?php
                            if (isset($login)) {
                                if ($login->errors) 
                                {
                                    ?>
                                    <div class="alert alert-danger alert-dismissible" role="alert">
                                      <strong>Error!</strong>
                                    <?php
                                      foreach ($login->errors as $error) {
                                      echo $error;
                                      }
                                    ?>
                                    </div>
                                <?php
                                  }
                                    if ($login->messages) 
                                    {
                                ?>
                                      <div class="alert alert-success alert-dismissible" role="alert">
                                        <strong>Aviso!</strong>
                                      <?php
                                        foreach ($login->messages as $message) 
                                        {
                                        echo $message;
                                        }
                                      ?>
                                      </div>
                                    <?php
                                    }
                                  }
                            ?>

                    <label>NIT</label>
                    <div class="mb-3">
                      <input type="text" class="form-control" placeholder="Nit" aria-label="Nit" name="nit_users" required="">
                    </div>
                    <label>Usuario</label>
                    <div class="mb-3">
                      <input type="text" class="form-control" placeholder="Usuario" aria-label="Usuario" name="usuario_users" required="">
                    </div>
                    <label>Contraseña</label>
                    <div class="mb-3">
                      <input type="password" class="form-control" placeholder="Contraseña" aria-label="Contraseña" aria-describedby="password-addon" type="password" name="con_users" required="" autocomplete="off">
                    </div>
                    <div class="text-center">
                      <button  class="btn bg-gradient-info w-100 mt-4 mb-0"  type="submit" name="login" id="submit" >INGRESAR</button>
                    </div>
                  </form>
                </div>
                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div style="margin: 0;position: absolute; top: 50%; transform: translateY(-50%);"> 
                <!--<image name="imgc" id="imgc" alt="centered" style="width: 100%; background-repeat: no-repeat; background-position: 50%;border-radius: 10%;background-size: 50% auto;" src="https://corpo-sistemas.com/splash.gif"/>-->
                <script>
                  function isMobile(){
			return (
				(navigator.userAgent.match(/Android/i)) ||
				(navigator.userAgent.match(/webOS/i)) ||
				(navigator.userAgent.match(/iPhone/i)) ||
				(navigator.userAgent.match(/iPod/i)) ||
				(navigator.userAgent.match(/iPad/i)) ||
				(navigator.userAgent.match(/BlackBerry/i))
			);
		}
    if ('Android'==isMobile())
				{	
          document.getElementById('imgc').style.visibility="hidden";
        }
  </script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <footer class="footer py-5">
    <div class="container">
      <div class="row">
      </div>
      <div class="row">
        <div class="col-8 mx-auto text-center mt-1">
          <p class="mb-0 text-secondary">
            Copyright © <script>
              document.write(new Date().getFullYear())
            </script> Corposistemas.
          </p>
        </div>
      </div>
    </div>
  </footer>
  <script src="assets/js/core/popper.min.js"></script>
  <script src="assets/js/core/bootstrap.min.js"></script>
  <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="assets/js/soft-ui-dashboard.min.js?v=1.0.6"></script>
</body>
</html>
<?php
}
?>
