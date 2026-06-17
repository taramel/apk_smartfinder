<?php
include 'koneksi.php'; include 'layout.php';
if(isLoggedIn()){header("Location: dashboard.php");exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $username=e($conn,$_POST['username']??'');
    $password=$_POST['password']??'';
    $q=mysqli_query($conn,"SELECT * FROM users WHERE username='$username' LIMIT 1");
    $user=mysqli_fetch_assoc($q);
    if($user && password_verify($password,$user['password'])){
        $_SESSION['user_id']=$user['id_user'];$_SESSION['username']=$user['username'];
        $_SESSION['nama_petugas']=$user['nama_petugas'];$_SESSION['role']=$user['role'];
        logActivity($conn,'Login','Login berhasil');
        header("Location: dashboard.php");exit;
    }else{$error='Username atau password salah.';}
}
renderHead('Login');
?>
<div style="min-height:100vh;background:var(--bg);display:flex;align-items:center;justify-content:center;padding:24px;">
  <div style="width:100%;max-width:420px;">
    <div style="text-align:center;margin-bottom:32px;">
      <div style="width:64px;height:64px;background:var(--primary);border-radius:18px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:28px;color:#fff;box-shadow:0 8px 24px rgba(26,107,74,.3);">
        <i class="fa-solid fa-magnifying-glass-location"></i>
      </div>
      <h1 style="font-family:'DM Serif Display',serif;font-size:26px;color:var(--text);">SmartFinder</h1>
      <p style="color:var(--text2);font-size:14px;margin-top:6px;">Panel Petugas Barang Temuan</p>
    </div>
    <?php if($error): ?><?=showAlert('error',$error)?><?php endif; ?>
    <div class="card" style="padding:28px;">
      <h2 style="font-size:18px;font-weight:700;margin-bottom:20px;">Masuk ke Akun</h2>
      <form method="POST" class="form-grid">
        <div class="form-group">
          <label>Username</label>
          <div class="input-group"><i class="fa-solid fa-user"></i><input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus></div>
        </div>
        <div class="form-group">
          <label>Password</label>
          <div class="input-group"><i class="fa-solid fa-lock"></i><input type="password" name="password" class="form-control" placeholder="Masukkan password" required></div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;font-size:15px;">
          <i class="fa-solid fa-right-to-bracket"></i> Masuk
        </button>
      </form>
      <div style="margin-top:16px;padding:12px 14px;background:var(--bg3);border-radius:8px;font-size:13px;color:var(--text2);text-align:center;">
        <i class="fa-solid fa-circle-info" style="color:var(--primary);"></i> Default: <b>admin</b> / <b>admin123</b>
      </div>
    </div>
    <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--text2);">
      <a href="index.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
    </p>
  </div>
</div>
<?php renderFoot(); ?>
