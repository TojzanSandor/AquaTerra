<?php
session_start();
require 'db_config.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("UPDATE jobs SET is_active = 1 WHERE id = :job_id");
        $stmt->execute(['job_id' => $job_id]);

        header("Location: admin_job_approval.php?status=approved");
        exit;

    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    header("Location: admin_job_approval.php?status=error");
    exit;
}
?>
