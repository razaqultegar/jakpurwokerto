<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title><?php echo $title; ?> - The Jakmania App</title>
  <link href="<?php echo base_url().'assets/vendor/fontawesome-free/css/all.min.css'?>" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="<?php echo base_url().'assets/css/sb-admin-2.css'?>" rel="stylesheet">
  <link href="<?php echo base_url().'assets/vendor/toastr/toastr.min.css'?>" rel="stylesheet">
  <style>
    .logo-default {
      max-width: 70%;
      height: auto !important;
      margin-bottom: 30px;
    }
  </style>
</head>
<body class="bg-login">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-xl-6 col-lg-6 col-md-6">
        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <div class="row">
              <div class="col-lg-12">
                <div class="p-5">
                  <div class="text-center">
                    <img class="logo-default" src="<?php echo base_url('assets/img/logo.png'); ?>">
                  </div>
                  <form method="POST" action="<?php echo site_url('login/validate_login'); ?>" class="user" autocomplete="off">
                    <div class="form-group">
                      <input type="text" class="form-control form-control-user" id="username" name="username" placeholder="Nama Pengguna...">
                    </div>
                    <div class="form-group">
                      <input type="password" class="form-control form-control-user" id="password" name="password" placeholder="Kata Sandi...">
                    </div>
                    <button type="submit" class="btn btn-danger btn-user btn-block">Masuk</button>
                    <hr>
                    <div class="text-center">
                      <p>Hak Cipta &copy; 2018 oleh <a href="https://limbodigital" target="_blank">Limbo Digital</a>.</p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url().'assets/vendor/jquery/jquery.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/jquery-easing/jquery.easing.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/js/sb-admin-2.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/toastr/toastr.min.js'?>"></script>
  <?php if($msg[0] == '1'){ ?>
    <script>
      $(document).ready(function(){
        toastr.options = {
          "closeButton": true,
          "progressBar": true,
          "positionClass": "toast-top-right",
          "showDuration": "3000",
          "hideDuration": "3000",
          "timeOut": "3000",
          "extendedTimeOut": "3000"
        }
        toastr['<?php echo $msg[1];?>']('<?php echo $msg[2];?>')
        <?php unset($_SESSION['pesan']); echo $msg[3];?>
      });
    </script>
  <?php } ?>
</body>
</html>
