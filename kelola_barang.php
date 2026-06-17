<?php
include 'koneksi.php'; include 'layout.php';
requireLogin();
if(!empty($_GET['del'])){
    $id=(int)$_GET['del'];
    $b=mysqli_fetch_assoc(mysqli_query($conn,"SELECT deskripsi,foto FROM barang_temuan WHERE id_barang=$id"));
    if($b['foto']&&file_exists('uploads/'.$b['foto'])) unlink('uploads/'.$b['foto']);
    mysqli_query($conn,"DELETE FROM klaim WHERE id_barang=$id");
    mysqli_query($conn,"DELETE FROM barang_temuan WHERE id_barang=$id");
    logActivity($conn,'Hapus Barang',$b['deskripsi']);
    header("Location: kelola_barang.php?msg=deleted"); exit;
}
$msg=$_GET['msg']??'';
$where="WHERE 1=1"; $keyword=''; $kat=''; $status='';
if(!empty($_GET['q'])){$keyword=e($conn,$_GET['q']);$where.=" AND (b.deskripsi LIKE '%$keyword%' OR b.merek LIKE '%$keyword%')";}
if(!empty($_GET['kat'])){$kat=(int)$_GET['kat'];$where.=" AND b.id_kategori=$kat";}
if(!empty($_GET['status'])){$status=e($conn,$_GET['status']);$where.=" AND b.status='$status'";}
$per=15;$page=max(1,(int)($_GET['page']??1));$offset=($page-1)*$per;
$total=mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS c FROM barang_temuan b $where"))['c'];
$pages=ceil($total/$per);
$result=mysqli_query($conn,"SELECT b.*,k.nama_kategori,l.nama_lokasi FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori JOIN lokasi l ON b.id_lokasi=l.id_lokasi $where ORDER BY b.tgl_temu DESC LIMIT $per OFFSET $offset");
$cats=mysqli_query($conn,"SELECT * FROM kategori ORDER BY nama_kategori");
ob_start();
?>
<div class="page-header">
  <div><h1>Data Barang Temuan</h1><p>Kelola semua barang temuan yang terdaftar</p></div>
  <a href="tambah_barang.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Tambah Barang</a>
</div>
<?php if($msg==='added'):?><?=showAlert('success','Barang berhasil ditambahkan!')?><?php endif;?>
<?php if($msg==='deleted'):?><?=showAlert('success','Barang berhasil dihapus.')?><?php endif;?>
<?php if($msg==='updated'):?><?=showAlert('success','Barang berhasil diperbarui.')?><?php endif;?>
<form method="GET" style="margin-bottom:16px;">
  <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <div class="search-wrap" style="flex:1;min-width:200px;"><i class="fa-solid fa-magnifying-glass"></i><input type="text" name="q" placeholder="Cari deskripsi atau merek..." value="<?=htmlspecialchars($keyword)?>"></div>
    <select name="kat" class="form-control" style="width:auto;min-width:140px;">
      <option value="">Semua Kategori</option>
      <?php while($c=mysqli_fetch_assoc($cats)):?><option value="<?=$c['id_kategori']?>" <?=$kat==$c['id_kategori']?'selected':''?>><?=$c['nama_kategori']?></option><?php endwhile;?>
    </select>
    <select name="status" class="form-control" style="width:auto;min-width:130px;">
      <option value="">Semua Status</option>
      <option value="Unclaimed" <?=$status==='Unclaimed'?'selected':''?>>Unclaimed</option>
      <option value="Claimed" <?=$status==='Claimed'?'selected':''?>>Claimed</option>
      <option value="Expired" <?=$status==='Expired'?'selected':''?>>Expired</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-filter"></i> Filter</button>
    <a href="kelola_barang.php" class="btn btn-secondary btn-sm"><i class="fa-solid fa-xmark"></i> Reset</a>
  </div>
</form>
<p style="color:var(--text2);font-size:13px;margin-bottom:12px;">Total: <b><?=$total?></b> barang</p>
<div class="table-wrap">
  <table>
    <thead><tr><th>ID</th><th>Foto</th><th>Deskripsi</th><th>Kategori</th><th>Lokasi</th><th>Tgl Temuan</th><th>Expired</th><th>Status</th><th style="text-align:center;">Aksi</th></tr></thead>
    <tbody>
    <?php if($total==0):?><tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text2);"><i class="fa-solid fa-box-open" style="font-size:32px;display:block;margin-bottom:8px;color:var(--border2);"></i>Tidak ada data</td></tr>
    <?php else:while($d=mysqli_fetch_assoc($result)):?>
    <tr>
      <td style="font-size:12px;color:var(--text3);font-weight:600;">#<?=$d['id_barang']?></td>
      <td><?php if($d['foto']&&file_exists('uploads/'.$d['foto'])):?><img src="uploads/<?=htmlspecialchars($d['foto'])?>" class="foto-preview"><?php else:?><div style="width:60px;height:44px;background:var(--bg3);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--text3);font-size:18px;"><i class="fa-solid fa-image"></i></div><?php endif;?></td>
      <td><div style="font-weight:600;"><?=htmlspecialchars($d['deskripsi'])?></div><?php if($d['merek']):?><div style="font-size:12px;color:var(--text3);"><?=htmlspecialchars($d['merek'])?> <?=($d['warna']?'&bull; '.htmlspecialchars($d['warna']):'') ?></div><?php endif;?></td>
      <td><?=$d['nama_kategori']?></td>
      <td><?=$d['nama_lokasi']?></td>
      <td style="font-size:12px;color:var(--text2);"><?=date('d M Y',strtotime($d['tgl_temu']))?></td>
      <td style="font-size:12px;color:<?=($d['tgl_expired']&&strtotime($d['tgl_expired'])<time()?'var(--red)':'var(--text2)')?>;"><?=$d['tgl_expired']?date('d M Y',strtotime($d['tgl_expired'])):'-'?></td>
      <td><span class="badge b-<?=strtolower($d['status'])?>"><?=$d['status']?></span></td>
      <td><div style="display:flex;gap:6px;justify-content:center;">
        <a href="qr_barang.php?id=<?=$d['id_barang']?>" class="btn btn-secondary btn-icon" title="QR Code"><i class="fa-solid fa-qrcode"></i></a>
        <a href="edit_barang.php?id=<?=$d['id_barang']?>" class="btn btn-secondary btn-icon" title="Edit"><i class="fa-solid fa-pen"></i></a>
        <a href="kelola_barang.php?del=<?=$d['id_barang']?>" class="btn btn-danger btn-icon" title="Hapus" onclick="return confirm('Hapus barang ini?')"><i class="fa-solid fa-trash"></i></a>
      </div></td>
    </tr>
    <?php endwhile;endif;?>
    </tbody>
  </table>
</div>
<?php if($pages>1):?>
<div class="pagination">
  <?php if($page>1):?><a href="?q=<?=urlencode($keyword)?>&kat=<?=$kat?>&status=<?=$status?>&page=<?=$page-1?>"><i class="fa-solid fa-chevron-left"></i></a><?php endif;?>
  <?php for($i=1;$i<=$pages;$i++):?><a href="?q=<?=urlencode($keyword)?>&kat=<?=$kat?>&status=<?=$status?>&page=<?=$i?>" class="<?=$i==$page?'pg-active':''?>"><?=$i?></a><?php endfor;?>
  <?php if($page<$pages):?><a href="?q=<?=urlencode($keyword)?>&kat=<?=$kat?>&status=<?=$status?>&page=<?=$page+1?>"><i class="fa-solid fa-chevron-right"></i></a><?php endif;?>
</div>
<?php endif;?>
<?php $content=ob_get_clean();buildPage('Data Barang','barang',$content);?>
