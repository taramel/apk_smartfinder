<?php
include 'koneksi.php';include 'layout.php';requireLogin();
$bulan=$_GET['bulan']??date('Y-m');
$bulan_sql=date('Y-m',strtotime($bulan.'-01'));
$y=substr($bulan_sql,0,4);$m=substr($bulan_sql,5,2);
$total_bulan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM barang_temuan WHERE YEAR(tgl_temu)='$y' AND MONTH(tgl_temu)='$m'"))['c'];
$claimed_bulan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM barang_temuan WHERE status='Claimed' AND YEAR(tgl_temu)='$y' AND MONTH(tgl_temu)='$m'"))['c'];
$klaim_bulan=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM klaim WHERE YEAR(tgl_klaim)='$y' AND MONTH(tgl_klaim)='$m'"))['c'];
$per_kat=mysqli_query($conn,"SELECT k.nama_kategori,COUNT(*) AS total,SUM(b.status='Claimed') AS claimed FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m' GROUP BY k.id_kategori ORDER BY total DESC");
$per_lok=mysqli_query($conn,"SELECT l.nama_lokasi,COUNT(*) AS total FROM barang_temuan b JOIN lokasi l ON b.id_lokasi=l.id_lokasi WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m' GROUP BY l.id_lokasi ORDER BY total DESC");
$detail=mysqli_query($conn,"SELECT b.*,k.nama_kategori,l.nama_lokasi FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori JOIN lokasi l ON b.id_lokasi=l.id_lokasi WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m' ORDER BY b.tgl_temu DESC");
$pct=$total_bulan>0?round(($claimed_bulan/$total_bulan)*100):0;
ob_start();
?>
<div class="page-header">
  <div><h1><i class="fa-solid fa-chart-bar" style="color:var(--primary);margin-right:8px;"></i>Laporan Bulanan</h1><p>Rekap data barang temuan per bulan</p></div>
  <div style="display:flex;gap:10px;align-items:center;">
    <form method="GET" style="display:flex;gap:8px;align-items:center;">
      <input type="month" name="bulan" value="<?=$bulan?>" class="form-control" style="width:auto;">
      <button class="btn btn-primary btn-sm"><i class="fa-solid fa-search"></i> Tampilkan</button>
    </form>
    <a href="cetak_laporan.php?bulan=<?=$bulan?>&format=pdf" target="_blank" class="btn btn-secondary btn-sm"><i class="fa-solid fa-file-pdf" style="color:var(--red);"></i> Cetak PDF</a>
    <a href="cetak_laporan.php?bulan=<?=$bulan?>&format=excel" class="btn btn-secondary btn-sm"><i class="fa-solid fa-file-excel" style="color:var(--green);"></i> Export Excel</a>
  </div>
</div>

<div style="background:var(--primary);color:#fff;border-radius:12px;padding:20px 24px;margin-bottom:24px;">
  <div style="font-size:13px;opacity:.8;margin-bottom:4px;font-weight:500;text-transform:uppercase;letter-spacing:.05em;">Periode Laporan</div>
  <div style="font-family:'DM Serif Display',serif;font-size:24px;"><?=date('F Y',strtotime($bulan.'-01'))?></div>
</div>

<div class="stats-grid" style="margin-bottom:24px;">
  <div class="stat-card"><div class="stat-icon si-primary"><i class="fa-solid fa-boxes-stacked"></i></div><div><div class="stat-val"><?=$total_bulan?></div><div class="stat-label">Total Barang Masuk</div></div></div>
  <div class="stat-card"><div class="stat-icon si-green"><i class="fa-solid fa-check-circle"></i></div><div><div class="stat-val"><?=$claimed_bulan?></div><div class="stat-label">Berhasil Diklaim</div></div></div>
  <div class="stat-card"><div class="stat-icon si-yellow"><i class="fa-solid fa-clipboard-list"></i></div><div><div class="stat-val"><?=$klaim_bulan?></div><div class="stat-label">Total Pengajuan Klaim</div></div></div>
  <div class="stat-card"><div class="stat-icon si-blue"><i class="fa-solid fa-percent"></i></div><div><div class="stat-val"><?=$pct?>%</div><div class="stat-label">Tingkat Keberhasilan</div></div></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:24px;">
  <div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:14px;"><i class="fa-solid fa-tags" style="color:var(--primary);margin-right:6px;"></i>Per Kategori</h3>
    <?php $rows=mysqli_fetch_all($per_kat,MYSQLI_ASSOC);$max=max(array_column($rows,'total')?:[1]);foreach($rows as $pk):$pct2=$max>0?round(($pk['total']/$max)*100):0;?>
    <div style="margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
        <span style="font-weight:500;"><?=$pk['nama_kategori']?></span>
        <span style="color:var(--text2);"><?=$pk['total']?> barang &bull; <?=$pk['claimed']?> diklaim</span>
      </div>
      <div style="height:6px;background:var(--bg3);border-radius:99px;"><div style="height:6px;background:var(--primary);border-radius:99px;width:<?=$pct2?>%;"></div></div>
    </div>
    <?php endforeach;if(!$rows):?><p style="color:var(--text3);font-size:13px;text-align:center;">Tidak ada data</p><?php endif;?>
  </div>
  <div class="card">
    <h3 style="font-size:15px;font-weight:700;margin-bottom:14px;"><i class="fa-solid fa-location-dot" style="color:var(--primary);margin-right:6px;"></i>Per Lokasi</h3>
    <?php $rowsl=mysqli_fetch_all($per_lok,MYSQLI_ASSOC);$maxl=max(array_column($rowsl,'total')?:[1]);foreach($rowsl as $pl):$pctl=$maxl>0?round(($pl['total']/$maxl)*100):0;?>
    <div style="margin-bottom:12px;">
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px;">
        <span style="font-weight:500;"><?=$pl['nama_lokasi']?></span>
        <span style="color:var(--text2);"><?=$pl['total']?> barang</span>
      </div>
      <div style="height:6px;background:var(--bg3);border-radius:99px;"><div style="height:6px;background:var(--accent);border-radius:99px;width:<?=$pctl?>%;"></div></div>
    </div>
    <?php endforeach;if(!$rowsl):?><p style="color:var(--text3);font-size:13px;text-align:center;">Tidak ada data</p><?php endif;?>
  </div>
</div>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
  <h3 style="font-size:15px;font-weight:700;">Detail Barang Bulan Ini</h3>
</div>
<div class="table-wrap">
  <table>
    <thead><tr><th>ID</th><th>Deskripsi</th><th>Kategori</th><th>Lokasi</th><th>Tgl Temuan</th><th>Status</th></tr></thead>
    <tbody>
    <?php $cnt=0;while($r=mysqli_fetch_assoc($detail)):$cnt++;?>
    <tr>
      <td style="font-size:12px;color:var(--text3);">#<?=$r['id_barang']?></td>
      <td style="font-weight:600;"><?=htmlspecialchars($r['deskripsi'])?></td>
      <td><?=$r['nama_kategori']?></td><td><?=$r['nama_lokasi']?></td>
      <td style="font-size:12px;color:var(--text2);"><?=date('d M Y',strtotime($r['tgl_temu']))?></td>
      <td><span class="badge b-<?=strtolower($r['status'])?>"><?=$r['status']?></span></td>
    </tr>
    <?php endwhile;if($cnt===0):?><tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text3);">Tidak ada data untuk periode ini</td></tr><?php endif;?>
    </tbody>
  </table>
</div>
<?php $content=ob_get_clean();buildPage('Laporan','laporan',$content);?>
