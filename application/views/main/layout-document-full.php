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
  <link href="<?php echo base_url().'assets/vendor/datatables/dataTables.bootstrap4.min.css'?>" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo base_url().'assets/vendor/wizard/css/datepicker.min.css'?>">
  <script src="<?php echo base_url().'assets/vendor/jquery/jquery.min.js'?>"></script>
</head>
<body id="page-top">
  <div id="wrapper">
    <ul class="navbar-nav sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo base_url('beranda'); ?>">
        <img class="logo-default" src="<?php echo base_url('assets/img/logo.png');?>">
      </a>
      <li class="nav-item <?=$this->uri->segment(1) == 'beranda' ? 'active' : ''?>">
        <a class="nav-link" href="<?php echo base_url('beranda'); ?>"><i class="fas fa-fw fa-home"></i><span>Beranda</span></a>
      </li>
      <li class="nav-item <?=$this->uri->segment(1) == 'anggota' ? 'active' : ''?>">
        <a class="nav-link" href="<?php echo base_url('anggota'); ?>"><i class="fas fa-fw fa-users"></i><span>Keanggotaan</span></a>
      </li>
      <li class="nav-item <?=$this->uri->segment(1) == 'kas' ? 'active' : ''?>">
        <a class="nav-link" href="<?php echo base_url('kas'); ?>"><i class="fas fa-fw fa-money-bill-wave"></i><span>Kas Umum</span></a>
      </li>
      <li class="nav-item <?=$this->uri->segment(1) == 'wilayah' || $this->uri->segment(1) == 'token' || $this->uri->segment(1) == 'pengguna' ? 'active' : ''?>">
        <a class="nav-link <?=$this->uri->segment(1) == 'wilayah' || $this->uri->segment(1) == 'token' || $this->uri->segment(1) == 'pengguna' ? '' : 'collapsed'?>" href="#" data-toggle="collapse" data-target="#pengaturan"><i class="fas fa-fw fa-cog"></i><span>Pengaturan</span>
        </a>
        <div id="pengaturan" class="collapse <?=$this->uri->segment(1) == 'wilayah' || $this->uri->segment(1) == 'token' || $this->uri->segment(1) == 'pengguna' ? 'show' : ''?>">
          <div class="py-2 collapse-inner rounded">
            <a class="collapse-item <?=$this->uri->segment(1) == 'pengguna' ? 'active' : ''?>" href="<?php echo base_url('pengguna'); ?>">Pengguna</a>
            <a class="collapse-item <?=$this->uri->segment(1) == 'token' ? 'active' : ''?>" href="<?php echo base_url('token'); ?>">Token</a>
            <a class="collapse-item <?=$this->uri->segment(1) == 'wilayah' ? 'active' : ''?>" href="<?php echo base_url('wilayah'); ?>">Wilayah</a>
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
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                <?php if($this->foto != NULL) {?>
                <img class="img-profile rounded-circle" src="<?php echo $this->foto ?>">
                <?php }else{ ?>
                <img class="img-profile rounded-circle" src="<?php echo $this->foto_profil ?>">
                <?php } ?>
                <span class="ml-2 d-none d-lg-inline text-gray-600 small"><?php echo $this->realname;?></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                <a class="dropdown-item" href="<?php echo site_url('login/logout');?>"><i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>Keluar</a>
              </div>
            </li>
          </ul>
        </nav>
        <div class="container-fluid">
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
            <span>Hak Cipta &copy; 2018 oleh <a href="https://razaqultegar.com" target="_blank">Razaqul Tegar</a>.</span>
          </div>
        </div>
      </footer>
    </div>
  </div>
  <script src="<?php echo base_url().'assets/vendor/bootstrap/js/bootstrap.bundle.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/jquery-easing/jquery.easing.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/js/sb-admin-2.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/wizard/js/datepicker.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/wizard/js/datepicker-id.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/datatables/jquery.dataTables.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/datatables/dataTables.bootstrap4.min.js'?>"></script>
  <script src="<?php echo base_url().'assets/vendor/highcharts/code/highcharts.js';?>"></script>
  <script src="<?php echo base_url().'assets/vendor/highcharts/code/highcharts-3d.js';?>"></script>
  <script src="<?php echo base_url('assets/vendor/highcharts/code/modules/stock.js');?>"></script>
  <script src="<?php echo base_url().'assets/vendor/highcharts/code/modules/exporting.js';?>"></script>
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

    // Date Picker
    var dp1 = $('#dp1').datepicker().data('datepicker');

    function random_all() {
        var campur = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz!@#$%^&*()_+{};:'/?.<>,/";
        var panjang = 15;
        var random_all = '';
        for (var i=0; i<panjang; i++) {
            var hasil = Math.floor(Math.random() * campur.length);
            random_all += campur.substring(hasil,hasil+1);
        }
		  document.tokenform.token.value = random_all;
		}
  </script>
</body>
</html>
