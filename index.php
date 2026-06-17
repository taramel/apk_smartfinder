<?php
include 'koneksi.php'; include 'layout.php';
$where = "WHERE b.status='Unclaimed'";
$keyword=''; $kat='';
if (!empty($_GET['q'])) { $keyword=e($conn,$_GET['q']); $where.=" AND (b.deskripsi LIKE '%$keyword%' OR b.merek LIKE '%$keyword%' OR b.warna LIKE '%$keyword%')"; }
if (!empty($_GET['kat'])) { $kat=(int)$_GET['kat']; $where.=" AND b.id_kategori=$kat"; }
$per=9; $page=max(1,(int)($_GET['page']??1)); $offset=($page-1)*$per;
$total=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM barang_temuan b $where"))['c'];
$pages=ceil($total/$per);
$result=mysqli_query($conn,"SELECT b.*,k.nama_kategori,k.icon,l.nama_lokasi FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori JOIN lokasi l ON b.id_lokasi=l.id_lokasi $where ORDER BY b.tgl_temu DESC LIMIT $per OFFSET $offset");
$st=mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(status='Unclaimed') AS unc,SUM(status='Claimed') AS clm,COUNT(*) AS tot FROM barang_temuan"));
$cats=mysqli_query($conn,"SELECT * FROM kategori ORDER BY nama_kategori");
renderHead('Beranda');
renderTopbar('home');
$icon_map=['smartphone'=>'fa-mobile-screen','file-text'=>'fa-file-lines','briefcase'=>'fa-briefcase','key'=>'fa-key','box'=>'fa-box'];
?>
<!-- HERO -->
<div style="background:linear-gradient(135deg,#1e3a2f 0%,#162a22 100%);color:#fff;padding:52px 24px;">
  <div style="max-width:1100px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px;">
    <div style="max-width:520px;">
      <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.15);border-radius:99px;padding:5px 14px;font-size:12px;font-weight:600;letter-spacing:.05em;margin-bottom:16px;">
        <i class="fa-solid fa-shield-check"></i> SISTEM BARANG TEMUAN KAMPUS
      </div>
      <h1 style="font-family:'DM Serif Display',serif;font-size:clamp(28px,4vw,44px);line-height:1.15;margin-bottom:14px;">Temukan Kembali<br>Barang <span style="color:#fbbf24;">Hilang</span> Anda</h1>
      <p style="opacity:.85;font-size:15px;line-height:1.7;margin-bottom:24px;">Cek daftar barang temuan di kampus. Jika barang Anda ada, lakukan verifikasi kepemilikan secara cepat dan mudah.</p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="#daftar-barang" class="btn" style="background:#fff;color:var(--primary);"><i class="fa-solid fa-list"></i> Lihat Barang</a>
        <a href="login.php" class="btn" style="background:rgba(255,255,255,.15);color:#fff;border:1px solid rgba(255,255,255,.3);"><i class="fa-solid fa-lock"></i> Login Petugas</a>
      </div>
    </div>
    <div style="display:flex;gap:16px;flex-wrap:wrap;">
      <div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:14px;padding:20px 24px;text-align:center;min-width:120px;">
        <div style="font-family:'DM Serif Display',serif;font-size:36px;font-weight:700;"><?=$st['tot']?></div>
        <div style="opacity:.8;font-size:13px;margin-top:4px;">Total Barang</div>
      </div>
      <div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:14px;padding:20px 24px;text-align:center;min-width:120px;">
        <div style="font-family:'DM Serif Display',serif;font-size:36px;font-weight:700;color:#fbbf24;"><?=$st['unc']?></div>
        <div style="opacity:.8;font-size:13px;margin-top:4px;">Belum Diklaim</div>
      </div>
      <div style="background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:14px;padding:20px 24px;text-align:center;min-width:120px;">
        <div style="font-family:'DM Serif Display',serif;font-size:36px;font-weight:700;color:#86efac;"><?=$st['clm']?></div>
        <div style="opacity:.8;font-size:13px;margin-top:4px;">Sudah Diklaim</div>
      </div>
    </div>
  </div>
</div>

<div class="wrap" id="daftar-barang">
  <!-- Search -->
  <form method="GET" style="margin-bottom:28px;">
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:18px 20px;box-shadow:var(--shadow);display:flex;gap:12px;align-items:center;flex-wrap:wrap;">
      <div class="search-wrap" style="flex:2;min-width:200px;">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" name="q" placeholder="Cari deskripsi, merek, warna barang..." value="<?=htmlspecialchars($keyword)?>">
      </div>
      <select name="kat" class="form-control" style="width:auto;min-width:160px;" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        <?php while($c=mysqli_fetch_assoc($cats)): ?>
        <option value="<?=$c['id_kategori']?>" <?=$kat==$c['id_kategori']?'selected':''?>><?=$c['nama_kategori']?></option>
        <?php endwhile; ?>
      </select>
      <button type="submit" class="btn btn-primary"><i class="fa-solid fa-search"></i> Cari</button>
      <?php if($keyword||$kat): ?><a href="index.php" class="btn btn-secondary"><i class="fa-solid fa-xmark"></i> Reset</a><?php endif; ?>
    </div>
  </form>

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
    <h2 style="font-size:18px;font-weight:700;">Barang Belum Diklaim <span style="background:var(--primary-light);color:var(--primary);padding:2px 10px;border-radius:99px;font-size:13px;margin-left:8px;"><?=$total?></span></h2>
  </div>

  <?php if($total==0): ?>
  <div class="empty-state card"><i class="fa-solid fa-box-open"></i><h3>Tidak Ada Barang</h3><p><?=$keyword?"Tidak ada hasil untuk \"".htmlspecialchars($keyword)."\"":"Belum ada barang temuan terdaftar"?></p></div>
  <?php else: ?>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(310px,1fr));gap:16px;">
  <?php while($d=mysqli_fetch_assoc($result)):
    $fa=$icon_map[$d['icon']]??'fa-box';
    $daysago = floor((time()-strtotime($d['tgl_temu']))/86400);
  ?>
  <div class="card" style="display:flex;flex-direction:column;gap:0;padding:0;overflow:hidden;transition:box-shadow .2s,transform .2s;" onmouseenter="this.style.boxShadow='0 8px 32px rgba(0,0,0,.12)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='';this.style.transform=''">
    <?php if($d['foto'] && file_exists('uploads/'.$d['foto'])): ?>
    <div style="height:160px;overflow:hidden;background:var(--bg3);">
      <img src="uploads/<?=htmlspecialchars($d['foto'])?>" style="width:100%;height:100%;object-fit:cover;" alt="Foto Barang">
    </div>
    <?php else: ?>
    <div style="height:100px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;font-size:40px;color:var(--primary);opacity:.4;">
      <i class="fa-solid <?=$fa?>"></i>
    </div>
    <?php endif; ?>
    <div style="padding:18px;flex:1;display:flex;flex-direction:column;gap:10px;">
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text3);margin-bottom:3px;"><?=$d['nama_kategori']?></div>
          <div style="font-weight:700;font-size:16px;"><?=htmlspecialchars($d['deskripsi'])?></div>
        </div>
        <span class="badge b-unclaimed" style="flex-shrink:0;margin-top:2px;">Unclaimed</span>
      </div>
      <div style="display:flex;flex-direction:column;gap:5px;font-size:13px;color:var(--text2);">
        <?php if($d['merek']): ?><div><i class="fa-solid fa-tag" style="width:16px;color:var(--text3);"></i> <?=htmlspecialchars($d['merek'])?><?=($d['warna']?' &bull; '.htmlspecialchars($d['warna']):'')?></div><?php endif; ?>
        <div><i class="fa-solid fa-location-dot" style="width:16px;color:var(--text3);"></i> <?=$d['nama_lokasi']?></div>
        <div><i class="fa-regular fa-clock" style="width:16px;color:var(--text3);"></i> <?=date('d M Y',strtotime($d['tgl_temu']))?> <span style="color:var(--text3);">(<?=$daysago?> hari lalu)</span></div>
      </div>
      <a href="klaim.php?id=<?=$d['id_barang']?>" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:auto;">
        <i class="fa-solid fa-shield-check"></i> Verifikasi &amp; Klaim
      </a>
    </div>
  </div>
  <?php endwhile; ?>
  </div>
  <?php if($pages>1): ?>
  <div class="pagination" style="margin-top:28px;">
    <?php if($page>1): ?><a href="?q=<?=urlencode($keyword)?>&kat=<?=$kat?>&page=<?=$page-1?>"><i class="fa-solid fa-chevron-left"></i></a><?php endif; ?>
    <?php for($i=1;$i<=$pages;$i++): ?><a href="?q=<?=urlencode($keyword)?>&kat=<?=$kat?>&page=<?=$i?>" class="<?=$i==$page?'pg-active':''?>"><?=$i?></a><?php endfor; ?>
    <?php if($page<$pages): ?><a href="?q=<?=urlencode($keyword)?>&kat=<?=$kat?>&page=<?=$page+1?>"><i class="fa-solid fa-chevron-right"></i></a><?php endif; ?>
  </div>
  <?php endif; ?>
  <?php endif; ?>
</div>
<?php renderFoot(); ?>
