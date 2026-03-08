<?php
require 'config.php';

// Jika ada form yang di-submit (Pendaftaran Siswa Baru)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    
    if ($stmt->execute()) {
        $pesan = "<div style='color: green;'>Pendaftaran berhasil! Data tersimpan di AWS RDS.</div>";
    } else {
        $pesan = "<div style='color: red;'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EduFlow LMS - Running on AWS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f9;}
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        input { width: 95%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 4px;}
        button { background: #ff9900; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold;}
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #232f3e; color: white; }
    </style>
</head>
<body>

<div class="container">
    <h2 style="text-align: center; color: #232f3e;">Selamat Datang di EduFlow LMS</h2>
    <p style="text-align: center;">Di-hosting pada <b>AWS EC2 Auto Scaling</b> & <b>Amazon RDS MySQL</b></p>
    
    <?php if(isset($pesan)) echo $pesan; ?>

    <h3>Form Pendaftaran Siswa</h3>
    <form method="POST" action="">
        <input type="text" name="name" placeholder="Nama Lengkap" required>
        <input type="email" name="email" placeholder="Alamat Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Daftar Sekarang</button>
    </form>

    <hr>

    <h3>Daftar Siswa Terdaftar (Live dari RDS)</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Nama Siswa</th>
            <th>Email</th>
            <th>Waktu Daftar</th>
        </tr>
        <?php
        // Mengambil data dari RDS
        $sql_select = "SELECT id, name, email, created_at FROM users ORDER BY id DESC";
        $result = $conn->query($sql_select);

        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr><td>" . $row["id"]. "</td><td>" . htmlspecialchars($row["name"]). "</td><td>" . htmlspecialchars($row["email"]). "</td><td>" . $row["created_at"]. "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center;'>Belum ada siswa yang mendaftar. <br><a href='setup.php'>Klik di sini untuk inisialisasi tabel database pertama kali.</a></td></tr>";
        }
        $conn->close();
        ?>
    </table>
</div>

</body>
</html>