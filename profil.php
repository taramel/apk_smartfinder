<?php
include 'koneksi.php'; include 'layout.php'; requireLogin();
$uid = (int)$_SESSION['user_id'];
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nama = e($conn,$_POST['nama_petugas']);
    $email= e($conn,$_POST['email']??'');
    $hp   = e($conn,$_POST['no_hp']??'');
    $old  = $_POST['old_password']??'';
    $new  = $_POST['new_password']??'';
    $user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id_user=$uid"));
    if ($old && $new) {
        if (!password_verify($old,$user['password'])) { $msg='error:Password lama salah.'; }
        else {
            $hash = password_hash($new,PASSWORD_DEFAULT);
            mysqli_query($conn,"UPDATE users SET nama_petugas='$nama',email='$email',no_hp='$hp',password='$hash' WHERE id_user=$uid");
            $_SESSION['nama_petugas']=$nama;
            logActivity($conn,'Update Profil','Termasuk ganti password');
            $msg='success:Profil dan password berhasil diperbarui!';
        }
    } else {
        mysqli_query($conn,"UPDATE users SET nama_petugas='$nama',email='$email',no_hp='$hp' WHERE id_user=$uid");
        $_SESSION['nama_petugas']=$nama;
        logActivity($conn,'Update Profil','Update data profil');
        $msg='success:Profil berhasil diperbarui!';
    }
}
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id_user=$uid"));
ob_start();
?>
<div class="page-header">
  <div><h1><i class="fa-solid fa-user-circle" style="color:var(--primary);margin-right:8px;"></i>Profil Saya</h1><p>Kelola informasi akun Anda</p></div>
</div>
<?php if($msg): list($t,$m)=explode(':',$msg,2); echo showAlert($t,$m); endif; ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;max-width:860px;">
  <!-- Info Profil -->
  <div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;">Informasi Akun</h3>
    <form method="POST" class="form-grid">
      <div class="form-group"><label>Nama Lengkap</label><input type="text" name="nama_petugas" class="form-control" value="<?=htmlspecialchars($user['nama_petugas']??'')?>" required></div>
      <div class="form-group"><label>Username</label><input type="text" class="form-control" value="<?=htmlspecialchars($user['username'])?>" disabled style="background:var(--bg3);cursor:not-allowed;"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" value="<?=htmlspecialchars($user['email']??'')?>"></div>
      <div class="form-group"><label>No. HP</label><input type="text" name="no_hp" class="form-control" value="<?=htmlspecialchars($user['no_hp']??'')?>"></div>
      <div class="form-group"><label>Role</label>
        <div style="padding:10px 14px;background:var(--bg3);border-radius:8px;border:1.5px solid var(--border);"><span class="badge <?=$user['role']==='admin'?'b-admin':'b-petugas'?>"><?=$user['role']?></span></div>
      </div>
      <input type="hidden" name="old_password" value=""><input type="hidden" name="new_password" value="">
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
    </form>
  </div>
  <!-- Ganti Password -->
  <div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:16px;">Ganti Password</h3>
    <form method="POST" class="form-grid">
      <input type="hidden" name="nama_petugas" value="<?=htmlspecialchars($user['nama_petugas']??'')?>">
      <input type="hidden" name="email" value="<?=htmlspecialchars($user['email']??'')?>">
      <input type="hidden" name="no_hp" value="<?=htmlspecialchars($user['no_hp']??'')?>">
      <div class="form-group"><label>Password Lama</label><div class="input-group"><i class="fa-solid fa-lock"></i><input type="password" name="old_password" class="form-control" placeholder="Masukkan password lama"></div></div>
      <div class="form-group"><label>Password Baru</label><div class="input-group"><i class="fa-solid fa-lock"></i><input type="password" name="new_password" class="form-control" placeholder="Password baru minimal 6 karakter"></div></div>
      <div class="alert alert-info"><i class="fa-solid fa-circle-info"></i><span style="font-size:13px;">Kosongkan keduanya jika tidak ingin ganti password.</span></div>
      <button type="submit" class="btn btn-warning"><i class="fa-solid fa-key"></i> Ganti Password</button>
    </form>
    <hr>
    <div style="font-size:13px;color:var(--text2);">
      <div style="margin-bottom:6px;"><i class="fa-regular fa-calendar" style="color:var(--text3);width:16px;"></i> Bergabung: <b><?=date('d M Y',strtotime($user['created_at']))?></b></div>
    </div>
  </div>
</div>
<?php $content=ob_get_clean(); buildPage('Profil','profil',$content); ?>
