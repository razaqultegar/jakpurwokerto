<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $title; ?> - The Jakmania App</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="colorlib.com">
		<link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/wizard/fonts/material-design-iconic-font/css/material-design-iconic-font.css'?>">
		<link rel="stylesheet" href="<?php echo base_url() . 'assets/vendor/wizard/css/style.css'?>">
		<link href="<?php echo base_url().'assets/vendor/toastr/toastr.min.css'?>" rel="stylesheet">
		<style>body{height:unset}.payment-container{max-width:800px;margin:50px auto;background:#fff;padding:40px;border-radius:10px;box-shadow:0 0 20px rgb(0 0 0 / .1)}.payment-header{text-align:center;margin-bottom:30px;padding-bottom:20px;border-bottom:2px solid #f0f0f0}.payment-header h2{color:#333;margin-bottom:10px}.payment-info{margin-bottom:30px}.info-row{display:flex;padding:12px 0;border-bottom:1px solid #f0f0f0}.info-label{font-weight:600;width:200px;color:#555}.info-value{flex:1;color:#333}.bank-info{background:#f8f9fa;padding:25px;border-radius:8px;margin:30px 0}.bank-info h3{color:#333;margin-bottom:20px;font-size:18px}.bank-detail{background:#fff;padding:15px;border-radius:5px;margin-bottom:15px;border-left:4px solid #007bff}.bank-name{font-weight:600;color:#007bff;font-size:16px;margin-bottom:8px}.account-number{font-size:20px;font-weight:700;color:#333;margin:10px 0;letter-spacing:1px}.account-name{color:#666;font-size:14px}.copy-btn{background:#007bff;color:#fff;border:none;padding:5px 15px;border-radius:4px;cursor:pointer;font-size:12px;margin-top:5px}.copy-btn:hover{background:#0056b3}.amount-box{background:#28a745;color:#fff;padding:20px;border-radius:8px;text-align:center;margin:20px 0}.amount-label{font-size:14px;margin-bottom:5px}.amount-value{font-size:32px;font-weight:700}.instructions{background:#fff3cd;border-left:4px solid #ffc107;padding:20px;border-radius:5px;margin:20px 0}.instructions h4{color:#856404;margin-bottom:15px}.instructions ol{margin-left:20px;color:#856404}.instructions li{margin-bottom:8px}.btn-back{display:inline-block;background:#6c757d;color:#fff;padding:12px 30px;text-decoration:none;border-radius:5px;margin-top:20px}.btn-back:hover{background:#5a6268}.success-icon{text-align:center;margin-bottom:20px}.success-icon i{font-size:60px;color:#28a745}</style>
	</head>
	<body>
		<div class="payment-container">
			<div class="success-icon">
				<i class="zmdi zmdi-check-circle"></i>
			</div>
			<div class="payment-header">
				<h2>Pendaftaran Berhasil!</h2>
				<p>Silakan lakukan pembayaran untuk menyelesaikan perpanjangan KTA Anda</p>
			</div>
			<div class="payment-info">
				<h3 style="margin-bottom: 20px; color: #333;">Informasi Pendaftar</h3>
				<div class="info-row">
					<div class="info-label">Nama Lengkap</div>
					<div class="info-value"><?php echo $anggota['agtNama']; ?></div>
				</div>
				<div class="info-row">
					<div class="info-label">NIK</div>
					<div class="info-value"><?php echo $anggota['agtNik']; ?></div>
				</div>
				<div class="info-row">
					<div class="info-label">No. KTA Sebelumnya</div>
					<div class="info-value"><?php echo $anggota['agtNoKTA'] ? $anggota['agtNoKTA'] : '-'; ?></div>
				</div>
				<div class="info-row">
					<div class="info-label">Email</div>
					<div class="info-value"><?php echo $anggota['agtEmail']; ?></div>
				</div>
				<div class="info-row">
					<div class="info-label">No. Telp/HP</div>
					<div class="info-value"><?php echo $anggota['agtNoTelp']; ?></div>
				</div>
			</div>
			<div class="amount-box">
				<div class="amount-label">Total Pembayaran</div>
				<div class="amount-value">Rp150.000</div>
				<small>Biaya Perpanjangan KTA Jakmania</small>
			</div>
			<div class="bank-info">
				<h3>Informasi Rekening Transfer</h3>
				<?php if ($anggota['agtMetodePembayaran'] == 'BCA') { ?>
				<div class="bank-detail">
					<div class="bank-name">Bank Central Asia (BCA)</div>
					<div class="account-number" id="accountBCA">0461939073 <span class="account-name">a.n. Maya Nurazizah</span></div>
					<button class="copy-btn" onclick="copyAccount('accountBCA')">Salin Nomor Rekening</button>
				</div>
				<?php } elseif ($anggota['agtMetodePembayaran'] == 'MANDIRI') { ?>
				<div class="bank-detail">
					<div class="bank-name">Bank Mandiri</div>
					<div class="account-number" id="accountMandiri">1800015214796 <span class="account-name">a.n. Khilmi Choerul F.</span></div>
					<button class="copy-btn" onclick="copyAccount('accountMandiri')">Salin Nomor Rekening</button>
				</div>
				<?php } ?>
			</div>
			<div class="instructions">
				<h4>Petunjuk Pembayaran:</h4>
				<ol>
					<li>Transfer sesuai <strong>nominal yang tertera</strong> ke rekening di atas</li>
					<li>Simpan bukti transfer Anda</li>
					<li>Konfirmasi pembayaran melalui WhatsApp ke <strong>08xx-xxxx-xxxx</strong></li>
					<li>Sertakan nama lengkap dan nomor KTA lama (jika ada) saat konfirmasi</li>
					<li>Pembayaran akan diverifikasi maksimal 1x24 jam</li>
					<li>KTA baru akan dikirimkan setelah pembayaran terverifikasi</li>
				</ol>
			</div>
			<div style="text-align: center;">
				<a href="<?php echo base_url(); ?>" class="btn-back">Kembali ke Beranda</a>
			</div>
		</div>
		<script src="<?php echo base_url() . 'assets/vendor/wizard/js/jquery-3.3.1.min.js'?>"></script>
		<script src="<?php echo base_url() . 'assets/vendor/toastr/toastr.min.js'?>"></script>
		<script>
			function copyAccount(elementId) {
				const accountNumber = document.getElementById(elementId).textContent;
				
				// Create temporary input
				const tempInput = document.createElement('input');
				tempInput.value = accountNumber;
				document.body.appendChild(tempInput);
				tempInput.select();
				document.execCommand('copy');
				document.body.removeChild(tempInput);
			
				// Show notification
				toastr.options = {
					"closeButton": true,
					"progressBar": true,
					"positionClass": "toast-top-right",
					"timeOut": "2000"
				}
				toastr.success('Nomor rekening berhasil disalin!');
			}

			<?php if(isset($msg[0]) == '1') { ?>
			$(document).ready(function(){
				toastr.options = {
					"closeButton": true,
					"progressBar": true,
					"positionClass": "toast-top-right",
					"showDuration": "3000",
					"hideDuration": "3000",
					"timeOut": "3000",
					"extendedTimeOut": "3000"
				}
				toastr['<?php echo $msg[1];?>']('<?php echo $msg[2];?>')
			});
			<?php } ?>
		</script>
	</body>
</html>
