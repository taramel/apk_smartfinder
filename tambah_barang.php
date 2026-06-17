<?php
include 'koneksi.php'; include 'layout.php';
requireLogin();
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['simpan'])){
    $kat=(int)$_POST['kategori']; $lok=(int)$_POST['lokasi'];
    $des=e($conn,$_POST['deskripsi']); $warna=e($conn,$_POST['warna']??'');
    $merek=e($conn,$_POST['merek']??''); $buk=e($conn,$_POST['bukti']);
    $petugas=(int)$_SESSION['user_id'];
    $expired=e($conn,$_POST['tgl_expired']??'');
    $expired_sql=$expired?"'$expired 23:59:59'":'DATE_ADD(NOW(),INTERVAL 30 DAY)';
    $foto='';
    if(!empty($_FILES['foto']['name'])&&$_FILES['foto']['error']===0){
        $ext=strtolower(pathinfo($_FILES['foto']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','webp'])&&$_FILES['foto']['size']<5*1024*1024){
            $fname='foto_'.time().'_'.rand(1000,9999).'.'.$ext;
            move_uploaded_file($_FILES['foto']['tmp_name'],'uploads/'.$fname);
            $foto=$fname;
        }
    }
    mysqli_query($conn,"INSERT INTO barang_temuan (id_kategori,id_lokasi,deskripsi,warna,merek,foto,bukti_rahasia,id_petugas,tgl_expired) VALUES ('$kat','$lok','$des','$warna','$merek','$foto','$buk','$petugas',$expired_sql)");
    logActivity($conn,'Tambah Barang',"Barang: $des");
    header("Location: kelola_barang.php?msg=added"); exit;
}
$kategori=mysqli_query($conn,"SELECT * FROM kategori ORDER BY nama_kategori");
$lokasi=mysqli_query($conn,"SELECT * FROM lokasi ORDER BY nama_lokasi");
ob_start();
?>
<div class="page-header">
  <div><h1><i class="fa-solid fa-plus-circle" style="color:var(--primary);margin-right:8px;"></i>Tambah Barang Temuan</h1><p>Input data barang temuan yang baru ditemukan</p></div>
  <a href="kelola_barang.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div style="max-width:760px;">
  <div class="card">
    <form method="POST" enctype="multipart/form-data" class="form-grid">
      <div class="form-row">
        <div class="form-group"><label>Kategori <span style="color:var(--red);">*</span></label>
          <select name="kategori" class="form-control" required>
            <option value="">-- Pilih --</option>
            <?php while($k=mysqli_fetch_assoc($kategori)): ?><option value="<?=$k['id_kategori']?>"><?=$k['nama_kategori']?></option><?php endwhile; ?>
          </select>
        </div>
        <div class="form-group"><label>Lokasi Temuan <span style="color:var(--red);">*</span></label>
          <select name="lokasi" class="form-control" required>
            <option value="">-- Pilih --</option>
            <?php while($l=mysqli_fetch_assoc($lokasi)): ?><option value="<?=$l['id_lokasi']?>"><?=$l['nama_lokasi']?></option><?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="form-group"><label>Deskripsi Barang <span style="color:var(--red);">*</span></label>
        <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: iPhone 14 Pro warna hitam, Dompet kulit coklat..." required>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Merek / Brand</label><input type="text" name="merek" class="form-control" placeholder="Contoh: Apple, Samsung, Gucci..."></div>
        <div class="form-group"><label>Warna</label><input type="text" name="warna" class="form-control" placeholder="Contoh: Hitam, Biru dongker..."></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Foto Barang</label>
          <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewFoto(this)">
          <span class="hint">JPG, PNG, WebP maks 5MB. Opsional.</span>
          <img id="foto-preview" src="" style="display:none;margin-top:8px;max-width:200px;border-radius:8px;border:1px solid var(--border);">
        </div>
        <div class="form-group"><label>Tanggal Kadaluarsa</label>
          <input type="date" name="tgl_expired" class="form-control" min="<?=date('Y-m-d')?>">
          <span class="hint">Kosongkan = otomatis 30 hari.</span>
        </div>
      </div>
      <hr>
      <div class="form-group">
        <label><i class="fa-solid fa-lock" style="color:var(--primary);"></i> Bukti Rahasia <span style="color:var(--red);">*</span></label>
        <input type="text" name="bukti" class="form-control" placeholder="IMEI / Nama pemilik / Ciri khas spesifik..." required>
        <span class="hint" style="color:var(--primary);"><i class="fa-solid fa-shield-halved"></i> Data ini tidak tampil ke publik. Hanya untuk verifikasi kepemilikan.</span>
      </div>
      <div style="display:flex;gap:12px;">
        <button type="submit" name="simpan" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Barang</button>
        <a href="kelola_barang.php" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
function previewFoto(input) {
    const img = document.getElementById('foto-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; img.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
<?php $content=ob_get_clean(); buildPage('Tambah Barang','tambah',$content); ?>
