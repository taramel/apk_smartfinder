<?php
include 'koneksi.php';
include 'layout.php';
requireLogin();

$id = (int)($_GET['id'] ?? 0);
$d = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT b.*,k.nama_kategori,l.nama_lokasi FROM barang_temuan b
     JOIN kategori k ON b.id_kategori=k.id_kategori
     JOIN lokasi l ON b.id_lokasi=l.id_lokasi
     WHERE b.id_barang=$id"));

if (!$d) { header("Location: kelola_barang.php"); exit; }

// URL yang akan di-encode ke QR
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on' ? 'https' : 'http')
          . '://' . $_SERVER['HTTP_HOST']
          . dirname($_SERVER['PHP_SELF']);
$klaim_url = $base_url . "/klaim.php?id=$id";

ob_start();
?>
<div class="page-header">
  <div>
    <h1><i class="fa-solid fa-qrcode" style="color:var(--primary);margin-right:8px;"></i>QR Code Barang</h1>
    <p>Scan QR untuk langsung membuka halaman klaim barang ini</p>
  </div>
  <div style="display:flex;gap:10px;">
    <button onclick="window.print()" class="btn btn-secondary"><i class="fa-solid fa-print"></i> Cetak QR</button>
    <a href="kelola_barang.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
  </div>
</div>

<!-- Print Style -->
<style>
@media print {
  .topbar, .sidebar, .page-header .btn, footer { display: none !important; }
  .main-content { padding: 0 !important; }
  .qr-card { box-shadow: none !important; border: 1px solid #ccc !important; }
}
</style>

<div style="max-width:500px;margin:0 auto;">
  <!-- Info Barang -->
  <div class="card qr-card" style="text-align:center;padding:32px;">
    <div style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text3);margin-bottom:8px;">
      <?= htmlspecialchars($d['nama_kategori']) ?> &bull; #<?= $id ?>
    </div>
    <div style="font-size:20px;font-weight:800;margin-bottom:4px;"><?= htmlspecialchars($d['deskripsi']) ?></div>
    <div style="color:var(--text2);font-size:13px;margin-bottom:24px;">
      <i class="fa-solid fa-location-dot"></i> <?= $d['nama_lokasi'] ?> &bull;
      <i class="fa-regular fa-clock"></i> <?= date('d M Y', strtotime($d['tgl_temu'])) ?>
    </div>

    <!-- QR CODE -->
    <div id="qrcode" style="display:inline-block;padding:16px;background:white;border-radius:12px;border:2px solid var(--border);margin-bottom:16px;"></div>

    <div style="font-size:12px;color:var(--text3);margin-bottom:8px;">Scan untuk klaim barang ini</div>
    <div style="font-size:11px;color:var(--text3);word-break:break-all;background:var(--bg3);padding:8px 12px;border-radius:6px;">
      <?= htmlspecialchars($klaim_url) ?>
    </div>

    <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
      <div style="font-size:11px;color:var(--text3);">SmartFinder &mdash; Politeknik Negeri Sambas</div>
    </div>
  </div>

  <!-- Tombol download -->
  <div style="text-align:center;margin-top:16px;">
    <button onclick="downloadQR()" class="btn btn-primary"><i class="fa-solid fa-download"></i> Download QR PNG</button>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
const url = <?= json_encode($klaim_url) ?>;

const qr = new QRCode(document.getElementById('qrcode'), {
  text: url,
  width: 220,
  height: 220,
  colorDark: '#1a1f2e',
  colorLight: '#ffffff',
  correctLevel: QRCode.CorrectLevel.H
});

function downloadQR() {
  setTimeout(() => {
    const canvas = document.querySelector('#qrcode canvas');
    if (!canvas) { alert('QR belum siap, coba lagi.'); return; }
    const link = document.createElement('a');
    link.download = 'qr_barang_<?= $id ?>.png';
    link.href = canvas.toDataURL('image/png');
    link.click();
  }, 300);
}
</script>

<?php
$content = ob_get_clean();
buildPage('QR Code Barang', 'barang', $content);
?>
