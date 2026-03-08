<?php
require 'config.php';

// SQL untuk membuat tabel users
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "<h3>Sistem Siap! Tabel 'users' berhasil dibuat di AWS RDS.</h3>";
    echo "<a href='index.php'>Kembali ke Halaman Utama</a>";
} else {
    echo "Error membuat tabel: " . $conn->error;
}

$conn->close();
?>