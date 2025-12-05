<div class="card shadow mb-4">
  <div class="card-header py-3 flex-row align-items-center">
    <div class="row-foto">
      <div class="foto-anggota">
        <?php if ($agtFoto != NULL) {?>
				<img src="<?php echo base_url('files/anggota/' . $agtFoto); ?>" class="center" data-toggle="modal" data-target="#exampleModal" style="width:100px;height:auto;border-radius:2%;cursor:zoom-in">
        <?php } else { ?>
				<img src="<?php echo base_url('files/anggota/default.jpg'); ?>" class="center" data-toggle="modal" data-target="#exampleModal" style="width:100px;height:auto;border-radius:2%;cursor:zoom-in">
        <?php } ?>
      </div>
    </div>
    <div class="row-anggota">
      <h2 class="header-anggota">
        <span class="nama-anggota"><?php echo !empty($wilNama) ? $wilNama . ' - ' : ''; ?> <?php echo $agtNama; ?></span>
        <span class="nokta-anggota">No. KTA: <?php echo !empty($agtNoKTA) ? $agtNoKTA : '-'; ?></span>
      </h2>
    </div>
    <div class="dropdown no-arrow">
      <a href="javascript:void(0)" id="dropdownMenuLink" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
      </a>
      <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
        <a class="dropdown-item" href="<?php echo site_url('anggota/edit/' . $agtId)?>">
					<i class="fas fa-pencil-alt fa-sm text-gray-700"></i>
					Ubah Data
				</a>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-xl-6 col-lg-6 mb-2 row-list">
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Nomor Induk Kependudukan</span>
            <span class="content-list"><?php echo $agtNik; ?></span>
          </div>
        </li>
				<li class="mb-2">
          <div class="section-list">
            <span class="header-list">Jenis Kelamin</span>
            <span class="content-list"><?php if($agtJnsKelamin == "L"){ echo "Laki-laki"; }else{ echo "Perempuan"; } ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Tempat, Tanggal Lahir</span>
            <span class="content-list"><?php echo $agtTmptLahir ?>, <?php echo $this->format_tanggal($agtTglLahir); ?> (<?php echo $agtUmur; ?> tahun)</span>
          </div>
        </li>
				<li class="mb-2">
          <div class="section-list">
            <span class="header-list">Alamat Lengkap</span>
            <span class="content-list"><?php echo $agtAlamatJalan; ?> <?php echo !empty($agtKelurahan) ? ', ' . $agtKelurahan : ''; ?> <?php echo !empty($agtKecamatan) ? ', ' . $agtKecamatan : ''; ?> <?php echo !empty($agtKabupaten) ? ', ' . $agtKabupaten : ''; ?> <?php echo !empty($agtProvinsi) ? ', ' . $agtProvinsi : ''; ?></span>
          </div>
        </li>
      </div>
      <div class="col-xl-6 col-lg-6 mb-2 row-list">
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Alamat Email</span>
            <span class="content-list"><?php echo $agtEmail; ?></span>
          </div>
        </li>
				<li class="mb-2">
          <div class="section-list">
            <span class="header-list">No. Telepon</span>
            <span class="content-list"><?php echo $agtNoTelp; ?></span>
          </div>
        </li>
        <li class="mb-2">
          <div class="section-list">
            <span class="header-list">Ukuran Kaos</span>
            <span class="content-list"><?php echo $agtUkuranKaos; ?></span>
          </div>
        </li>
				<li class="mb-2">
          <div class="section-list">
            <span class="header-list">Pembayaran</span>
            <span class="content-list"><?php echo $agtMetodePembayaran; ?></span>
          </div>
        </li>
      </div>
    </div>
		<div class="small text-right">Tanggal Pengisian: <?php echo $this->format_tanggal($agtTglInsert); ?></div>
  </div>
</div>

<div id="exampleModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <?php if ($agtFoto != NULL) {?>
				<img src="<?php echo base_url('files/anggota/' . $agtFoto); ?>" style="display:block;margin-right: auto;margin-left:auto;width:50%">
        <?php } else { ?>
				<img src="<?php echo base_url('files/anggota/default.jpg'); ?>" style="display:block;margin-right: auto;margin-left:auto;width:50%">
        <?php } ?>
      </div>
    </div>
  </div>
</div>
