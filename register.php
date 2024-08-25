<?php
require 'db_config.php';
require 'vendor/autoload.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }


    function validateInput($data) {
        return htmlspecialchars(stripslashes(trim($data)));
    }


    $errors = [];
    $success = '';


    $email = validateInput($_POST["email"]);
    $username = validateInput($_POST["username"]);
    $password = validateInput($_POST["password"]);
    $confirm_password = validateInput($_POST["confirm_password"]);
    $userType = validateInput($_POST["userType"]);
    $companyName = $companyWebsite = $companyAddress = $companyDescription = "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters long and include a mix of letters and numbers.";
    }

 
    if ($userType == "company") {
        $companyName = validateInput($_POST["companyName"]);
        $companyWebsite = validateInput($_POST["companyWebsite"]);
        $companyAddress = validateInput($_POST["companyAddress"]);
        $companyDescription = validateInput($_POST["companyDescription"]);
    }


    if ($password !== $confirm_password) {
        $errors[] = "A jelszavak nem egyeznek!";
    }


    try {
        $pdo = new PDO($dsn, $db_username, $db_password, $options);
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Ez az e-mail cím már foglalt!";
        }
    } catch (PDOException $e) {
        $errors[] = "Adatbázis hiba: " . $e->getMessage();
    }


    if (empty($errors)) {
        $activation_token = bin2hex(random_bytes(16));
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $pdo->beginTransaction();

            $query = "INSERT INTO users (email, username, password_hash, is_active, activation_token, role, company_name, company_website, company_address, company_description)
                      VALUES (:email, :username, :password_hash, :is_active, :activation_token, :role, :company_name, :company_website, :company_address, :company_description)";
            
            $stmt = $pdo->prepare($query);
            
            $stmt->execute([
                ':email' => $email,
                ':username' => $username,
                ':password_hash' => $hashed_password,
                ':is_active' => 0, 
                ':activation_token' => $activation_token,
                ':role' => $userType,
                ':company_name' => $companyName,
                ':company_website' => $companyWebsite,
                ':company_address' => $companyAddress,
                ':company_description' => $companyDescription,
            ]);

            $pdo->commit();


            $mail = new PHPMailer(true);

            try {

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'allasborzeit@gmail.com';
                $mail->Password = 'yfhofepazbuqtnpb'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;


                $mail->setFrom('allasborzeit@gmail.com', 'Allas borze IT');
                $mail->addAddress($email, $username);


                 $activation_link = "http://localhost/web/activate.php?token=" . $activation_token;
                 $mail->isHTML(true);
                 $mail->Subject = 'Account Activation';
                 $mail->Body = "Please click the following link to activate your account: <a href='$activation_link'>$activation_link</a>";
                 $mail->AltBody = "Please click the following link to activate your account: $activation_link";

                $mail->send();
                echo 'Registration successful! Please check your email to activate your account.';
            } catch (Exception $e) {
                echo "Registration successful, but the activation email could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Regisztráció</h2>
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
    
    <form method="POST" action="register.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token, ENT_QUOTES, 'UTF-8'); ?>">

    </form>

    <a href="Urlap.php" class="btn btn-primary">Vissza a regisztrációhoz</a>
</div>
</body>
</html>
