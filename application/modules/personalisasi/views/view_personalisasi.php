<div class="card shadow mb-4">
  <div class="card-body">
    <div class="table-responsive">
      <table id="example" class="table table-bordered" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>Aksi</th>
            <th>Nama</th>
            <th>Value</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
  $('#example').DataTable({
    "processing": true,
    "serverSide": true,
    columns: [
      { "data": "aksi", "class" : "text-left", "width":"5%", 'sortable' : false },
      { "data": "setLabel", "class" : "text-left",  "width":"15%",'sortable' : false},
      { "data": "setValue", "class" : "text-left", "width":"55%", 'sortable' : false}
    ],
    "order": [],
    "ajax": {
      "url": "<?php echo $url_get_json;?>",
      "type": "POST",
      "data": {}
    }
  });
});
</script>
