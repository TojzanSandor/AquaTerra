<?php
require 'db_config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$errors = [];
$success = '';


$user_id = $_SESSION['user_id'];
try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors[] = "Felhasználó nem található.";
    }
} catch (PDOException $e) {
    $errors[] = "Adatbázis hiba: " . $e->getMessage();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST["username"]));
    $email = htmlspecialchars(trim($_POST["email"]));
    $companyName = htmlspecialchars(trim($_POST["companyName"]));
    $companyWebsite = htmlspecialchars(trim($_POST["companyWebsite"]));
    $companyAddress = htmlspecialchars(trim($_POST["companyAddress"]));
    $companyDescription = htmlspecialchars(trim($_POST["companyDescription"]));

    try {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :user_id");
        $stmt->execute(['email' => $email, 'user_id' => $user_id]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Ez az e-mail cím már foglalt!";
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, company_name = :companyName, company_website = :companyWebsite, company_address = :companyAddress, company_description = :companyDescription WHERE id = :user_id");
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'companyName' => $companyName,
                'companyWebsite' => $companyWebsite,
                'companyAddress' => $companyAddress,
                'companyDescription' => $companyDescription,
                'user_id' => $user_id
            ]);
            $success = "Profil sikeresen frissítve!";
            $_SESSION['username'] = $username; 
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
    <title>Profil szerkesztése</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Profil szerkesztése</h2>
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
    
    <form action="edit_profile.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Felhasználónév</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="companyName" class="form-label">Cég neve</label>
            <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo htmlspecialchars($user['company_name']); ?>">
        </div>
        <div class="mb-3">
            <label for="companyWebsite" class="form-label">Cég weboldala</label>
            <input type="url" class="form-control" id="companyWebsite" name="companyWebsite" value="<?php echo htmlspecialchars($user['company_website']); ?>">
        </div>
        <div class="mb-3">
            <label for="companyAddress" class="form-label">Cég címe</label>
            <input type="text" class="form-control" id="companyAddress" name="companyAddress" value="<?php echo htmlspecialchars($user['company_address']); ?>">
        </div>
        <div class="mb-3">
            <label for="companyDescription" class="form-label">Cég leírása</label>
            <textarea class="form-control" id="companyDescription" name="companyDescription"><?php echo htmlspecialchars($user['company_description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Mentés</button>
    </form>
    <a href="user_dashboard.php" class="btn btn-secondary mt-3">Vissza a profilhoz</a>
</div>
</body>
</html>
