<div class="card shadow mb-4">
  <div class="card-header py-3 flex-row align-items-center">
    <div class="row-foto">
      <div class="foto-anggota">
        <img class="center" src="<?php echo base_url('files/anggota/'.$agtFoto); ?>" data-toggle="modal" data-target="#exampleModal" style="width:80px;height:auto;border-radius:50%;cursor:zoom-in;">
      </div>
    </div>
    <div class="row-anggota">
      <h2 class="header-anggota">
        <span class="nama-anggota"><?php echo $agtNama; ?> (<?php echo $agtNmPendek; ?>)</span>
        <span class="nokta-anggota"><?php echo !empty($noKta) ? $noKta : '-'; ?></span>
      </h2>
    </div>
    <div class="dropdown no-arrow">
      <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
        <a class="dropdown-item" href="<?php echo site_url('anggota/cetak/'.$noKta)?>"><i class="fas fa-print fa-sm text-gray-700"></i> Cetak Formulir</a>
        <a class="dropdown-item" href="<?php echo site_url('anggota/edit/'.$noKta)?>"><i class="fas fa-pencil-alt fa-sm text-gray-700"></i> Ubah Data</a>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-xl-6 col-lg-6 mb-2 row-list">
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Koordinator Wilayah</span>
            <span class="content-list"><?php echo $wilNama ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Tempat, Tanggal Lahir</span>
            <span class="content-list"><?php echo $agtTmptLahir ?>, <?php echo $this->format_tanggal($agtTglLahir); ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Jenis Kelamin</span>
            <span class="content-list"><?php if($agtJnsKelamin == "L"){ echo "Laki - laki"; }else{ echo "Perempuan"; } ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Pendidikan Terakhir</span>
            <span class="content-list"><?php echo $dikPendidikan ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Pekerjaan</span>
            <span class="content-list"><?php echo $pkjNama ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">No. Telepon</span>
            <span class="content-list"><?php echo $agtNoTelp ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Alamat Email</span>
            <span class="content-list"><?php echo $agtEmail ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Ukuran Kaos</span>
            <span class="content-list"><?php echo $agtUkrnKaos ?></span>
          </div>
        </li>
      </div>
      <div class="col-xl-6 col-lg-6 mb-2 row-list">
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Alamat Lengkap</span>
            <span class="content-list"><?php echo $agtAlamatJalan ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Kelurahan</span>
            <span class="content-list"><?php echo !empty($agtKelurahan) ? $agtKelurahan : '-'; ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Kecamatan</span>
            <span class="content-list"><?php echo $agtKecamatan ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Kode Pos</span>
            <span class="content-list"><?php echo $agtKdPos ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Status KTA</span>
            <span class="content-list"><?php if($agtStatusKta == "0"){ echo "Belum"; }else{ echo "Sudah"; } ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Berlaku Dari</span>
            <span class="content-list"><?php echo !empty($agtBrlkDari) ? $agtBrlkDari : '-'; ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Berlaku Sampai</span>
            <span class="content-list"><?php echo !empty($agtBrlkSampai) ? $agtBrlkSampai : '-'; ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Tanggal Pengisian</span>
            <span class="content-list"><?php echo $this->format_tanggal($agtTglInsert); ?></span>
          </div>
        </li>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
        <img src="<?php echo base_url('files/anggota/'.$agtFoto); ?>" style="display:block;margin-right: auto;margin-left:auto;width:100%;">
      </div>
    </div>
  </div>
</div>