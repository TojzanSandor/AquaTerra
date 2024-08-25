<?php
require 'db_config.php';

if (isset($_GET['id'])) {
    $job_id = intval($_GET['id']);

    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = :id");
        $stmt->execute(['id' => $job_id]);

        header("Location: admin_job_approval.php?status=rejected");
        exit;
    } catch (PDOException $e) {
        die("Adatbázis hiba: " . $e->getMessage());
    }
} else {
    die("Érvénytelen kérés.");
}
?>
