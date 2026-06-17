<?php
include 'koneksi.php';include 'layout.php';
$id=(int)($_GET['id']??0);
$q=mysqli_query($conn,"SELECT b.*,k.nama_kategori,k.icon,l.nama_lokasi FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori JOIN lokasi l ON b.id_lokasi=l.id_lokasi WHERE b.id_barang=$id AND b.status='Unclaimed'");
$d=mysqli_fetch_assoc($q);
if(!$d){header("Location: index.php");exit;}
include 'barang.php';
$objek=buatObjekBarang($id,$d['deskripsi'],$d['nama_kategori']);
$icon_map=['smartphone'=>'fa-mobile-screen','file-text'=>'fa-file-lines','briefcase'=>'fa-briefcase','key'=>'fa-key','box'=>'fa-box'];
$fa=$icon_map[$d['icon']]??'fa-box';
renderHead('Klaim Barang');renderTopbar('home');
?>
<div class="wrap" style="max-width:680px;">
  <a href="index.php" style="display:inline-flex;align-items:center;gap:8px;color:var(--text2);font-size:14px;margin-bottom:24px;"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar</a>

  <!-- Barang Info Card -->
  <div class="card" style="margin-bottom:20px;border-left:4px solid var(--primary);">
    <div style="display:flex;align-items:center;gap:16px;">
      <div style="width:60px;height:60px;border-radius:14px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;color:var(--primary);font-size:26px;flex-shrink:0;">
        <i class="fa-solid <?=$fa?>"></i>
      </div>
      <div style="flex:1;">
        <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text3);margin-bottom:4px;"><?=$d['nama_kategori']?></div>
        <div style="font-size:20px;font-weight:700;"><?=htmlspecialchars($d['deskripsi'])?></div>
        <div style="color:var(--text2);font-size:13px;margin-top:6px;display:flex;gap:16px;flex-wrap:wrap;">
          <?php if($d['merek']):?><span><i class="fa-solid fa-tag"></i> <?=htmlspecialchars($d['merek'])?></span><?php endif;?>
          <span><i class="fa-solid fa-location-dot"></i> <?=$d['nama_lokasi']?></span>
          <span><i class="fa-regular fa-clock"></i> <?=date('d M Y',strtotime($d['tgl_temu']))?></span>
        </div>
      </div>
      <?php if($d['foto']&&file_exists('uploads/'.$d['foto'])):?>
      <img src="uploads/<?=htmlspecialchars($d['foto'])?>" style="width:80px;height:70px;object-fit:cover;border-radius:10px;flex-shrink:0;">
      <?php endif;?>
    </div>
  </div>

  <!-- Alert -->
  <div class="alert alert-info" style="margin-bottom:20px;">
    <i class="fa-solid fa-circle-info"></i>
    <span>Isi data berikut dengan benar. Sistem akan memverifikasi kepemilikan secara otomatis menggunakan data yang Anda masukkan.</span>
  </div>

  <!-- Form Klaim -->
  <div class="card">
    <h2 style="font-size:18px;font-weight:700;margin-bottom:20px;"><i class="fa-solid fa-shield-check" style="color:var(--primary);margin-right:8px;"></i>Form Verifikasi Kepemilikan</h2>
    <form action="proses.php" method="POST" class="form-grid">
      <input type="hidden" name="id" value="<?=$id?>">
      <div class="form-row">
        <div class="form-group"><label>Nama Lengkap <span style="color:var(--red);">*</span></label>
          <input type="text" name="nama_pencari" class="form-control" placeholder="Sesuai KTM / KTP" required>
        </div>
        <div class="form-group"><label>No. Identitas (NIM/NIP/KTP) <span style="color:var(--red);">*</span></label>
          <input type="text" name="identitas" class="form-control" placeholder="Nomor identitas" required>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>No. HP / WhatsApp</label>
          <div class="input-group"><i class="fa-solid fa-phone"></i><input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx"></div>
        </div>
        <div class="form-group"><label>Email</label>
          <div class="input-group"><i class="fa-solid fa-envelope"></i><input type="email" name="email" class="form-control" placeholder="email@kampus.ac.id"></div>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label style="display:flex;align-items:center;gap:8px;">
          <span style="background:var(--primary-light);color:var(--primary);padding:4px 10px;border-radius:6px;font-size:12px;">KUNCI VERIFIKASI</span>
          <?=$objek->labelBukti()?> <span style="color:var(--red);">*</span>
        </label>
        <input type="text" name="bukti_input" class="form-control" placeholder="<?=$objek->placeholderBukti()?>" required>
        <span class="hint" style="color:var(--primary);"><i class="fa-solid fa-lock"></i> Data ini dicocokan secara langsung dengan data rahasia yang tersimpan di sistem.</span>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;font-size:15px;">
        <i class="fa-solid fa-paper-plane"></i> Kirim Verifikasi
      </button>
    </form>
  </div>
</div>
<?php renderFoot();?>
