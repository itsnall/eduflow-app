<?php
session_start();
require 'config.php';

// Jika sudah login, langsung arahkan ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Cari user berdasarkan email
    $stmt = $conn->prepare("SELECT id, name, password FROM admins WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $hashed_password);
            $stmt->fetch();
            
            // Verifikasi password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password yang Anda masukkan salah!";
            }
        } else {
            $error = "Email tidak terdaftar!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - EduFlow Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card { border: none; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .btn-custom { background-color: #ff9900; color: white; font-weight: 600; border: none; }
        .btn-custom:hover { background-color: #e68a00; color: white; }
    </style>
</head>
<body>
    <div class="card p-4">
        <div class="text-center mb-4">
            <i class="fa-solid fa-graduation-cap text-primary fs-1 mb-2"></i>
            <h4 class="fw-bold" style="color: #2c5364;">EduFlow Login</h4>
            <p class="text-muted small">Silakan login untuk mengakses Dashboard</p>
        </div>

        <?php if($error != ""): ?>
            <div class="alert alert-danger py-2 small"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label text-muted small fw-bold">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" required>
                </div>
            </div>
            <button type="submit" class="btn btn-custom w-100 py-2"><i class="fa-solid fa-right-to-bracket me-2"></i>Masuk Dashboard</button>
        </form>
        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none small"><i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>