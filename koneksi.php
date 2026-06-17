<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "smartfinder");
if (!$conn) die("Koneksi Database Gagal: " . mysqli_connect_error());
mysqli_set_charset($conn, 'utf8mb4');

function e($conn, $str) { return mysqli_real_escape_string($conn, trim($str)); }
function isLoggedIn() { return isset($_SESSION['user_id']); }
function requireLogin() { if (!isLoggedIn()) { header("Location: login.php"); exit; } }
function isAdmin() { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function requireAdmin() { requireLogin(); if (!isAdmin()) { header("Location: dashboard.php"); exit; } }

function logActivity($conn, $aksi, $detail = '') {
    if (!isset($_SESSION['user_id'])) return;
    $uid = (int)$_SESSION['user_id'];
    $aksi = e($conn, $aksi);
    $detail = e($conn, $detail);
    $ip = e($conn, $_SERVER['REMOTE_ADDR'] ?? '');
    mysqli_query($conn, "INSERT INTO activity_log (id_user,aksi,detail,ip_address) VALUES ('$uid','$aksi','$detail','$ip')");
}
