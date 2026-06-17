<?php
include 'koneksi.php'; include 'layout.php'; requireLogin();
$id=(int)($_GET['id']??0);
$d=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM barang_temuan WHERE id_barang=$id"));
if(!$d){header("Location: kelola_barang.php");exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $kat=(int)$_POST['kategori'];$lok=(int)$_POST['lokasi'];
    $des=e($conn,$_POST['deskripsi']);$warna=e($conn,$_POST['warna']??'');
    $merek=e($conn,$_POST['merek']??'');$buk=e($conn,$_POST['bukti']);
    $st=e($conn,$_POST['status']);$foto=$d['foto'];
    if(!empty($_FILES['foto']['name'])&&$_FILES['foto']['error']===0){
        $ext=strtolower(pathinfo($_FILES['foto']['name'],PATHINFO_EXTENSION));
        if(in_array($ext,['jpg','jpeg','png','webp'])&&$_FILES['foto']['size']<5*1024*1024){
            if($foto&&file_exists('uploads/'.$foto))unlink('uploads/'.$foto);
            $fname='foto_'.time().'_'.rand(1000,9999).'.'.$ext;
            move_uploaded_file($_FILES['foto']['tmp_name'],'uploads/'.$fname);
            $foto=$fname;
        }
    }
    mysqli_query($conn,"UPDATE barang_temuan SET id_kategori='$kat',id_lokasi='$lok',deskripsi='$des',warna='$warna',merek='$merek',foto='$foto',bukti_rahasia='$buk',status='$st' WHERE id_barang=$id");
    logActivity($conn,'Edit Barang',"ID:$id $des");
    header("Location: kelola_barang.php?msg=updated");exit;
}
$kategori=mysqli_query($conn,"SELECT * FROM kategori ORDER BY nama_kategori");
$lokasi=mysqli_query($conn,"SELECT * FROM lokasi ORDER BY nama_lokasi");
ob_start();
?>
<div class="page-header">
  <div><h1>Edit Barang <span style="color:var(--text3);font-size:18px;">#<?=$id?></span></h1></div>
  <a href="kelola_barang.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
</div>
<div style="max-width:760px;"><div class="card">
  <form method="POST" enctype="multipart/form-data" class="form-grid">
    <div class="form-row">
      <div class="form-group"><label>Kategori</label>
        <select name="kategori" class="form-control" required>
          <?php while($k=mysqli_fetch_assoc($kategori)):?><option value="<?=$k['id_kategori']?>" <?=$d['id_kategori']==$k['id_kategori']?'selected':''?>><?=$k['nama_kategori']?></option><?php endwhile;?>
        </select>
      </div>
      <div class="form-group"><label>Lokasi</label>
        <select name="lokasi" class="form-control" required>
          <?php while($l=mysqli_fetch_assoc($lokasi)):?><option value="<?=$l['id_lokasi']?>" <?=$d['id_lokasi']==$l['id_lokasi']?'selected':''?>><?=$l['nama_lokasi']?></option><?php endwhile;?>
        </select>
      </div>
    </div>
    <div class="form-group"><label>Deskripsi</label><input type="text" name="deskripsi" class="form-control" value="<?=htmlspecialchars($d['deskripsi'])?>" required></div>
    <div class="form-row">
      <div class="form-group"><label>Merek</label><input type="text" name="merek" class="form-control" value="<?=htmlspecialchars($d['merek']??'')?>"></div>
      <div class="form-group"><label>Warna</label><input type="text" name="warna" class="form-control" value="<?=htmlspecialchars($d['warna']??'')?>"></div>
    </div>
    <div class="form-row">
      <div class="form-group"><label>Ganti Foto</label>
        <input type="file" name="foto" class="form-control" accept="image/*">
        <?php if($d['foto']&&file_exists('uploads/'.$d['foto'])):?>
        <img src="uploads/<?=htmlspecialchars($d['foto'])?>" style="margin-top:8px;max-width:160px;border-radius:8px;border:1px solid var(--border);">
        <?php endif;?>
      </div>
      <div class="form-group"><label>Status</label>
        <select name="status" class="form-control">
          <option value="Unclaimed" <?=$d['status']==='Unclaimed'?'selected':''?>>Unclaimed</option>
          <option value="Claimed" <?=$d['status']==='Claimed'?'selected':''?>>Claimed</option>
          <option value="Expired" <?=$d['status']==='Expired'?'selected':''?>>Expired</option>
        </select>
      </div>
    </div>
    <div class="form-group"><label>Bukti Rahasia</label><input type="text" name="bukti" class="form-control" value="<?=htmlspecialchars($d['bukti_rahasia']??'')?>"></div>
    <div style="display:flex;gap:12px;">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
      <a href="kelola_barang.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div></div>
<?php $content=ob_get_clean();buildPage('Edit Barang','barang',$content);?>
