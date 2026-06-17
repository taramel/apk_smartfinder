<?php
include 'koneksi.php'; include 'layout.php';
requireLogin();
$st=mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(status='Unclaimed') AS unc,SUM(status='Claimed') AS clm,SUM(status='Expired') AS exp,COUNT(*) AS tot FROM barang_temuan"));
$klaim_pending=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM klaim WHERE status_klaim='Pending'"))['c'];
$klaim_today=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM klaim WHERE DATE(tgl_klaim)=CURDATE()"))['c'];
$barang_today=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM barang_temuan WHERE DATE(tgl_temu)=CURDATE()"))['c'];
$recent=mysqli_query($conn,"SELECT b.*,k.nama_kategori,l.nama_lokasi FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori JOIN lokasi l ON b.id_lokasi=l.id_lokasi ORDER BY b.tgl_temu DESC LIMIT 8");
$recent_klaim=mysqli_query($conn,"SELECT kl.*,b.deskripsi,p.nama_pencari FROM klaim kl JOIN barang_temuan b ON kl.id_barang=b.id_barang JOIN pencari p ON kl.id_pencari=p.id_pencari ORDER BY kl.tgl_klaim DESC LIMIT 5");
$per_kat=mysqli_query($conn,"SELECT k.nama_kategori,COUNT(*) AS total,SUM(b.status='Claimed') AS claimed FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori GROUP BY k.id_kategori");
ob_start();
?>
<div class="page-header">
  <div><h1>Dashboard</h1><p>Selamat datang kembali, <b><?=$_SESSION['nama_petugas']?></b></p></div>
  <a href="tambah_barang.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Barang</a>
</div>

<!-- Stats -->
<div class="stats-grid">
  <div class="stat-card"><div class="stat-icon si-primary"><i class="fa-solid fa-boxes-stacked"></i></div><div><div class="stat-val"><?=$st['tot']?></div><div class="stat-label">Total Barang</div></div></div>
  <div class="stat-card"><div class="stat-icon si-yellow"><i class="fa-solid fa-clock"></i></div><div><div class="stat-val"><?=$st['unc']?></div><div class="stat-label">Belum Diklaim</div></div></div>
  <div class="stat-card"><div class="stat-icon si-green"><i class="fa-solid fa-check-circle"></i></div><div><div class="stat-val"><?=$st['clm']?></div><div class="stat-label">Sudah Diklaim</div></div></div>
  <div class="stat-card"><div class="stat-icon si-red"><i class="fa-solid fa-hourglass"></i></div><div><div class="stat-val"><?=$klaim_pending?></div><div class="stat-label">Klaim Pending</div></div></div>
  <div class="stat-card"><div class="stat-icon si-blue"><i class="fa-solid fa-calendar-day"></i></div><div><div class="stat-val"><?=$barang_today?></div><div class="stat-label">Masuk Hari Ini</div></div></div>
  <div class="stat-card"><div class="stat-icon si-yellow"><i class="fa-solid fa-bell"></i></div><div><div class="stat-val"><?=$klaim_today?></div><div class="stat-label">Klaim Hari Ini</div></div></div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;">
  <!-- Recent Barang -->
  <div>
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
      <h2 style="font-size:16px;font-weight:700;">Barang Terbaru</h2>
      <a href="kelola_barang.php" class="btn btn-secondary btn-sm">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Deskripsi</th><th>Kategori</th><th>Lokasi</th><th>Status</th><th>Tgl</th></tr></thead>
        <tbody>
        <?php while($r=mysqli_fetch_assoc($recent)): ?>
        <tr>
          <td style="font-weight:600;"><?=htmlspecialchars($r['deskripsi'])?></td>
          <td style="color:var(--text2);"><?=$r['nama_kategori']?></td>
          <td style="color:var(--text2);"><?=$r['nama_lokasi']?></td>
          <td><span class="badge b-<?=strtolower($r['status'])?>"><?=$r['status']?></span></td>
          <td style="color:var(--text3);font-size:12px;"><?=date('d/m/Y',strtotime($r['tgl_temu']))?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Right column -->
  <div style="display:flex;flex-direction:column;gap:20px;">
    <!-- Per Kategori -->
    <div>
      <h2 style="font-size:16px;font-weight:700;margin-bottom:14px;">Per Kategori</h2>
      <div class="card" style="display:flex;flex-direction:column;gap:14px;">
        <?php while($pk=mysqli_fetch_assoc($per_kat)):
          $pct=$st['tot']>0?round(($pk['total']/$st['tot'])*100):0;
        ?>
        <div>
          <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px;">
            <span style="font-weight:500;"><?=$pk['nama_kategori']?></span>
            <span style="color:var(--text2);"><?=$pk['total']?> <span style="color:var(--text3);">(<small><?=$pk['claimed']?> claimed</small>)</span></span>
          </div>
          <div style="height:5px;background:var(--bg3);border-radius:99px;"><div style="height:5px;background:var(--primary);border-radius:99px;width:<?=$pct?>%;transition:width .5s;"></div></div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>

    <!-- Klaim Terbaru -->
    <div>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <h2 style="font-size:16px;font-weight:700;">Klaim Terbaru</h2>
        <?php if($klaim_pending>0): ?>
        <span class="badge b-pending"><?=$klaim_pending?> pending</span>
        <?php endif; ?>
      </div>
      <div class="card" style="display:flex;flex-direction:column;gap:0;padding:0;overflow:hidden;">
        <?php $cnt=0; while($rk=mysqli_fetch_assoc($recent_klaim)):$cnt++; ?>
        <div style="padding:12px 16px;<?=$cnt>1?'border-top:1px solid var(--border);':''?>">
          <div style="font-weight:600;font-size:14px;"><?=htmlspecialchars($rk['deskripsi'])?></div>
          <div style="font-size:13px;color:var(--text2);margin-top:2px;display:flex;align-items:center;justify-content:space-between;">
            <span><?=htmlspecialchars($rk['nama_pencari'])?></span>
            <span class="badge b-<?=strtolower($rk['status_klaim'])?>" style="font-size:10px;"><?=$rk['status_klaim']?></span>
          </div>
        </div>
        <?php endwhile; ?>
        <?php if($cnt===0): ?><div style="padding:20px;text-align:center;color:var(--text3);font-size:13px;">Belum ada klaim</div><?php endif; ?>
        <div style="padding:12px 16px;border-top:1px solid var(--border);"><a href="kelola_klaim.php" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">Kelola Semua Klaim</a></div>
      </div>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
buildPage('Dashboard','dashboard',$content);
