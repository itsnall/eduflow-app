<?php
// Konfigurasi ini akan ditimpa (di-inject) oleh Terraform saat EC2 menyala
$db_host = "REPLACE_WITH_RDS_ENDPOINT"; 
$db_user = "REPLACE_WITH_DB_USER";
$db_pass = "REPLACE_WITH_DB_PASS";
$db_name = "eduflowdb";

// Membuat koneksi ke AWS RDS
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database RDS Gagal: " . $conn->connect_error);
}
?>