<?php
require 'db_config.php';

session_start();

function validateInput($data) {
    return htmlspecialchars(trim($data));
}

$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = validateInput($_POST["username"]);
    $password = validateInput($_POST["password"]);

    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1");
        $stmt->execute(['username' => $username, 'email' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            if ($user['banned']) {  
                $errors[] = "This account has been banned. Please contact the administrator.";
            } elseif (!$user['is_active']) {
                $errors[] = "The account is not yet active. Please check your email for the activation link.";
            } else {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'company') {
                    header("Location: company_dashboard.php"); 
                } else {
                    header("Location: user_dashboard.php");
                }
                exit;
            }
        } else {
            $errors[] = "Invalid username/email or password!";
        }
    } catch (PDOException $e) {
        $errors[] = "Database error: " . $e->getMessage();
    }
}

$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
    
</head>
<body>
<div class="container mt-5">
    <h2>Bejelentkezés</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
        <div class="mb-3">
            <label for="username" class="form-label">Felhasználónév vagy Email</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Jelszó</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Bejelentkezés</button>
    </form>
    <p>Még nincs fiókod? <a href="urlap.php">Regisztráció</a></p>
    <p>Elfelejtett jelszó? <a href="password_reset_request.php">Jelszó visszaállítása</a></p>
</div>
</body>
</html>

