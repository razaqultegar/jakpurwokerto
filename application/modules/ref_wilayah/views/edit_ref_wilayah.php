<style>
.masked_input {
  text-align: left !important
}
</style>
<script src="<?php echo base_url('assets/vendor/jquery.inputmask/4.0/jquery.inputmask.bundle.js');?>"></script>
<script type="text/javascript">
var regEx1 = new RegExp((',00').replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g');
var regEx2 = new RegExp(('.').replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g');
    
function isNumber(evt) {
  evt = (evt) ? evt : window.event;
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    return false;
  }
  return true;
}

function makeNumber(numb){
  var r = numb.replace(regEx1, '').replace(regEx2, '');
  return r;
}

$(document).ready(function(){
  $('.masked_input').inputmask({
    alias: "currency",
    rightAlign: true,
    groupSeparator: ".",
    radixPoint: ",",
    autoGroup: true,
    digitsOptional: false,
    digits: 0,
    allowPlus: false,
    allowMinus: true,
    onUnMask: function(maskedValue, unmaskedValue) {
      return unmaskedValue;
    },
    prefix: 'Rp ', 
    placeholder: '0',
    oncomplete : function(){
      calculate();
    }
  });
});

function calculate(){
  var awal = $('#kasMasuk').inputmask('unmaskedvalue');
  awal = makeNumber(awal);
  var susut = $("#kasKeluar").inputmask('unmaskedvalue');
  susut = makeNumber(susut);
  var nilai = eval(awal-susut);
  console.log(nilai);
  $("#kasSaldo").val(nilai);
}
</script>
<?php if($msg[0] == '1') { ?>
<div class="alert alert-<?php echo $msg[1];?> alert-dismissible fade show" role="alert">
  <i class="fas fa-info-circle mr-2"></i><?php echo $msg[2];?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php } unset($_SESSION['pesan']); ?>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold">Ubah Data Wilayah</h6>
  </div>
  <div class="card-body">
    <form action="<?php echo site_url('ref_wilayah/editAction');?>" method="post" autocomplete="off">
      <input type="hidden" name="wilIdWilayah" value="<?php echo $wilIdWilayah?>">
      <div class="row">
        <div class="col-xl-12 col-lg-12 mb-2 row-list">
          <li class="mb-2">
            <div class="section-list">
              <label>Nama Wilayah</label>
              <input name="wilNama" type="text" class="form-control" value="<?php echo $wilNama?>">
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
