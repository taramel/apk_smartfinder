<?php
include 'koneksi.php';
logActivity($conn,'Logout','Keluar dari sistem');
session_destroy();
header("Location: login.php");
exit;
