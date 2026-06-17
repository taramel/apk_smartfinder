<?php
include 'koneksi.php';include 'layout.php';requireAdmin();
$msg=$_GET['msg']??'';
if(!empty($_GET['del'])&&$_GET['del']!=$_SESSION['user_id']){
    $uid=(int)$_GET['del'];
    mysqli_query($conn,"DELETE FROM users WHERE id_user=$uid AND id_user!=".(int)$_SESSION['user_id']);
    logActivity($conn,'Hapus User',"User ID: $uid");
    header("Location: kelola_user.php?msg=deleted");exit;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nama=e($conn,$_POST['nama_petugas']);
    $username=e($conn,$_POST['username']);
    $email=e($conn,$_POST['email']??'');
    $no_hp=e($conn,$_POST['no_hp']??'');
    $role=e($conn,$_POST['role']);
    $password=$_POST['password']??'';
    $edit_id=(int)($_POST['edit_id']??0);
    if($edit_id){
        $set="nama_petugas='$nama',username='$username',email='$email',no_hp='$no_hp',role='$role'";
        if($password) $set.=",password='".password_hash($password,PASSWORD_DEFAULT)."'";
        mysqli_query($conn,"UPDATE users SET $set WHERE id_user=$edit_id");
        logActivity($conn,'Edit User',"User ID: $edit_id");
        header("Location: kelola_user.php?msg=updated");exit;
    }else{
        if(!$password){header("Location: kelola_user.php?msg=nopass");exit;}
        $hash=password_hash($password,PASSWORD_DEFAULT);
        $cek=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_user FROM users WHERE username='$username'"));
        if($cek){header("Location: kelola_user.php?msg=exists");exit;}
        mysqli_query($conn,"INSERT INTO users (username,password,nama_petugas,email,no_hp,role) VALUES ('$username','$hash','$nama','$email','$no_hp','$role')");
        logActivity($conn,'Tambah User',"Username: $username");
        header("Location: kelola_user.php?msg=added");exit;
    }
}
$edit_data=null;
if(!empty($_GET['edit'])){$eid=(int)$_GET['edit'];$edit_data=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id_user=$eid"));}
$users=mysqli_query($conn,"SELECT * FROM users ORDER BY created_at DESC");
ob_start();
?>
<div class="page-header">
  <div><h1>Manajemen User</h1><p>Kelola akun petugas SmartFinder</p></div>
</div>
<?php
$alerts=['added'=>['success','User berhasil ditambahkan!'],'updated'=>['success','User berhasil diperbarui.'],'deleted'=>['success','User berhasil dihapus.'],'nopass'=>['error','Password tidak boleh kosong untuk user baru.'],'exists'=>['error','Username sudah digunakan.']];
if($msg&&isset($alerts[$msg])) echo showAlert($alerts[$msg][0],$alerts[$msg][1]);
?>
<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">
  <!-- Tabel User -->
  <div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Nama Petugas</th><th>Username</th><th>Email</th><th>Role</th><th>Tgl Daftar</th><th style="text-align:center;">Aksi</th></tr></thead>
        <tbody>
        <?php while($u=mysqli_fetch_assoc($users)):?>
        <tr>
          <td style="font-weight:600;"><?=htmlspecialchars($u['nama_petugas'])?></td>
          <td style="font-family:monospace;font-size:13px;color:var(--text2);"><?=htmlspecialchars($u['username'])?></td>
          <td style="font-size:13px;color:var(--text2);"><?=htmlspecialchars($u['email']??'-')?></td>
          <td><span class="badge <?=$u['role']==='admin'?'b-admin':'b-petugas'?>"><?=$u['role']?></span></td>
          <td style="font-size:12px;color:var(--text3);"><?=date('d M Y',strtotime($u['created_at']))?></td>
          <td>
            <div style="display:flex;gap:6px;justify-content:center;">
              <a href="kelola_user.php?edit=<?=$u['id_user']?>" class="btn btn-secondary btn-icon"><i class="fa-solid fa-pen"></i></a>
              <?php if($u['id_user']!=$_SESSION['user_id']):?>
              <a href="kelola_user.php?del=<?=$u['id_user']?>" class="btn btn-danger btn-icon" onclick="return confirm('Hapus user ini?')"><i class="fa-solid fa-trash"></i></a>
              <?php endif;?>
            </div>
          </td>
        </tr>
        <?php endwhile;?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Form Tambah/Edit -->
  <div class="card">
    <h3 style="font-size:16px;font-weight:700;margin-bottom:16px;"><?=$edit_data?'Edit User':'Tambah User Baru'?></h3>
    <form method="POST" class="form-grid">
      <?php if($edit_data):?><input type="hidden" name="edit_id" value="<?=$edit_data['id_user']?>"><?php endif;?>
      <div class="form-group"><label>Nama Lengkap <span style="color:var(--red);">*</span></label>
        <input type="text" name="nama_petugas" class="form-control" value="<?=htmlspecialchars($edit_data['nama_petugas']??'')?>" required>
      </div>
      <div class="form-group"><label>Username <span style="color:var(--red);">*</span></label>
        <input type="text" name="username" class="form-control" value="<?=htmlspecialchars($edit_data['username']??'')?>" required>
      </div>
      <div class="form-group"><label>Email</label>
        <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($edit_data['email']??'')?>">
      </div>
      <div class="form-group"><label>No. HP</label>
        <input type="text" name="no_hp" class="form-control" value="<?=htmlspecialchars($edit_data['no_hp']??'')?>">
      </div>
      <div class="form-group"><label>Role <span style="color:var(--red);">*</span></label>
        <select name="role" class="form-control">
          <option value="petugas" <?=(!$edit_data||$edit_data['role']==='petugas')?'selected':''?>>Petugas</option>
          <option value="admin" <?=($edit_data&&$edit_data['role']==='admin')?'selected':''?>>Admin</option>
        </select>
      </div>
      <div class="form-group"><label>Password <?=$edit_data?'<span style="color:var(--text3);font-weight:400;">(kosongkan jika tidak diganti)</span>':''?></label>
        <input type="password" name="password" class="form-control" placeholder="<?=$edit_data?'Kosongkan jika tidak diganti':'Password baru'?>">
      </div>
      <div style="display:flex;gap:8px;">
        <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;"><i class="fa-solid fa-floppy-disk"></i> <?=$edit_data?'Update':'Tambah'?></button>
        <?php if($edit_data):?><a href="kelola_user.php" class="btn btn-secondary">Batal</a><?php endif;?>
      </div>
    </form>
  </div>
</div>
<?php $content=ob_get_clean();buildPage('Kelola User','user',$content);?>
