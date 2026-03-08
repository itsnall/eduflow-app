<?php
require 'config.php';

$pesan = "";
$tipe_pesan = "";

// Menangkap notifikasi PRG
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $pesan = "Akun Admin berhasil dibuat! Silakan menuju halaman Login.";
    $tipe_pesan = "success";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Memasukkan data ke tabel ADMINS
    $stmt = $conn->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: register_admin.php?status=success");
            exit();
        } else {
            $pesan = "Error: " . $stmt->error;
            $tipe_pesan = "danger";
        }
    } else {
        $pesan = "Tabel Admins belum siap. Jalankan setup.php terlebih dahulu.";
        $tipe_pesan = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Admin - EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #232f3e; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); width: 100%; max-width: 400px; }
        .btn-custom { background-color: #ff9900; color: white; font-weight: 600; border: none; }
        .btn-custom:hover { background-color: #e68a00; color: white; }
    </style>
</head>
<body>
    <div class="card p-4">
        <div class="text-center mb-4">
            <i class="fa-solid fa-user-shield text-warning fs-1 mb-2"></i>
            <h4 class="fw-bold text-dark">Registrasi Admin</h4>
            <p class="text-muted small">Buat kredensial akses Dashboard tingkat tinggi</p>
        </div>

        <?php if($pesan != ""): ?>
            <div class="alert alert-<?= $tipe_pesan ?> py-2 small"><?= $pesan ?></div>
            <?php if($tipe_pesan == "success"): ?>
                <a href="login.php" class="btn btn-primary w-100 mb-3">Menuju Halaman Login</a>
            <?php endif; ?>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Nama Admin</label>
                <input type="text" class="form-control bg-light" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Email Admin</label>
                <input type="email" class="form-control bg-light" name="email" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Password Sistem</label>
                <input type="password" class="form-control bg-light" name="password" required>
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2"><i class="fa-solid fa-user-plus me-2"></i>Buat Akun Admin</button>
        </form>
    </div>
</body>
</html>