<?php
include 'koneksi.php'; include 'layout.php'; requireAdmin();
$per=20; $page=max(1,(int)($_GET['page']??1)); $offset=($page-1)*$per;
$total=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM activity_log"))['c'];
$pages=ceil($total/$per);
$logs=mysqli_query($conn,"SELECT al.*,u.nama_petugas,u.username FROM activity_log al LEFT JOIN users u ON al.id_user=u.id_user ORDER BY al.created_at DESC LIMIT $per OFFSET $offset");
ob_start();
?>
<div class="page-header">
  <div><h1><i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);margin-right:8px;"></i>Log Aktivitas</h1><p>Riwayat semua aktivitas petugas di sistem</p></div>
</div>
<div class="table-wrap">
  <table>
    <thead><tr><th>Waktu</th><th>Petugas</th><th>Aksi</th><th>Detail</th><th>IP Address</th></tr></thead>
    <tbody>
    <?php while($l=mysqli_fetch_assoc($logs)): ?>
    <tr>
      <td style="font-size:12px;color:var(--text2);white-space:nowrap;"><?=date('d M Y H:i:s',strtotime($l['created_at']))?></td>
      <td><div style="font-weight:600;font-size:13px;"><?=htmlspecialchars($l['nama_petugas']??'—')?></div><div style="font-size:11px;color:var(--text3);"><?=htmlspecialchars($l['username']??'')?></div></td>
      <td><span style="background:var(--primary-light);color:var(--primary);padding:3px 10px;border-radius:6px;font-size:12px;font-weight:600;"><?=htmlspecialchars($l['aksi'])?></span></td>
      <td style="font-size:13px;color:var(--text2);"><?=htmlspecialchars($l['detail'])?></td>
      <td style="font-size:12px;color:var(--text3);font-family:monospace;"><?=htmlspecialchars($l['ip_address'])?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php if($pages>1): ?>
<div class="pagination">
  <?php if($page>1): ?><a href="?page=<?=$page-1?>"><i class="fa-solid fa-chevron-left"></i></a><?php endif; ?>
  <?php for($i=1;$i<=$pages;$i++): ?><a href="?page=<?=$i?>" class="<?=$i==$page?'pg-active':''?>"><?=$i?></a><?php endfor; ?>
  <?php if($page<$pages): ?><a href="?page=<?=$page+1?>"><i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
</div>
<?php endif; ?>
<?php $content=ob_get_clean(); buildPage('Log Aktivitas','log',$content); ?>
