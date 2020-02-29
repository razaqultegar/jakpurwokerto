<div class="card shadow mb-4">
  <div class="card-header py-3">
    <a href="#" class="btn btn-success btn-icon-split btn-sm">
      <span class="icon text-white-50">
        <i class="fas fa-print"></i>
      </span>
      <span class="text">Export Excel</span>
    </a>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="table-anggota" class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>No</th>
            <th>Aksi</th>
            <th>No. KTA</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Wilayah</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    var table = $('#table-anggota').DataTable({
    "processing": true,
    "serverSide": true,
    columns: [
      { "data": "no", "class" : "text-left", "width":"5%", 'sortable' : true },
      { "data": "aksi", "class" : "text-center", "width":"10%", 'sortable' : false },
      { "data": "noKta", "class" : "text-left",  "width":"20%",'sortable' : false},
      { "data": "agtNama", "class" : "text-left",  "width":"30%",'sortable' : true},
      { "data": "jnsKelamin", "class" : "text-left",  "width":"15%",'sortable' : false},
      { "data": "wilNama", "class" : "text-left",  "width":"30%",'sortable' : false}
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