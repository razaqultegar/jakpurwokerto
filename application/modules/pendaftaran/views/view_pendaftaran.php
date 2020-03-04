<?php
if ($this->session->userdata('isToken') == TRUE) {
  ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?> - The Jakmania App</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="colorlib.com">
    <link rel="stylesheet" href="<?php echo base_url().'assets/vendor/wizard/fonts/material-design-iconic-font/css/material-design-iconic-font.css'?>">
		<link rel="stylesheet" href="<?php echo base_url().'assets/vendor/wizard/css/datepicker.min.css'?>">
		<link rel="stylesheet" href="<?php echo base_url().'assets/vendor/wizard/css/style.css'?>">
    <link href="<?php echo base_url().'assets/vendor/toastr/toastr.min.css'?>" rel="stylesheet">
	</head>
	<body>
		<div class="wrapper">
      <form id="wizard" method="post" action="<?php echo site_url('pendaftaran/addAction');?>" enctype="multipart/form-data" autocomplete="off">
        <h4></h4>
        <section>
          <h3>Formulir Pendaftaran</h3>
          <div class="form-row">
            <div class="form-col">
              <label>Koordinator Wilayah <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtIdWilayah" class="form-control">
                  <option value="">-Pilih Wilayah-</option>
                  <?php
                    if (!empty($list_wilayah)){
                      foreach ($list_wilayah as $key => $value) {
                        echo '<option value="'.$value->wilIdWilayah.'">'.$value->wilNama.'</option>';
                      }
                    }
                  ?>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
            <div class="form-col">
              <label>Foto Pas (2x3cm) <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtFoto" type="file" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>Nama Lengkap <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtNama" type="text" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Nama Panggilan <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtNmPendek" type="text" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>Jenis Kelamin <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtJnsKelamin" class="form-control">
                  <option value="">-Pilih Jenis Kelamin-</option>
                  <?php
                    if (!empty($list_jenis_kelamin)){
                      foreach ($list_jenis_kelamin as $key => $value) {
                        echo '<option value="'.$key.'">'.$value.'</option>';
                      }
                    }
                  ?>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
            <div class="form-col">
              <label>Tempat Lahir <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtTmptLahir" type="text" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Tanggal Lahir <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtTglLahir" type="text" class="form-control datepicker-here" data-language="id" data-date-format="dd/mm/yyyy" id="dp1">
              </div>
            </div>
          </div>
        </section>
        <h4></h4>
        <section>
          <h3>Formulir Pendaftaran</h3>
          <div class="form-row">
            <div class="form-col">
              <label>Pendidikan Terakhir <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtIdPendidikan" class="form-control">
                  <option value="">-Pilih Pendidikan Terakhir-</option>
                  <?php
                    if (!empty($list_pendidikan)){
                      foreach ($list_pendidikan as $key => $value) {
                        echo '<option value="'.$value->dikIdPendidikan.'">'.$value->dikPendidikan.'</option>';
                      }
                    }
                  ?>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
            <div class="form-col">
              <label>Pekerjaan <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtIdPekerjaan" class="form-control">
                  <option value="">-Pilih Pekerjaan-</option>
                  <?php
                    if (!empty($list_pekerjaan)){
                      foreach ($list_pekerjaan as $key => $value) {
                        echo '<option value="'.$value->pkjIdPekerjaan.'">'.$value->pkjNama.'</option>';
                      }
                    }
                  ?>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col" style="width:100%;">
              <label>Alamat Lengkap <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtAlamatJalan" type="text" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>Kelurahan</label>
              <div class="form-holder">
                <input name="agtKelurahan" type="text" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Kecamatan <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtKecamatan" type="text" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Kode Pos <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtKdPos" type="number" class="form-control">
              </div>
            </div>
          </div>
        </section>
        <h4></h4>
        <section>
          <h3>Formulir Pendaftaran</h3>
          <div class="form-row">
            <div class="form-col">
              <label>No. Telp/HP <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtNoTelp" type="number" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Alamat Email <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtEmail" type="email" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Ukuran Kaos <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtUkrnKaos" type="text" class="form-control" style="text-transform:uppercase;">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col" style="width:100%;">
              <div style="height:130px;overflow:auto;border:1px solid #ccc;padding:10px;">
                <p>Menyatakan bahwa saya bersedia menjadi ANGGOTA THE JAKMANIA dengan ketentuan-ketentuan sebagai berikut :</p>
                <ol>
                  <li>Menjaga nama baik THE JAKMANIA.</li>
                  <li>Mentaati Anggaran Dasar dan Anggoran Rumah Tangga The Jakmania serta peraturan-peraturan lainnya yang dibuat oleh pengurus dan tidak bertentangan dengan AD/ART Jakmania.</li>
                  <li>Menghormati pengurus dan sesame anggota Jakmania.</li>
                  <li>Mencegah dan menanggulangi setiap usaha atau tindakan yang merugikan nama baik The Jakmania khususnya dan Persija pada umumnya.</li>
                  <li>Memupuk dan memelihara persatuan dan kesatuan antar sesame supporter.</li>
                  <li>Senantiasa memberikan dukangan baik moril maupun materil untuk PERSIJA.</li>
                </ol>
                <p>Apabila suatu saat saya melanggar ketentuan diatas maka saya bersedia diambil tindakann sangsi organisasi berupa pencabutan keanggotaan. Segala dampak hokum atas pelanggaran yang saya buat adalah tanggung jawab saya sebagai pribadi dan bukan tanggung jawab THE JAKMANIA. Demikianlah Surat Pernyataan ini saya isi dengan penuh kesadaran dan tanpa adanya unsur paksaan.</p>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label class="container">Ya, Saya Setuju
                <input type="checkbox" name="terms">
                <span class="checkmark"></span>
              </label>
            </div>
          </div>
        </section>
      </form>
		</div>
		<script src="<?php echo base_url().'assets/vendor/wizard/js/jquery-3.3.1.min.js'?>"></script>
		<script src="<?php echo base_url().'assets/vendor/wizard/js/jquery.steps.min.js'?>"></script>
		<script src="<?php echo base_url().'assets/vendor/wizard/js/datepicker-copy.min.js'?>"></script>
    <script src="<?php echo base_url().'assets/vendor/wizard/js/datepicker-id.js'?>"></script>
    <script src="<?php echo base_url().'assets/vendor/wizard/js/main.js'?>"></script>
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
        setTimeout(function(){ <?php unset($_SESSION['pesan']); echo $msg[3];?> }, 3000);
      });
    </script>
    <?php } ?>
  </body>
</html>
<?php } elseif($this->session->userdata('isToken') == FALSE){
  redirect('/');
} ?>