<?php
require 'config.php';

$pesan = "";
$tipe_pesan = "";

// Menangkap notifikasi sukses dari proses Redirect
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $pesan = "Pendaftaran berhasil! Data siswa telah tersimpan dengan aman di AWS RDS.";
    $tipe_pesan = "success";
}

// Proses Form Pendaftaran (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            
            // Trik PRG (Post/Redirect/Get) untuk mencegah resubmission
            header("Location: index.php?status=success");
            exit(); // Hentikan skrip agar tidak lanjut mengeksekusi kode di bawahnya
            
        } else {
            $pesan = "Error saat menyimpan: " . $stmt->error;
            $tipe_pesan = "danger";
        }
    } else {
        $pesan = "Database belum siap! Silakan inisialisasi tabel terlebih dahulu.";
        $tipe_pesan = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduFlow LMS - Cloud Native Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar-custom { background: linear-gradient(135deg, #0f2027, #203a43, #2c5364); }
        .card { border: none; border-radius: 12px; box-shadow: 0 6px 15px rgba(0,0,0,0.08); transition: transform 0.2s; }
        .card:hover { transform: translateY(-3px); }
        .card-header { border-radius: 12px 12px 0 0 !important; font-weight: 600; }
        .btn-custom { background-color: #ff9900; color: white; font-weight: 600; border: none; border-radius: 8px; padding: 10px; }
        .btn-custom:hover { background-color: #e68a00; color: white; }
        .table-hover tbody tr:hover { background-color: #f1f5f9; }
        .aws-badge { background-color: #232f3e; color: #ff9900; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fa-solid fa-graduation-cap me-2"></i>EduFlow</a>
            
            <div class="d-flex align-items-center">
                <span class="aws-badge d-none d-md-block me-3"><i class="fa-brands fa-aws me-1"></i>Powered by AWS Cloud</span>
                <a href="login.php" class="btn btn-outline-light btn-sm fw-bold"><i class="fa-solid fa-right-to-bracket me-2"></i>Login Admin</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2 class="fw-bold" style="color: #2c5364;">Selamat Datang di Portal EduFlow</h2>
                <p class="text-muted">Learning Management System dengan skalabilitas tinggi berbasis Auto Scaling & RDS</p>
            </div>
        </div>

        <?php if($pesan != ""): ?>
        <div class="alert alert-<?= $tipe_pesan ?> alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid <?= $tipe_pesan == 'success' ? 'fa-check-circle' : 'fa-triangle-exclamation' ?> me-2"></i>
            <?= $pesan ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white text-primary border-bottom-0 pt-4 pb-0">
                        <h5 class="mb-0"><i class="fa-solid fa-user-plus me-2"></i>Registrasi Siswa</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-regular fa-user"></i></span>
                                    <input type="text" class="form-control" name="name" placeholder="Masukkan nama" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">Alamat Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-regular fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="email@contoh.com" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Buat password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-paper-plane me-2"></i>Daftar Sekarang</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header bg-white text-dark border-bottom-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fa-solid fa-users me-2 text-primary"></i>Direktori Siswa Aktif</h5>
                        <span class="badge bg-success rounded-pill">Live from RDS</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Siswa</th>
                                        <th>Email</th>
                                        <th>Tanggal Bergabung</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql_select = "SELECT id, name, email, created_at FROM users ORDER BY id DESC";
                                    $result = $conn->query($sql_select);

                                    if ($result && $result->num_rows > 0) {
                                        while($row = $result->fetch_assoc()) {
                                            $date = date("d M Y, H:i", strtotime($row["created_at"]));
                                            echo "<tr>";
                                            echo "<td><span class='badge bg-secondary'>#" . $row["id"]. "</span></td>";
                                            echo "<td class='fw-bold text-dark'>" . htmlspecialchars($row["name"]). "</td>";
                                            echo "<td>" . htmlspecialchars($row["email"]). "</td>";
                                            echo "<td class='text-muted small'><i class='fa-regular fa-clock me-1'></i>" . $date . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center py-4 text-muted'>";
                                        echo "<i class='fa-solid fa-folder-open fs-1 mb-3 d-block opacity-50'></i>";
                                        echo "Belum ada data siswa.<br><a href='setup.php' class='btn btn-outline-primary btn-sm mt-3'>Inisialisasi Database Pertama Kali</a>";
                                        echo "</td></tr>";
                                    }
                                    $conn->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-5 mb-3">
            <div class="col-12 text-center text-muted small">
                &copy; <?= date("Y") ?> EduFlow - Final Project Kelompok 2. <br>
                Di-deploy secara otomatis menggunakan Terraform.
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>