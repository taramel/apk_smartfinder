<?php
include 'koneksi.php';
requireLogin();

$bulan  = $_GET['bulan']  ?? date('Y-m');
$format = $_GET['format'] ?? 'pdf';
$y = substr($bulan,0,4);
$m = substr($bulan,5,2);
$bulan_label = date('F Y', strtotime($bulan.'-01'));

// Ambil data
$st = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) AS total,
            SUM(status='Unclaimed') AS unclaimed,
            SUM(status='Claimed') AS claimed,
            SUM(status='Expired') AS expired
     FROM barang_temuan
     WHERE YEAR(tgl_temu)='$y' AND MONTH(tgl_temu)='$m'"));

$per_kat = mysqli_fetch_all(mysqli_query($conn,
    "SELECT k.nama_kategori, COUNT(*) AS total, SUM(b.status='Claimed') AS claimed
     FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori
     WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m'
     GROUP BY k.id_kategori ORDER BY total DESC"), MYSQLI_ASSOC);

$per_lok = mysqli_fetch_all(mysqli_query($conn,
    "SELECT l.nama_lokasi, COUNT(*) AS total
     FROM barang_temuan b JOIN lokasi l ON b.id_lokasi=l.id_lokasi
     WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m'
     GROUP BY l.id_lokasi ORDER BY total DESC"), MYSQLI_ASSOC);

$detail = mysqli_fetch_all(mysqli_query($conn,
    "SELECT b.id_barang,b.deskripsi,b.merek,b.warna,b.tgl_temu,b.status,
            k.nama_kategori,l.nama_lokasi
     FROM barang_temuan b
     JOIN kategori k ON b.id_kategori=k.id_kategori
     JOIN lokasi l ON b.id_lokasi=l.id_lokasi
     WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m'
     ORDER BY b.tgl_temu DESC"), MYSQLI_ASSOC);

$klaim_data = mysqli_fetch_all(mysqli_query($conn,
    "SELECT kl.tgl_klaim,kl.status_klaim,b.deskripsi,p.nama_pencari,p.identitas_pencari
     FROM klaim kl
     JOIN barang_temuan b ON kl.id_barang=b.id_barang
     JOIN pencari p ON kl.id_pencari=p.id_pencari
     WHERE YEAR(kl.tgl_klaim)='$y' AND MONTH(kl.tgl_klaim)='$m'
     ORDER BY kl.tgl_klaim DESC"), MYSQLI_ASSOC);

$pct = $st['total'] > 0 ? round(($st['claimed']/$st['total'])*100) : 0;
$nama_petugas = $_SESSION['nama_petugas'] ?? '-';
$tgl_cetak = date('d F Y H:i');

// ── EXCEL (CSV) ──────────────────────────────────────────
if ($format === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="laporan_smartfinder_'.$bulan.'.csv"');
    $out = fopen('php://output','w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));

    fputcsv($out, ['LAPORAN BARANG TEMUAN - SMARTFINDER']);
    fputcsv($out, ['Periode', $bulan_label]);
    fputcsv($out, ['Dicetak oleh', $nama_petugas]);
    fputcsv($out, ['Tanggal Cetak', $tgl_cetak]);
    fputcsv($out, []);

    fputcsv($out, ['RINGKASAN']);
    fputcsv($out, ['Total Barang', $st['total']]);
    fputcsv($out, ['Belum Diklaim', $st['unclaimed']]);
    fputcsv($out, ['Sudah Diklaim', $st['claimed']]);
    fputcsv($out, ['Expired', $st['expired']]);
    fputcsv($out, ['Tingkat Klaim', $pct.'%']);
    fputcsv($out, []);

    fputcsv($out, ['PER KATEGORI']);
    fputcsv($out, ['Kategori','Total','Diklaim']);
    foreach ($per_kat as $r) fputcsv($out, [$r['nama_kategori'],$r['total'],$r['claimed']]);
    fputcsv($out, []);

    fputcsv($out, ['PER LOKASI']);
    fputcsv($out, ['Lokasi','Total Barang']);
    foreach ($per_lok as $r) fputcsv($out, [$r['nama_lokasi'],$r['total']]);
    fputcsv($out, []);

    fputcsv($out, ['DETAIL BARANG TEMUAN']);
    fputcsv($out, ['ID','Deskripsi','Merek','Warna','Kategori','Lokasi','Tgl Temuan','Status']);
    foreach ($detail as $r) {
        fputcsv($out, [
            '#'.$r['id_barang'],
            $r['deskripsi'], $r['merek'], $r['warna'],
            $r['nama_kategori'], $r['nama_lokasi'],
            date('d/m/Y', strtotime($r['tgl_temu'])),
            $r['status']
        ]);
    }
    fputcsv($out, []);

    fputcsv($out, ['RIWAYAT KLAIM']);
    fputcsv($out, ['Tgl Klaim','Barang','Pemohon','Identitas','Status']);
    foreach ($klaim_data as $r) {
        fputcsv($out, [
            date('d/m/Y H:i', strtotime($r['tgl_klaim'])),
            $r['deskripsi'], $r['nama_pencari'], $r['identitas_pencari'], $r['status_klaim']
        ]);
    }
    fclose($out);
    exit;
}

// ── PDF (HTML print) ─────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan SmartFinder — <?= $bulan_label ?></title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1f2e; background: #fff; }

/* PRINT BUTTON BAR */
.print-bar {
  position: fixed; top: 0; left: 0; right: 0; z-index: 999;
  background: #1a6b4a; color: #fff; padding: 10px 24px;
  display: flex; align-items: center; gap: 12px;
}
.print-bar span { flex: 1; font-weight: 700; font-size: 14px; }
.print-bar button {
  background: #fff; color: #1a6b4a; border: none; padding: 7px 18px;
  border-radius: 6px; font-weight: 700; cursor: pointer; font-size: 13px;
}
.print-bar a { color: #fff; font-size: 13px; text-decoration: none; }

.page { max-width: 900px; margin: 60px auto 40px; padding: 0 24px; }

/* HEADER */
.header { background: #1a6b4a; color: #fff; padding: 24px 32px; border-radius: 10px 10px 0 0; margin-bottom: 0; }
.header h1 { font-size: 20px; font-weight: 800; margin-bottom: 4px; }
.header p { font-size: 12px; opacity: .85; }

/* META BAR */
.meta-bar {
  display: flex; gap: 0; background: #e8f5ee;
  border: 1px solid #a7d7b8; border-top: none;
  border-radius: 0 0 10px 10px; margin-bottom: 24px;
  overflow: hidden;
}
.meta-item { flex: 1; padding: 14px 18px; border-right: 1px solid #a7d7b8; }
.meta-item:last-child { border-right: none; }
.meta-item .val { font-size: 22px; font-weight: 800; color: #1a6b4a; font-family: Georgia, serif; }
.meta-item .lbl { font-size: 11px; color: #555; margin-top: 2px; }

/* SECTIONS */
.section { margin-bottom: 24px; }
.section-title {
  font-size: 13px; font-weight: 800; text-transform: uppercase;
  letter-spacing: .06em; color: #1a6b4a; margin-bottom: 10px;
  padding-bottom: 6px; border-bottom: 2px solid #e8f5ee;
}

/* TABLES */
table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 8px; }
thead th {
  background: #1a6b4a; color: #fff; padding: 8px 10px;
  text-align: left; font-size: 11px; font-weight: 700;
  text-transform: uppercase; letter-spacing: .04em;
}
tbody tr { border-bottom: 1px solid #f0f0f0; }
tbody tr:nth-child(even) { background: #f9fbfa; }
td { padding: 8px 10px; color: #1a1f2e; }

/* BADGES */
.badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: 700; }
.b-claimed { background: #dcfce7; color: #15803d; }
.b-unclaimed { background: #fef3c7; color: #b45309; }
.b-expired { background: #f3f4f6; color: #6b7280; }
.b-valid { background: #dcfce7; color: #15803d; }
.b-invalid { background: #fee2e2; color: #b91c1c; }
.b-pending { background: #fef3c7; color: #b45309; }

/* GRID 2 COL */
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }

/* PROGRESS BAR */
.bar-wrap { margin-bottom: 10px; }
.bar-label { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 3px; }
.bar-track { height: 6px; background: #e5e7eb; border-radius: 99px; }
.bar-fill { height: 6px; background: #1a6b4a; border-radius: 99px; }

/* FOOTER */
.doc-footer {
  text-align: center; padding: 16px; color: #9ca3af;
  font-size: 11px; border-top: 1px solid #e5e7eb; margin-top: 32px;
}

/* PRINT */
@media print {
  .print-bar { display: none !important; }
  .page { margin-top: 0; }
  body { font-size: 11px; }
  .header { border-radius: 0; }
}
</style>
</head>
<body>

<!-- PRINT BAR -->
<div class="print-bar">
  <span>Laporan SmartFinder — <?= $bulan_label ?></span>
  <button onclick="window.print()">Cetak / Simpan PDF</button>
  <a href="laporan.php?bulan=<?= $bulan ?>">✕ Tutup</a>
</div>

<div class="page">

  <!-- HEADER -->
  <div class="header">
    <h1>Laporan Barang Temuan</h1>
    <p>SmartFinder — Sistem Manajemen Barang Temuan | Politeknik Negeri Sambas</p>
    <p style="margin-top:6px;">Periode: <b><?= $bulan_label ?></b> &nbsp;|&nbsp; Dicetak oleh: <b><?= htmlspecialchars($nama_petugas) ?></b> &nbsp;|&nbsp; <?= $tgl_cetak ?></p>
  </div>

  <!-- META -->
  <div class="meta-bar">
    <div class="meta-item"><div class="val"><?= $st['total'] ?></div><div class="lbl">Total Barang Masuk</div></div>
    <div class="meta-item"><div class="val"><?= $st['claimed'] ?></div><div class="lbl">Berhasil Diklaim</div></div>
    <div class="meta-item"><div class="val"><?= $st['unclaimed'] ?></div><div class="lbl">Belum Diklaim</div></div>
    <div class="meta-item"><div class="val"><?= $st['expired'] ?></div><div class="lbl">Expired</div></div>
    <div class="meta-item"><div class="val"><?= $pct ?>%</div><div class="lbl">Tingkat Klaim</div></div>
    <div class="meta-item"><div class="val"><?= count($klaim_data) ?></div><div class="lbl">Total Pengajuan Klaim</div></div>
  </div>

  <!-- GRAFIK PER KATEGORI & LOKASI -->
  <div class="grid-2">
    <div class="section">
      <div class="section-title">Per Kategori</div>
      <?php
      $max_kat = max(array_column($per_kat,'total') ?: [1]);
      foreach ($per_kat as $r):
        $pct2 = round(($r['total']/$max_kat)*100);
      ?>
      <div class="bar-wrap">
        <div class="bar-label">
          <span><?= htmlspecialchars($r['nama_kategori']) ?></span>
          <span><?= $r['total'] ?> barang (<?= $r['claimed'] ?> diklaim)</span>
        </div>
        <div class="bar-track"><div class="bar-fill" style="width:<?= $pct2 ?>%;"></div></div>
      </div>
      <?php endforeach; ?>
      <?php if(!$per_kat): ?><p style="color:#9ca3af;font-size:12px;">Tidak ada data</p><?php endif; ?>
    </div>

    <div class="section">
      <div class="section-title">Per Lokasi</div>
      <?php
      $max_lok = max(array_column($per_lok,'total') ?: [1]);
      foreach ($per_lok as $r):
        $pct3 = round(($r['total']/$max_lok)*100);
      ?>
      <div class="bar-wrap">
        <div class="bar-label">
          <span><?= htmlspecialchars($r['nama_lokasi']) ?></span>
          <span><?= $r['total'] ?> barang</span>
        </div>
        <div class="bar-track"><div class="bar-fill" style="width:<?= $pct3 ?>;background:#2d9768;"></div></div>
      </div>
      <?php endforeach; ?>
      <?php if(!$per_lok): ?><p style="color:#9ca3af;font-size:12px;">Tidak ada data</p><?php endif; ?>
    </div>
  </div>

  <!-- DETAIL BARANG -->
  <div class="section">
    <div class="section-title">Detail Barang Temuan (<?= count($detail) ?> barang)</div>
    <table>
      <thead>
        <tr><th>ID</th><th>Deskripsi</th><th>Merek/Warna</th><th>Kategori</th><th>Lokasi</th><th>Tgl Temuan</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php foreach ($detail as $r): ?>
      <tr>
        <td style="color:#9ca3af;">#<?= $r['id_barang'] ?></td>
        <td><b><?= htmlspecialchars($r['deskripsi']) ?></b></td>
        <td style="color:#555;"><?= htmlspecialchars($r['merek']) ?><?= $r['warna']?' / '.htmlspecialchars($r['warna']):'' ?></td>
        <td><?= $r['nama_kategori'] ?></td>
        <td><?= $r['nama_lokasi'] ?></td>
        <td><?= date('d M Y', strtotime($r['tgl_temu'])) ?></td>
        <td><span class="badge b-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if(!$detail): ?><tr><td colspan="7" style="text-align:center;padding:20px;color:#9ca3af;">Tidak ada data</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- RIWAYAT KLAIM -->
  <div class="section">
    <div class="section-title">Riwayat Klaim (<?= count($klaim_data) ?> klaim)</div>
    <table>
      <thead>
        <tr><th>Tgl Klaim</th><th>Barang</th><th>Pemohon</th><th>Identitas</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php foreach ($klaim_data as $r): ?>
      <tr>
        <td><?= date('d M Y H:i', strtotime($r['tgl_klaim'])) ?></td>
        <td><?= htmlspecialchars($r['deskripsi']) ?></td>
        <td><b><?= htmlspecialchars($r['nama_pencari']) ?></b></td>
        <td style="color:#555;"><?= htmlspecialchars($r['identitas_pencari']) ?></td>
        <td><span class="badge b-<?= strtolower($r['status_klaim']) ?>"><?= $r['status_klaim'] ?></span></td>
      </tr>
      <?php endforeach; ?>
      <?php if(!$klaim_data): ?><tr><td colspan="5" style="text-align:center;padding:20px;color:#9ca3af;">Tidak ada klaim bulan ini</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="doc-footer">
    Laporan ini digenerate otomatis oleh sistem SmartFinder &mdash; Politeknik Negeri Sambas &copy; <?= date('Y') ?>
  </div>

</div>
</body>
</html>
<?php exit; ?>
