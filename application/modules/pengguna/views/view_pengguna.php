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
    <h6 class="m-0 font-weight-bold"><?php echo $title; ?></h6>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table id="table-pengguna" class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>No</th>
            <th>Aksi</th>
            <th>Nama Pengguna</th>
            <th>Username</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<a href="<?php echo $url_add;?>" class="float" title="Tambah Data Pengguna"><i class="fas fa-plus fa-2x my-float"></i></a>
<script type="text/javascript">
$(document).ready(function(){
  var table = $('#table-pengguna').DataTable({
    "processing": true,
    "serverSide": true,
    columns: [
      { "data": "no", "class" : "text-left", "width":"5%", 'sortable' : false },
      { "data": "aksi", "class" : "text-center", "width":"10%", 'sortable' : false },
      { "data": "realname", "class" : "text-left",  "width":"40%",'sortable' : true},
      { "data": "username", "class" : "text-left",  "width":"40%",'sortable' : true}
    ],
    "order" : [],
    "ajax": {
      "url" : "<?php echo $url_get_json;?>",
      "type" : "POST"
    },
    "scrollCollapse": true
  });
});
</script>
