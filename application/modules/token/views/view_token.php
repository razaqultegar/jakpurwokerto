<?php if($msg[0] == '1'){ ?>
<div class="alert alert-<?php echo $msg[1];?> alert-dismissible fade show" role="alert">
  <i class="fas fa-info-circle mr-2"></i><?php echo $msg[2];?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php } unset($_SESSION['pesan']); ?>
<div class="row">
  <div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Tambah Token</h6>
      </div>
      <div class="card-body">
        <form name="tokenform" action="<?php echo site_url('token/addAction');?>" method="post" autocomplete="off">
          <div class="row">
            <div class="col-xl-12 col-lg-12 mb-2 row-list">
              <li class="mb-2">
                <div class="section-list">
                  <label>Token</label>
                  <input name="token" type="text" value="" class="form-control">
                </div>
              </li>
            </div>
          </div>
          <div class="text-right">
            <button type="button" onClick="random_all();" class="btn btn-secondary btn-icon-split btn-sm" name="batal" value="batal">
              <span class="icon text-white-50">
                <i class="fas fa-sync"></i>
              </span>
              <span class="text">Generate</span>
            </button>
            <button type="submit" class="btn btn-success btn-icon-split btn-sm" name="simpan" value="simpan">
              <span class="icon text-white-50">
                <i class="fas fa-save"></i>
              </span>
              <span class="text">Simpan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-xl-6 col-lg-6">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Data Token</h6>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th width="5%">Aksi</th>
                <th>Token</th>
              </tr>
            </thead>
          <?php 
            foreach($token as $row){ 
          ?>
            <tbody>
              <tr>
                <td><a href="<?php echo base_url('token/delete/'.$row->tokenId) ?>" class="btn btn-danger btn-circle btn-sm" title="Hapus Data"><i class="fas fa-trash"></i></a></td>
                <td><?php echo $row->token  ?></td>
              </tr>
            </tbody>
          <?php } ?>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
