<?php
require 'db_config.php';

session_start();

function validateInput($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = validateInput($_POST["token"]);
    $password = validateInput($_POST["password"]);
    $confirm_password = validateInput($_POST["confirm_password"]);

    if ($password !== $confirm_password) {
        $errors[] = 'A jelszavak nem egyeznek.';
    } else {
        try {
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token AND expires > NOW() LIMIT 1");
            $stmt->execute(['token' => $token]);
            $reset = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($reset) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $pdo->prepare("UPDATE users SET password_hash = :password WHERE email = :email");
                $stmt->execute(['password' => $hashed_password, 'email' => $reset['email']]);

                $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
                $stmt->execute(['token' => $token]);

                $success = 'A jelszavad sikeresen visszaállítva. Most már bejelentkezhetsz az új jelszóval.';
            } else {
                $errors[] = 'Érvénytelen vagy lejárt token.';
            }
        } catch (PDOException $e) {
            $errors[] = "Adatbázis hiba: " . $e->getMessage();
        }
    }
} else if (isset($_GET['token'])) {
    $token = validateInput($_GET['token']);
} else {
    $errors[] = 'Érvénytelen token.';
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó Visszaállítása</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Jelszó Visszaállítása</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success">
            <p><?php echo $success; ?></p>
        </div>
    <?php elseif (isset($token)): ?>
        <form action="password_reset.php" method="POST">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            <div class="mb-3">
                <label for="password" class="form-label">Új Jelszó</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Jelszó Megerősítése</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Jelszó Visszaállítása</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

