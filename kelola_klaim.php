<?php
include 'koneksi.php';include 'layout.php';requireLogin();
if(!empty($_GET['action'])&&!empty($_GET['id'])){
    $kid=(int)$_GET['id'];$action=$_GET['action'];
    if($action==='approve'){
        $klaim=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM klaim WHERE id_klaim=$kid"));
        mysqli_query($conn,"UPDATE klaim SET status_klaim='Valid' WHERE id_klaim=$kid");
        mysqli_query($conn,"UPDATE barang_temuan SET status='Claimed' WHERE id_barang=".$klaim['id_barang']);
        $uid=(int)$_SESSION['user_id'];
        mysqli_query($conn,"INSERT INTO verifikasi (id_klaim,id_user,status_verifikasi) VALUES ('$kid','$uid','Valid')");
        logActivity($conn,'Approve Klaim',"Klaim ID: $kid");
    }elseif($action==='reject'){
        mysqli_query($conn,"UPDATE klaim SET status_klaim='Invalid' WHERE id_klaim=$kid");
        $uid=(int)$_SESSION['user_id'];
        mysqli_query($conn,"INSERT INTO verifikasi (id_klaim,id_user,status_verifikasi) VALUES ('$kid','$uid','Invalid')");
        logActivity($conn,'Reject Klaim',"Klaim ID: $kid");
    }
    header("Location: kelola_klaim.php?msg=updated");exit;
}
$msg=$_GET['msg']??'';
$filter=e($conn,$_GET['filter']??'');
$where=$filter?"WHERE kl.status_klaim='$filter'":'';
$result=mysqli_query($conn,"SELECT kl.*,b.deskripsi,b.id_barang,p.nama_pencari,p.identitas_pencari,p.no_hp,p.email FROM klaim kl JOIN barang_temuan b ON kl.id_barang=b.id_barang JOIN pencari p ON kl.id_pencari=p.id_pencari $where ORDER BY kl.tgl_klaim DESC");
$counts=mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(status_klaim='Pending') AS pending,SUM(status_klaim='Valid') AS valid,SUM(status_klaim='Invalid') AS invalid,COUNT(*) AS total FROM klaim"));
ob_start();
?>
<div class="page-header">
  <div><h1>Manajemen Klaim</h1><p>Verifikasi dan kelola permintaan klaim barang temuan</p></div>
</div>
<?php if($msg==='updated'):?><?=showAlert('success','Status klaim berhasil diperbarui!')?><?php endif;?>
<div style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
  <a href="kelola_klaim.php" class="btn <?=!$filter?'btn-primary':'btn-secondary'?> btn-sm">Semua <span style="opacity:.7;">(<?=$counts['total']?>)</span></a>
  <a href="kelola_klaim.php?filter=Pending" class="btn <?=$filter==='Pending'?'btn-warning':'btn-secondary'?> btn-sm"><i class="fa-solid fa-clock"></i> Pending <span style="opacity:.7;">(<?=$counts['pending']?>)</span></a>
  <a href="kelola_klaim.php?filter=Valid" class="btn <?=$filter==='Valid'?'btn-success':'btn-secondary'?> btn-sm"><i class="fa-solid fa-check"></i> Valid <span style="opacity:.7;">(<?=$counts['valid']?>)</span></a>
  <a href="kelola_klaim.php?filter=Invalid" class="btn <?=$filter==='Invalid'?'btn-danger':'btn-secondary'?> btn-sm"><i class="fa-solid fa-xmark"></i> Invalid <span style="opacity:.7;">(<?=$counts['invalid']?>)</span></a>
</div>
<div class="table-wrap">
  <table>
    <thead><tr><th>ID</th><th>Barang</th><th>Pemohon</th><th>Identitas</th><th>Kontak</th><th>Tgl Klaim</th><th>Status</th><th style="text-align:center;">Aksi</th></tr></thead>
    <tbody>
    <?php $cnt=0;while($r=mysqli_fetch_assoc($result)):$cnt++;?>
    <tr>
      <td style="font-size:12px;color:var(--text3);font-weight:600;">#<?=$r['id_klaim']?></td>
      <td><a href="klaim.php?id=<?=$r['id_barang']?>" style="font-weight:600;color:var(--text);"><?=htmlspecialchars($r['deskripsi'])?></a></td>
      <td style="font-weight:600;"><?=htmlspecialchars($r['nama_pencari'])?></td>
      <td style="font-size:13px;color:var(--text2);"><?=htmlspecialchars($r['identitas_pencari'])?></td>
      <td style="font-size:12px;color:var(--text2);"><?=htmlspecialchars($r['no_hp']??'-')?><?php if($r['email']):?><br><span style="color:var(--text3);"><?=htmlspecialchars($r['email'])?></span><?php endif;?></td>
      <td style="font-size:12px;color:var(--text2);"><?=date('d M Y H:i',strtotime($r['tgl_klaim']))?></td>
      <td><span class="badge b-<?=strtolower($r['status_klaim'])?>"><?=$r['status_klaim']?></span></td>
      <td>
        <?php if($r['status_klaim']==='Pending'):?>
        <div style="display:flex;gap:6px;justify-content:center;">
          <a href="kelola_klaim.php?action=approve&id=<?=$r['id_klaim']?>" class="btn btn-success btn-sm" onclick="return confirm('Setujui klaim ini?')"><i class="fa-solid fa-check"></i> Setujui</a>
          <a href="kelola_klaim.php?action=reject&id=<?=$r['id_klaim']?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak klaim ini?')"><i class="fa-solid fa-xmark"></i> Tolak</a>
        </div>
        <?php else:?><span style="font-size:12px;color:var(--text3);">—</span><?php endif;?>
      </td>
    </tr>
    <?php endwhile;?>
    <?php if($cnt===0):?><tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text2);"><i class="fa-solid fa-clipboard" style="font-size:32px;display:block;margin-bottom:8px;color:var(--border2);"></i>Tidak ada data klaim</td></tr><?php endif;?>
    </tbody>
  </table>
</div>
<?php $content=ob_get_clean();buildPage('Kelola Klaim','klaim',$content);?>
