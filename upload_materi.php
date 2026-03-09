<?php
session_start();
require 'config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$pesan = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file_materi"])) {
    $judul = $_POST['judul'];
    $file_tmp = $_FILES["file_materi"]["tmp_name"];
    
    $file_name = time() . "_" . str_replace(" ", "_", basename($_FILES["file_materi"]["name"]));
    $s3_path = "s3://" . $s3_bucket . "/" . $file_name;

    // Menggunakan exec() untuk menangkap kode status keberhasilan sejati (0 = Sukses)
    $cmd = "aws s3 cp " . escapeshellarg($file_tmp) . " " . escapeshellarg($s3_path) . " --region " . escapeshellarg($s3_region) . " 2>&1";
    exec($cmd, $output_array, $return_var);
    $output_text = implode("<br>", $output_array);

    if ($return_var === 0) {
        // Jika AWS CLI benar-benar sukses (Status 0)
        $stmt = $conn->prepare("INSERT INTO materials (title, file_name) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("ss", $judul, $file_name);
            $stmt->execute();
            $pesan = "<div class='alert alert-success fw-bold'><i class='fa-solid fa-check-circle me-2'></i>Sempurna! Materi berhasil diunggah ke AWS S3 dan Database.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menyimpan ke Database (Tabel mungkin belum ada): " . $conn->error . "</div>";
        }
    } else {
        // Jika AWS CLI gagal (misal karena IAM Role atau Nama Bucket salah)
        $pesan = "<div class='alert alert-danger'><i class='fa-solid fa-triangle-exclamation me-2'></i><strong>AWS S3 Error:</strong><br><small class='font-monospace'>" . $output_text . "</small></div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Upload Materi S3 - EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light p-5">
    <div class="container" style="max-width: 600px;">
        <div class="card shadow-sm p-4 border-0" style="border-radius: 12px;">
            <h4 class="mb-4 text-primary fw-bold"><i class="fa-solid fa-cloud-arrow-up me-2"></i>Upload Materi S3</h4>
            <?= $pesan ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Judul Materi</label>
                    <input type="text" class="form-control bg-light" name="judul" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Pilih File (PDF/DOCX)</label>
                    <input type="file" class="form-control bg-light" name="file_materi" required>
                </div>
                <button type="submit" class="btn btn-warning w-100 fw-bold">Upload ke Cloud</button>
                <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-2">Kembali ke Dashboard</a>
            </form>
        </div>
    </div>
</body>
</html>