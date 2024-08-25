<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit;
}

require 'db_config.php';

$user_id = $_SESSION['user_id'];
try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("Felhasználó nem található.");
    }
} catch (PDOException $e) {
    die("Adatbázis hiba: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cég Dashboard</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Üdvözöljük a Céges Irányítópulton, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <table class="table">
        <tr>
            <th>Felhasználónév</th>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
        </tr>
        <?php if ($user['role'] == 'company'): ?>
        <tr>
            <th>Cég neve</th>
            <td><?php echo htmlspecialchars($user['company_name']); ?></td>
        </tr>
        <tr>
            <th>Cég weboldala</th>
            <td><?php echo htmlspecialchars($user['company_website']); ?></td>
        </tr>
        <tr>
            <th>Cég címe</th>
            <td><?php echo htmlspecialchars($user['company_address']); ?></td>
        </tr>
        <tr>
            <th>Cég leírása</th>
            <td><?php echo htmlspecialchars($user['company_description']); ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <a href="edit_profile.php" class="btn btn-primary mt-3">Profil szerkesztése</a>
    <a href="change_password.php" class="btn btn-warning mt-3">Jelszó megváltoztatása</a>
    <a href="logout.php" class="btn btn-danger mt-3">Kijelentkezés</a>
    <a href="post_job.php" class="btn btn-primary mt-3">Álláshirdetés közzététele</a>

    <a href="job_listings.php" class="btn btn-primary mt-3">Állásajánlatok megtekintése</a>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">Állásportál</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="inbox.php">Inbox</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="sent_messages.php">Elküldött Üzenetek</a>
            </li>
        </ul>
    </div>
</nav>


</div>
</body>
</html>
