<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <title><?php echo $title; ?> - Jakmania Purwokerto App</title>
  <link href="<?php echo base_url().'assets/vendor/fontawesome-free/css/all.min.css'?>" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
  <link href="<?php echo base_url().'assets/css/sb-admin-2.css'?>" rel="stylesheet">
  <link href="<?php echo base_url().'assets/vendor/datatables/dataTables.bootstrap4.min.css'?>" rel="stylesheet">
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
</head>
<body id="page-top">
  <div id="wrapper">
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url('beranda/view'); ?>">
        <img class="logo-default" src="<?php echo base_url('assets/img/'.$this->logo_app);?>">
      </a>
      <li class="nav-item active">
        <a class="nav-link" href="<?php echo base_url('beranda/view'); ?>"><i class="fas fa-fw fa-tachometer-alt"></i><span>Beranda</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo"><i class="fas fa-fw fa-desktop"></i><span>Sistem</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="py-2 collapse-inner rounded">
            <a class="nav-link" href="<?php echo base_url('backup/view'); ?>">Backup</a>
            <a class="nav-link" href="<?php echo base_url('pengguna/view'); ?>">Pengguna</a>
            <a class="nav-link" href="<?php echo base_url('personalisasi/view'); ?>">Personalisasi</a>
          </div>
        </div>
      </li>
    </ul>
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-fw"></i>
                <span class="badge badge-danger badge-counter">3+</span>
              </a>
              <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                <h6 class="dropdown-header">
                  Notifikasi
                </h6>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-primary">
                      <i class="fas fa-file-alt text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 12, 2019</div>
                    <span class="font-weight-bold">A new monthly report is ready to download!</span>
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-success">
                      <i class="fas fa-donate text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 7, 2019</div>
                    $290.29 has been deposited into your account!
                  </div>
                </a>
                <a class="dropdown-item d-flex align-items-center" href="#">
                  <div class="mr-3">
                    <div class="icon-circle bg-warning">
                      <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                  </div>
                  <div>
                    <div class="small text-gray-500">December 2, 2019</div>
                    Spending Alert: We've noticed unusually high spending for your account.
                  </div>
                </a>
              </div>
            </li>
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="<?php echo $this->foto_profil ?>">
                <span class="ml-2 d-none d-lg-inline text-gray-600 small"><?php echo $this->realname;?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="<?php echo $this->url_profil ?>"><i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>Profil Saya</a>
                <a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>Pengaturan</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Keluar</a>
              </div>
            </li>
          </ul>
        </nav>
        <div class="container-fluid">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800"><?php echo $title; ?></h1>
          </div>
          <?php
          if ($this->session->userdata('isLogin') == TRUE) {
            echo $content;
          }
          ?>
        </div>
      </div>
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright my-auto">
            <span>Copyright &copy; <?php echo $this->tahun_pembuatan?> <?php echo $this->hak_cipta?></span>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
        </div>
      </div>
    </div>
  </div>
  <script src="<?php echo base_url().'assets/vendor/jquery/jquery.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/jquery-easing/jquery.easing.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/js/sb-admin-2.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/chart.js/Chart.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/datatables/jquery.dataTables.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/datatables/dataTables.bootstrap4.min.js'?>"></script>
  <script>
    $.fn.dataTable.defaults.language = {
      "lengthMenu": "Menampilkan _MENU_ data per halaman",
      "zeroRecords": "Maaf, data kosong",
      "info": "Menampilkan halaman _START_ - _END_ dari _TOTAL_ data",
      "infoEmpty": "Data tidak ditemukan",
      "infoFiltered": "(difilter dari _MAX_ total data)",
      "search": "Cari Data",
      "paginate": {
        "next": "Selanjutnya",
        "previous": "Sebelumnya",
        "first": "Awal",
        "last": "Akhir"
      }
    };
  </script>
</body>
</html>
