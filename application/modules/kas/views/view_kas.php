<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold">Jumlah Saldo : <?php echo $total_kas_saldo; ?></h6>
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