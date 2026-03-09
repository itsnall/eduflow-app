<?php
session_start();
require 'config.php';

// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pesan = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file_materi"])) {
    $judul = $_POST['judul'];
    $file_tmp = $_FILES["file_materi"]["tmp_name"];
    
    // Ganti spasi dengan underscore untuk nama file S3
    $file_name = time() . "_" . str_replace(" ", "_", basename($_FILES["file_materi"]["name"]));
    $s3_path = "s3://" . $s3_bucket . "/" . $file_name;

    // Menjalankan perintah AWS CLI via PHP untuk mengunggah ke S3
    $cmd = "aws s3 cp " . escapeshellarg($file_tmp) . " " . escapeshellarg($s3_path) . " --region " . escapeshellarg($s3_region) . " 2>&1";
    $output = shell_exec($cmd);

    // Jika berhasil upload ke S3, simpan namanya ke Database RDS
    if (strpos($output, 'upload:') !== false) {
        $stmt = $conn->prepare("INSERT INTO materials (title, file_name) VALUES (?, ?)");
        $stmt->bind_param("ss", $judul, $file_name);
        $stmt->execute();
        $pesan = "<div class='alert alert-success'>Materi berhasil diunggah ke AWS S3!</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal upload ke S3: $output</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Upload Materi S3 - EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm p-4">
            <h4 class="mb-4 text-primary">Upload Materi Pelajaran</h4>
            <?= $pesan ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Judul Materi</label>
                    <input type="text" class="form-control" name="judul" required>
                </div>
                <div class="mb-4">
                    <label>Pilih File (PDF/DOCX)</label>
                    <input type="file" class="form-control" name="file_materi" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Upload ke AWS S3</button>
                <a href="dashboard.php" class="btn btn-secondary w-100 mt-2">Kembali ke Dashboard</a>
            </form>
        </div>
    </div>
</body>
</html>