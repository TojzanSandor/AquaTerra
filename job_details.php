<?php
session_start();
require 'db_config.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Érvénytelen kérés.");
}

$job_id = intval($_GET['id']);

try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = :id");
    $stmt->execute(['id' => $job_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$job) {
        die("A munka nem található.");
    }

} catch (PDOException $e) {
    die("Adatbázis hiba: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
    <title>Munka Részletek</title>
</head>
<body>
<div class="container mt-5">
    <h1><?php echo htmlspecialchars($job['job_title']); ?></h1>
    <p><strong>Cég:</strong> <?php echo htmlspecialchars($job['company_name']); ?></p>
    <p><strong>Leírás:</strong> <?php echo nl2br(htmlspecialchars($job['job_description'])); ?></p>
    <p><strong>Lokáció:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
    <p><strong>Kategória:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
    <p><strong>Jelentkezési Határidő:</strong> <?php echo htmlspecialchars($job['application_deadline']); ?></p>
    
    <a href="send_message.php?receiver_id=<?php echo $job['company_id']; ?>" class="btn btn-primary">Üzenet Küldés</a>
>
    <a href="job_listings.php" class="btn btn-primary">Vissza a Munka Listázásához</a>
</div>
</body>
</html>
