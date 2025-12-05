<?php if ($this->session->userdata('isToken') == TRUE) { ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?> - The Jakmania App</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="colorlib.com">
    <link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/wizard/fonts/material-design-iconic-font/css/material-design-iconic-font.css'?>">
		<link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/wizard/css/datepicker.min.css'?>">
		<link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/wizard/css/style.css'?>">
    <link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.css'?>">
	</head>
	<body>
		<div class="wrapper">
      <form id="wizard" method="post" action="<?php echo site_url('perpanjangan/addAction');?>" enctype="multipart/form-data" autocomplete="off">
        <h4></h4>
        <section>
          <h3>Formulir Perpanjangan</h3>
          <div class="form-row">
            <div class="form-col">
              <label>Foto Pas (2x3cm) <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtFoto" type="file" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Nomor Induk Kependudukan <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtNik" type="text" class="form-control">
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
          </div>
          <div class="form-row">
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
          <h3>Formulir Perpanjangan</h3>
          <div class="form-row">
            <div class="form-col">
              <label>Provinsi <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtProvinsi" id="agtProvinsi" class="form-control">
                  <option value="">-- Pilih Provinsi --</option>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
            <div class="form-col">
              <label>Kabupaten/Kota <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtKabupaten" id="agtKabupaten" class="form-control">
                  <option value="">-- Pilih Kabupaten/Kota --</option>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>Kecamatan <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtKecamatan" id="agtKecamatan" class="form-control">
                  <option value="">-- Pilih Kecamatan --</option>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
            <div class="form-col">
              <label>Kelurahan/Desa <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtKelurahan" id="agtKelurahan" class="form-control">
                  <option value="">-- Pilih Kelurahan/Desa --</option>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col" style="width:100%;">
              <label>Alamat Jalan</label>
              <div class="form-holder">
                <input name="agtAlamatJalan" type="text" class="form-control">
              </div>
            </div>
          </div>
        </section>
        <h4></h4>
        <section>
          <h3>Formulir Perpanjangan</h3>
          <div class="form-row">
            <div class="form-col">
              <label>Alamat Email <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtEmail" type="email" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>No. Telp/HP <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtNoTelp" type="number" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>No. KTA Sebelumnya <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtNoKTA" type="email" class="form-control">
              </div>
            </div>
            <div class="form-col">
              <label>Foto KTA Sebelumnya <span style="color:red;">*</span></label>
              <div class="form-holder">
                <input name="agtFotoKTA" type="file" class="form-control">
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>Ukuran Kaos <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtUkuranKaos" class="form-control">
                  <option value="">-- Pilih Ukuran Kaos --</option>
                  <option value="S">S</option>
                  <option value="M">M</option>
                  <option value="L">L</option>
                  <option value="XL">XL</option>
                  <option value="XXL">XXL</option>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
            <div class="form-col">
              <label>Metode Pembayaran <span style="color:red;">*</span></label>
              <div class="form-holder">
                <select name="agtMetodePembayaran" class="form-control">
                  <option value="">-- Pilih Metode Pembayaran --</option>
                  <option value="BCA">Manual Transfer - Bank Central Asia (BCA)</option>
                  <option value="MANDIRI">Manual Transfer - Bank Mandiri</option>
                </select>
                <i class="zmdi zmdi-chevron-down"></i>
              </div>
            </div>
          </div>
        </section>
      </form>
		</div>
		<script src="<?php echo base_url() . 'assets/vendor/wizard/js/jquery-3.3.1.min.js'?>"></script>
		<script src="<?php echo base_url() . 'assets/vendor/wizard/js/jquery.steps.min.js'?>"></script>
		<script src="<?php echo base_url() . 'assets/vendor/wizard/js/datepicker.min.js'?>"></script>
    <script src="<?php echo base_url() . 'assets/vendor/wizard/js/datepicker-id.js'?>"></script>
    <script src="<?php echo base_url() . 'assets/vendor/wizard/js/main.js'?>"></script>
    <script src="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.js'?>"></script>
    <script>
			$(document).ready(function(){
				const baseUrl = '<?php echo site_url('api/'); ?>';

				// Load provinces on page load
				loadProvinces();

				// Load regencies when province changes
				$('#agtProvinsi').change(function(){
					const provinceCode = $(this).val();
					if(provinceCode) {
						loadRegencies(provinceCode);
						$('#agtKabupaten').html('<option value="">-- Pilih Kabupaten/Kota --</option>');
						$('#agtKecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
						$('#agtKelurahan').html('<option value="">-- Pilih Kelurahan/Desa --</option>');
					}
				});

				// Load districts when regency changes
				$('#agtKabupaten').change(function(){
					const regencyCode = $(this).val();
					if(regencyCode) {
						loadDistricts(regencyCode);
						$('#agtKecamatan').html('<option value="">-- Pilih Kecamatan --</option>');
						$('#agtKelurahan').html('<option value="">-- Pilih Kelurahan/Desa --</option>');
					}
				});

				// Load villages when district changes
				$('#agtKecamatan').change(function(){
					const districtCode = $(this).val();
					if(districtCode) {
						loadVillages(districtCode);
						$('#agtKelurahan').html('<option value="">-- Pilih Kelurahan/Desa --</option>');
					}
				});

				function loadProvinces() {
					$.ajax({
						url: baseUrl + 'get_provinces',
						type: 'GET',
						dataType: 'json',
						success: function(response) {
							let html = '<option value="">-- Pilih Provinsi --</option>';
							if (response.data) {
								$.each(response.data, function(index, item) {
									html += '<option value="' + item.code + '">' + item.name + '</option>';
								});
							}
							$('#agtProvinsi').html(html);
						}
					});
				}

				function loadRegencies(provinceCode) {
					$.ajax({
						url: baseUrl + 'get_regencies',
						type: 'GET',
						data: { province_code: provinceCode },
						dataType: 'json',
						success: function(response) {
							let html = '<option value="">-- Pilih Kabupaten/Kota --</option>';
							if (response.data) {
								$.each(response.data, function(index, item) {
									html += '<option value="' + item.code + '">' + item.name + '</option>';
								});
							}
							$('#agtKabupaten').html(html);
						}
					});
				}

				function loadDistricts(regencyCode) {
					$.ajax({
						url: baseUrl + 'get_districts',
						type: 'GET',
						data: { regency_code: regencyCode },
						dataType: 'json',
						success: function(response) {
							let html = '<option value="">-- Pilih Kecamatan --</option>';
							if (response.data) {
								$.each(response.data, function(index, item) {
									html += '<option value="' + item.code + '">' + item.name + '</option>';
								});
							}
							$('#agtKecamatan').html(html);
						}
					});
				}

				function loadVillages(districtCode) {
					$.ajax({
						url: baseUrl + 'get_villages',
						type: 'GET',
						data: { district_code: districtCode },
						dataType: 'json',
						success: function(response) {
							let html = '<option value="">-- Pilih Kelurahan/Desa --</option>';
							if (response.data) {
								$.each(response.data, function(index, item) {
									html += '<option value="' + item.code + '">' + item.name + '</option>';
								});
							}
							$('#agtKelurahan').html(html);
						}
					});
				}
			});
    </script>
    <?php if(isset($msg[0]) == '1') { ?>
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
