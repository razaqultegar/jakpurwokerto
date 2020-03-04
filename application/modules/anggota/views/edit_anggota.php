<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold">Ubah Data Anggota</h6>
  </div>
  <div class="card-body">
    <form action="<?php echo site_url('anggota/editAction');?>" method="post" enctype="multipart/form-data">
      <input type="hidden" name="agtId" value="<?php echo $agtId; ?>">
      <div class="row">
        <div class="col-xl-4 col-lg-4 mb-2 row-list">
          <li class="mb-2">
            <?php if($agtFoto != NULL) {?>
              <img class="center" src="<?php echo base_url('files/anggota/'.$agtFoto); ?>" style="width:260px;height:auto;border-radius:2%;margin-left:auto;margin-right:auto;display:block;">
            <?php } else { ?>
              <img class="center" src="<?php echo base_url('files/anggota/default.jpg'); ?>" style="width:260px;height:auto;border-radius:2%;margin-left:auto;margin-right:auto;display:block;">
            <?php } ?>
          </li>
          <li class="mb-2">
            <label>Foto Pas (2x3cm)</label>
            <input name="agtFoto" type="file" class="form-control">
            <input name="agtFoto" type="hidden" class="form-control" value="<?php echo $agtFoto; ?>">
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Koordinator Wilayah</label>
              <select name="agtIdWilayah" class="form-control">
              <?php
              if (!empty($list_wilayah)){
                foreach ($list_wilayah as $key => $value) {
                  echo '<option value="'.$value->wilIdWilayah.'" '.set_select('agtIdWilayah', $value->wilIdWilayah, ($agtIdWilayah == $value->wilIdWilayah) ? true : false).'>'.$value->wilNama.'</option>';
                }
              }
              ?>
              </select>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Tanggal Pengisian</label>
              <input name="agtTglInsert" type="text" class="form-control" value="<?php echo $this->format_tanggal($agtTglInsert); ?>" disabled>
            </div>
          </li>
        </div>
        <div class="col-xl-4 col-lg-4 mb-2 row-list">
          <li class="mb-2">
            <div class="section-list">
              <label>No. KTA</label>
              <input name="agtNoKta" type="text" class="form-control" value="<?php echo $agtNoKta ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Nama Lengkap</label>
              <input name="agtNama" type="text" class="form-control" value="<?php echo $agtNama; ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Nama Panggilan</label>
              <input name="agtNmPendek" type="text" class="form-control" value="<?php echo $agtNmPendek; ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Jenis Kelamin</label><br/>
              <select name="agtJnsKelamin" class="form-control">
                <option value="L" <?php if($agtJnsKelamin == 'L') { echo "selected"; } ?>>Laki-laki</option>
                <option value="P" <?php if($agtJnsKelamin == 'P') { echo "selected"; } ?>>Perempuan</option>
              </select>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Tempat Lahir</label>
              <input name="agtTmptLahir" type="text" class="form-control" value="<?php echo $agtTmptLahir; ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Tanggal Lahir</label>
              <input name="agtTglLahir" type="text" class="form-control" data-language="id" data-date-format="dd/mm/yyyy" id="dp1" value="<?php echo date('d/m/Y', strtotime($agtTglLahir)); ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Pendidikan Terakhir</label>
              <select name="agtIdPendidikan" class="form-control">
              <?php
              if (!empty($list_pendidikan)){
                foreach ($list_pendidikan as $key => $value) {
                  echo '<option value="'.$value->dikIdPendidikan.'" '.set_select('agtIdPendidikan', $value->dikIdPendidikan, ($agtIdPendidikan == $value->dikIdPendidikan) ? true : false).'>'.$value->dikPendidikan.'</option>';
                }
              }
              ?>
              </select>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Pekerjaan</label>
              <select name="agtIdPekerjaan" class="form-control">
              <?php
              if (!empty($list_pekerjaan)){
                foreach ($list_pekerjaan as $key => $value) {
                  echo '<option value="'.$value->pkjIdPekerjaan.'" '.set_select('agtIdPekerjaan', $value->pkjIdPekerjaan, ($agtIdPekerjaan == $value->pkjIdPekerjaan) ? true : false).'>'.$value->pkjNama.'</option>';
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
              <label>Alamat Lengkap</label>
              <input name="agtAlamatJalan" type="text" class="form-control" value="<?php echo $agtAlamatJalan; ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <div class="row">
                <div class="col">
                  <label>Kelurahan</label>
                  <input name="agtKelurahan" type="text" class="form-control" value="<?php echo $agtKelurahan; ?>">
                </div>
                <div class="col">
                  <label>Kecamatan</label>
                  <input name="agtKecamatan" type="text" class="form-control" value="<?php echo $agtKecamatan; ?>">
                </div>
              </div>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Kode Pos</label>
              <input name="agtKdPos" type="number" class="form-control" value="<?php echo $agtKdPos; ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Status KTA</label>
              <select name="agtStatusKta" class="form-control">
                <option value="0" <?php if($agtStatusKta == '0') { echo "selected"; } ?>>Belum</option>
                <option value="1" <?php if($agtStatusKta == '1') { echo "selected"; } ?>>Sudah</option>
              </select>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <div class="row">
                <div class="col">
                  <label>Berlaku Dari</label>
                  <input name="agtBrlkDari" type="text" class="form-control" value="<?php echo $agtBrlkDari ?>">
                </div>
                <div class="col">
                  <label>Berlaku Sampai</label>
                  <input name="agtBrlkSampai" type="text" class="form-control" value="<?php echo $agtBrlkSampai ?>">
                </div>
              </div>
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>No. Telepon</label>
              <input name="agtNoTelp" type="number" class="form-control" value="<?php echo $agtNoTelp ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Alamat Email</label>
              <input name="agtEmail" type="email" class="form-control" value="<?php echo $agtEmail ?>">
            </div>
          </li>
          <li class="mb-2">
            <div class="section-list">
              <label>Ukuran Kaos</label>
              <input name="agtUkrnKaos" type="text" class="form-control" value="<?php echo $agtUkrnKaos ?>" style="text-transform:uppercase;">
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
        <button type="button" onclick="history.go(-1);" class="btn btn-danger btn-icon-split btn-sm">
          <span class="icon text-white-50">
            <i class="fas fa-times"></i>
          </span>
          <span class="text">Batalkan</span>
        </button>
      </div>
    </form>
  </div>
</div>