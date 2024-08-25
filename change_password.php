<?php
require 'db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = htmlspecialchars(trim($_POST["current_password"]));
    $new_password = htmlspecialchars(trim($_POST["new_password"]));
    $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
    $user_id = $_SESSION['user_id'];

    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($current_password, $user['password_hash'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password_hash WHERE id = :user_id");
                $stmt->execute(['password_hash' => $hashed_password, 'user_id' => $user_id]);
                $success = "Jelszó sikeresen megváltoztatva!";
            } else {
                $errors[] = "Az új jelszavak nem egyeznek!";
            }
        } else {
            $errors[] = "A jelenlegi jelszó helytelen!";
        }
    } catch (PDOException $e) {
        $errors[] = "Adatbázis hiba: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó megváltoztatása</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Jelszó megváltoztatása</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <p><?php echo $success; ?></p>
        </div>
    <?php endif; ?>
    
    <form action="change_password.php" method="POST">
        <div class="mb-3">
            <label for="current_password" class="form-label">Jelenlegi jelszó</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Új jelszó</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Új jelszó megerősítése</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Jelszó megváltoztatása</button>
    </form>
    <a href="user_dashboard.php" class="btn btn-secondary mt-3">Vissza a profilhoz</a>
</div>
</body>
</html>
