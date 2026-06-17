<?php
include 'koneksi.php'; requireLogin();
$format = $_GET['format'] ?? 'excel';
$bulan  = $_GET['bulan']  ?? date('Y-m');
$y = substr($bulan,0,4); $m = substr($bulan,5,2);
$rows = mysqli_query($conn,"SELECT b.id_barang,b.deskripsi,b.merek,b.warna,k.nama_kategori,l.nama_lokasi,b.tgl_temu,b.status FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori JOIN lokasi l ON b.id_lokasi=l.id_lokasi WHERE YEAR(b.tgl_temu)='$y' AND MONTH(b.tgl_temu)='$m' ORDER BY b.tgl_temu DESC");
$data = mysqli_fetch_all($rows, MYSQLI_ASSOC);

if ($format === 'excel') {
    $filename = "laporan_smartfinder_{$bulan}.csv";
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    $out = fopen('php://output','w');
    fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    fputcsv($out,['ID','Deskripsi','Merek','Warna','Kategori','Lokasi','Tgl Temuan','Status']);
    foreach ($data as $r) {
        fputcsv($out,[$r['id_barang'],$r['deskripsi'],$r['merek'],$r['warna'],$r['nama_kategori'],$r['nama_lokasi'],date('d/m/Y',strtotime($r['tgl_temu'])),$r['status']]);
    }
    fclose($out); exit;
}

if ($format === 'pdf') {
    $filename = "laporan_smartfinder_{$bulan}.html";
    header("Content-Type: text/html; charset=utf-8");
    $bulan_label = date('F Y', strtotime($bulan.'-01'));
    $total = count($data);
    $claimed = count(array_filter($data, fn($r) => $r['status']==='Claimed'));
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8">
    <title>Laporan SmartFinder '.$bulan_label.'</title>
    <style>
    body{font-family:Arial,sans-serif;font-size:13px;color:#1a1a1a;margin:0;padding:0;}
    .header{background:#1a6b4a;color:#fff;padding:24px 32px;}
    .header h1{margin:0;font-size:20px;}.header p{margin:4px 0 0;opacity:.8;font-size:13px;}
    .meta{display:flex;gap:24px;padding:16px 32px;background:#f0fdf4;border-bottom:1px solid #bbf7d0;}
    .meta-item{}.meta-item .val{font-size:22px;font-weight:700;color:#1a6b4a;}.meta-item .lbl{font-size:12px;color:#4b5563;}
    .content{padding:24px 32px;}
    table{width:100%;border-collapse:collapse;font-size:12px;}
    th{background:#f3f4f6;padding:8px 10px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:.04em;color:#6b7280;border-bottom:2px solid #e5e7eb;}
    td{padding:8px 10px;border-bottom:1px solid #f3f4f6;}
    .badge{display:inline-block;padding:2px 8px;border-radius:99px;font-size:11px;font-weight:700;}
    .b-claimed{background:#dcfce7;color:#16a34a;}.b-unclaimed{background:#fef3c7;color:#b45309;}.b-expired{background:#f3f4f6;color:#6b7280;}
    .footer{text-align:center;padding:16px;color:#9ca3af;font-size:11px;border-top:1px solid #e5e7eb;margin-top:24px;}
    @media print{.no-print{display:none;}}
    </style></head><body>';
    echo '<div class="no-print" style="background:#1a6b4a;color:#fff;padding:10px 20px;display:flex;gap:12px;align-items:center;">
    <span style="flex:1;font-weight:600;">Laporan SmartFinder — '.$bulan_label.'</span>
    <button onclick="window.print()" style="background:#fff;color:#1a6b4a;border:none;padding:6px 16px;border-radius:6px;font-weight:700;cursor:pointer;">🖨️ Cetak / Simpan PDF</button>
    <a href="laporan.php?bulan='.$bulan.'" style="color:#fff;font-size:13px;text-decoration:none;">✕ Tutup</a>
    </div>';
    echo '<div class="header"><h1>📋 Laporan Barang Temuan</h1><p>SmartFinder — Sistem Manajemen Barang Temuan Kampus | Periode: '.$bulan_label.'</p></div>';
    echo '<div class="meta">
    <div class="meta-item"><div class="val">'.$total.'</div><div class="lbl">Total Barang</div></div>
    <div class="meta-item"><div class="val">'.$claimed.'</div><div class="lbl">Berhasil Diklaim</div></div>
    <div class="meta-item"><div class="val">'.($total>0?round($claimed/$total*100):0).'%</div><div class="lbl">Tingkat Klaim</div></div>
    <div class="meta-item"><div class="val">'.date('d M Y').'</div><div class="lbl">Tgl Cetak</div></div>
    </div>';
    echo '<div class="content"><table><thead><tr><th>ID</th><th>Deskripsi</th><th>Merek</th><th>Kategori</th><th>Lokasi</th><th>Tgl Temuan</th><th>Status</th></tr></thead><tbody>';
    foreach ($data as $r) {
        $st = strtolower($r['status']);
        echo '<tr><td style="color:#9ca3af;">#'.$r['id_barang'].'</td><td><b>'.htmlspecialchars($r['deskripsi']).'</b></td><td>'.htmlspecialchars($r['merek']).'</td><td>'.htmlspecialchars($r['nama_kategori']).'</td><td>'.htmlspecialchars($r['nama_lokasi']).'</td><td>'.date('d M Y',strtotime($r['tgl_temu'])).'</td><td><span class="badge b-'.$st.'">'.$r['status'].'</span></td></tr>';
    }
    if(!$data) echo '<tr><td colspan="7" style="text-align:center;padding:20px;color:#9ca3af;">Tidak ada data</td></tr>';
    echo '</tbody></table></div>';
    echo '<div class="footer">Dicetak pada '.date('d M Y H:i').' | SmartFinder &copy; '.date('Y').'</div></body></html>';
    exit;
}
