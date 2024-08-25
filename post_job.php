<?php
session_start();
require 'db_config.php';

if ($_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    echo "You must be logged in as a company to post a job.";
    exit;
}

$errors = [];
$success = '';

function validateInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $position = validateInput($_POST["position"]);
    $description = validateInput($_POST["description"]);
    $location = validateInput($_POST["location"]);
    $deadline = validateInput($_POST["deadline"]);
    $company_name = validateInput($_POST["company_name"]);
    $company_id = $_SESSION['user_id'];

    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare("INSERT INTO jobs (company_id, job_title, job_description, location, application_deadline, is_active, is_expired, company_name) VALUES (:company_id, :position, :description, :location, :deadline, 0, 0, :company_name)");
        
        $stmt->execute([
            'company_id' => $company_id,
            'position' => $position,
            'description' => $description,
            'location' => $location,
            'deadline' => $deadline,
            'company_name' => $company_name
        ]);

        echo "Állásajánlat sikeresen feladva. Várja meg, amíg az adminisztrátor jóváhagyja.";
        
    } catch (PDOException $e) {
        echo "Adatbázis hiba: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Álláshirdetés Közzététele</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Új Álláshirdetés Közzététele</h2>
    <form action="post_job.php" method="POST">
        <div class="mb-3">
            <label for="position" class="form-label">Állás címe</label>
            <input type="text" class="form-control" id="position" name="position" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Állásleírás</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>

        <div class="mb-3">
            <label for="location" class="form-label">Helyszín</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>

        <div class="mb-3">
            <label for="deadline" class="form-label">Jelentkezési határidő</label>
            <input type="date" class="form-control" id="deadline" name="deadline" required>
        </div>

        <div class="mb-3">
            <label for="company_name" class="form-label">Cég neve</label>
            <input type="text" class="form-control" id="company_name" name="company_name" required>
        </div>

        <button type="submit" class="btn btn-primary">Hirdetés Közzététele</button>
    </form>
</div>
</body>
</html>
