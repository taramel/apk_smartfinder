<?php
include 'koneksi.php';include 'barang.php';
$id=(int)$_POST['id'];
$input=trim($_POST['bukti_input']??'');
$nama=e($conn,$_POST['nama_pencari']??'');
$identitas=e($conn,$_POST['identitas']??'');
$no_hp=e($conn,$_POST['no_hp']??'');
$email=e($conn,$_POST['email']??'');
$data=mysqli_fetch_assoc(mysqli_query($conn,"SELECT b.*,k.nama_kategori FROM barang_temuan b JOIN kategori k ON b.id_kategori=k.id_kategori WHERE b.id_barang=$id AND b.status='Unclaimed'"));
if(!$data){header("Location: index.php");exit;}
$objek=buatObjekBarang($id,$data['deskripsi'],$data['nama_kategori']);
mysqli_query($conn,"INSERT INTO pencari (nama_pencari,identitas_pencari,no_hp,email) VALUES ('$nama','$identitas','$no_hp','$email')");
$id_pencari=mysqli_insert_id($conn);
if($objek->validasi($input,$data['bukti_rahasia'])){
    mysqli_query($conn,"UPDATE barang_temuan SET status='Claimed' WHERE id_barang=$id");
    mysqli_query($conn,"INSERT INTO klaim (id_barang,id_pencari,bukti_input,status_klaim) VALUES ('$id','$id_pencari','".e($conn,$input)."','Valid')");
    header("Location: hasil.php?status=valid&barang=".urlencode($data['deskripsi']));exit;
}else{
    mysqli_query($conn,"INSERT INTO klaim (id_barang,id_pencari,bukti_input,status_klaim) VALUES ('$id','$id_pencari','".e($conn,$input)."','Invalid')");
    header("Location: hasil.php?status=invalid&id=$id");exit;
}
