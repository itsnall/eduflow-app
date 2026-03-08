<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup - EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style> body { background-color: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; } </style>
</head>
<body>
    <div class="container text-center">
        <div class="card p-5 shadow border-0" style="max-width: 500px; margin: auto; border-radius: 15px;">
            <i class="fa-solid fa-database text-primary" style="font-size: 4rem; margin-bottom: 20px;"></i>
            
            <?php
            require 'config.php';

            // 1. Buat Tabel Siswa
            $sql_users = "CREATE TABLE IF NOT EXISTS users (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            // 2. Buat Tabel Admin
            $sql_admins = "CREATE TABLE IF NOT EXISTS admins (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(50) NOT NULL,
                password VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";

            if ($conn->query($sql_users) === TRUE && $conn->query($sql_admins) === TRUE) {
                echo "<h4 class='text-success fw-bold mb-3'>Sistem Siap!</h4>";
                echo "<p class='text-muted'>Tabel 'users' dan 'admins' telah berhasil dibuat di AWS RDS.</p>";
                echo "<a href='register_admin.php' class='btn btn-warning mt-3 px-4 py-2 rounded-pill fw-bold text-dark'>Lanjut Buat Akun Admin</a>";
            } else {
                echo "<h4 class='text-danger fw-bold mb-3'>Terjadi Kesalahan</h4>";
                echo "<p class='text-muted'>Error: " . $conn->error . "</p>";
            }

            $conn->close();
            ?>
        </div>
    </div>
</body>
</html>