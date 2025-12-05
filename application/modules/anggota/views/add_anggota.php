<?php if(isset($msg[0]) == '1') { ?>
<div class="alert alert-<?php echo $msg[1];?> alert-dismissible fade show" role="alert">
  <i class="fas fa-info-circle mr-2"></i><?php echo $msg[2];?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php } unset($_SESSION['pesan']); ?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold">Tambah Data Anggota</h6>
  </div>
  <div class="card-body">
    <form action="<?php echo site_url('anggota/addAction');?>" method="post" enctype="multipart/form-data" autocomplete="off">
      <div class="row">
        <div class="col-xl-4 col-lg-4 mb-2 row-list">
          <li class="mb-2">
            <label>Foto Pas (2x3cm)</label>
            <input name="agtFoto" type="file" class="form-control">
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Koordinator Wilayah</label>
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
            </div>
          </li>
        </div>
        <div class="col-xl-4 col-lg-4 mb-2 row-list">
          <li class="mb-2">
            <div class="section-list">
              <label>No. KTA</label>
              <input name="agtNoKta" type="text" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Nama Lengkap</label>
              <input name="agtNama" type="text" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Nama Panggilan</label>
              <input name="agtNmPendek" type="text" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Jenis Kelamin</label><br/>
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
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Tempat Lahir</label>
              <input name="agtTmptLahir" type="text" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Tanggal Lahir</label>
              <input name="agtTglLahir" type="text" class="form-control" data-language="id" data-date-format="dd/mm/yyyy" id="dp1">
            </div>
          </li>
        </div>
        <div class="col-xl-4 col-lg-4 mb-2 row-list">
          <li class="mb-2">
            <div class="section-list">
              <label>Alamat Jalan</label>
              <input name="agtAlamatJalan" type="text" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <div class="row">
                <div class="col">
                  <label>Kelurahan</label>
                  <input name="agtKelurahan" type="text" class="form-control">
                </div>
                <div class="col">
                  <label>Kecamatan</label>
                  <input name="agtKecamatan" type="text" class="form-control">
                </div>
              </div>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Kode Pos</label>
              <input name="agtKdPos" type="number" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Status KTA</label>
              <select name="agtStatusKta" class="form-control">
                <option value="">-Pilih Status KTA-</option>
                <?php
                  if (!empty($list_status_kta)){
                    foreach ($list_status_kta as $key => $value) {
                      echo '<option value="'.$key.'">'.$value.'</option>';
                    }
                  }
                ?>
              </select>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <div class="row">
                <div class="col">
                  <label>Berlaku Dari</label>
                  <input name="agtBrlkDari" type="text" class="form-control">
                </div>
                <div class="col">
                  <label>Berlaku Sampai</label>
                  <input name="agtBrlkSampai" type="text" class="form-control">
                </div>
              </div>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>No. Telepon</label>
              <input name="agtNoTelp" type="number" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Alamat Email</label>
              <input name="agtEmail" type="email" class="form-control">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Ukuran Kaos</label>
              <input name="agtUkrnKaos" type="text" class="form-control" style="text-transform:uppercase;">
            </div>
          </li>
        </div>
      </div>
      <div class="text-right mt-4 mb-2">
        <button type="submit" class="btn btn-success btn-icon-split btn-sm" name="simpan" value="simpan">
          <span class="icon text-white-50">
            <i class="fas fa-save"></i>
          </span>
          <span class="text">Simpan Perubahan</span>
        </button>
        <button type="button" onclick="history.go(-1);" class="btn btn-danger btn-icon-split btn-sm" name="batal" value="batal">
          <span class="icon text-white-50">
            <i class="fas fa-times"></i>
          </span>
          <span class="text">Batalkan</span>
        </button>
      </div>
    </form>
  </div>
</div>
