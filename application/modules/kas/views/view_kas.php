<?php if($msg[0] == '1'){ ?>
<div class="alert alert-<?php echo $msg[1];?> alert-dismissible fade show" role="alert">
  <i class="fas fa-info-circle mr-2"></i><?php echo $msg[2];?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php } unset($_SESSION['pesan']); ?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <?php echo form_open('kas/export', array('id' => 'form-filter', 'class' => 'form-horizontal'));?>
      <button type="submit" class="btn btn-success btn-icon-split btn-sm">
        <span class="icon text-white-50">
          <i class="fas fa-print"></i>
        </span>
        <span class="text">Export Excel</span>
      </button>
      <input type="hidden" value="" id='export' name="filter_multiple">
    <?php echo form_close();?>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="table-kas" class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>No</th>
            <th>Aksi</th>
            <th>Tanggal</th>
            <th>Wilayah</th>
            <th>Kas Masuk</th>
            <th>Kas Keluar</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<a href="<?php echo $url_add;?>" class="float" title="Tambah Data Kas Umum"><i class="fas fa-plus fa-2x my-float"></i></a>
<script type="text/javascript">
$(document).ready(function(){
    var table = $('#table-kas').DataTable({
    "processing": true,
    "serverSide": true,
    columns: [
      { "data": "no", "class" : "text-left", "width":"5%", 'sortable' : false },
      { "data": "aksi", "class" : "text-center", "width":"10%", 'sortable' : false },
      { "data": "kasTanggal", "class" : "text-left",  "width":"14%",'sortable' : false},
      { "data": "wilNama", "class" : "text-left",  "width":"14%",'sortable' : false},
      { "data": "kasMasuk", "class" : "text-left",  "width":"20%",'sortable' : true},
      { "data": "kasKeluar", "class" : "text-left",  "width":"20%",'sortable' : true}
    ],
    "order" : [],
    "ajax": {
      "url" : "<?php echo $url_get_json;?>",
      "type" : "POST"
    },
    "scrollY": false,
    "scrollCollapse": true
  });
});
</script>