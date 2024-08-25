<?php
require 'db_config.php';
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

function validateInput($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = validateInput($_POST["email"]);

    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); 
            $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires) VALUES (:email, :token, :expires)");
            $stmt->execute(['email' => $email, 'token' => $token, 'expires' => $expires]);
            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'allasborzeit@gmail.com'; 
            $mail->Password = 'yfhofepazbuqtnpb'; 
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('allasborzeit@gmail.com', 'Allas borze IT');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Kattints ide a jelszavad visszaállításához: <a href='https://localhost/web/password_reset.php?token=$token'>Jelszó visszaállítása</a>";

            if ($mail->send()) {
                $success = 'Egy e-mailt küldtünk a jelszó visszaállítási utasításokkal.';
            } else {
                $errors[] = 'Nem sikerült elküldeni az e-mailt. Próbáld újra később.';
            }
        } else {
            $errors[] = 'Ez az e-mail cím nincs regisztrálva.';
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
    <title>Jelszó visszaállítása</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Jelszó visszaállítása</h2>
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
    <?php else: ?>
        <form action="password_reset_request.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email cím</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Küldés</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
