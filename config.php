<?php
$db_host = "REPLACE_WITH_RDS_ENDPOINT"; 
$db_user = "REPLACE_WITH_DB_USER";
$db_pass = "REPLACE_WITH_DB_PASS";
$db_name = "eduflowdb";

$s3_bucket = "eduflow-tfstate-final-project4";
$s3_region = "ap-southeast-1";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Koneksi ke database RDS Gagal: " . $conn->connect_error);
}
?>