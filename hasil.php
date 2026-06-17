<?php
include 'koneksi.php';include 'layout.php';
$status=$_GET['status']??'';
$barang=htmlspecialchars($_GET['barang']??'');
$id=(int)($_GET['id']??0);
renderHead('Hasil Verifikasi');renderTopbar('home');
?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:24px;background:var(--bg);">
  <div style="text-align:center;max-width:460px;">
    <?php if($status==='valid'):?>
    <div style="width:88px;height:88px;border-radius:50%;background:var(--green-light);border:3px solid #bbf7d0;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;color:var(--green);animation:pop .4s cubic-bezier(.34,1.56,.64,1);">
      <i class="fa-solid fa-check"></i>
    </div>
    <h1 style="font-family:'DM Serif Display',serif;font-size:30px;color:var(--green);margin-bottom:10px;">Verifikasi Berhasil!</h1>
    <p style="color:var(--text2);line-height:1.7;font-size:15px;">Kepemilikan <b style="color:var(--text);"><?=$barang?></b> telah terkonfirmasi.<br>Silakan ambil barang Anda di kantor petugas dengan menunjukkan identitas diri.</p>
    <div class="alert alert-success" style="margin:20px 0;text-align:left;">
      <i class="fa-solid fa-circle-check"></i>
      <span>Status barang telah diperbarui menjadi <b>Claimed</b>. Terima kasih telah menggunakan SmartFinder.</span>
    </div>
    <?php else:?>
    <div style="width:88px;height:88px;border-radius:50%;background:var(--red-light);border:3px solid #fecaca;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:40px;color:var(--red);">
      <i class="fa-solid fa-xmark"></i>
    </div>
    <h1 style="font-family:'DM Serif Display',serif;font-size:30px;color:var(--red);margin-bottom:10px;">Verifikasi Gagal</h1>
    <p style="color:var(--text2);line-height:1.7;font-size:15px;">Bukti yang Anda masukkan tidak cocok dengan data yang tersimpan. Periksa kembali atau hubungi petugas langsung.</p>
    <div class="alert alert-warning" style="margin:20px 0;text-align:left;">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <span>Percobaan klaim telah dicatat. Jika Anda merasa ini adalah kesalahan, hubungi petugas kampus.</span>
    </div>
    <?php endif;?>
    <div style="display:flex;gap:12px;justify-content:center;">
      <a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-house"></i> Beranda</a>
      <?php if($status!=='valid'&&$id):?><a href="klaim.php?id=<?=$id?>" class="btn btn-primary"><i class="fa-solid fa-rotate-left"></i> Coba Lagi</a><?php endif;?>
    </div>
  </div>
</div>
<style>@keyframes pop{from{transform:scale(.5);opacity:0;}to{transform:scale(1);opacity:1;}}</style>
<?php renderFoot();?>
