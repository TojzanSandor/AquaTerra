<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$receiver_id_prefilled = isset($_GET['receiver_id']) ? intval($_GET['receiver_id']) : 0;
$errors = [];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $receiver_id = intval($_POST['receiver_id']);
    $message_text = trim($_POST['message_text']);
    $sender_id = $_SESSION['user_id'];

    if (empty($message_text)) {
        $errors[] = "Message cannot be empty.";
    }

    if (empty($errors)) {
        try {
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (:sender_id, :receiver_id, :message_text)");
            $stmt->execute([
                'sender_id' => $sender_id,
                'receiver_id' => $receiver_id,
                'message_text' => $message_text
            ]);
            $success = "Message sent successfully.";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
    <title>Üzenet Küldés</title>
</head>
<body>
<div class="container mt-5">
    <h1>Üzenet Küldés</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php echo implode('<br>', $errors); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form action="send_message.php" method="POST">
        <div class="mb-3">
            <label for="receiver_id" class="form-label">Recipiens</label>
            <select name="receiver_id" id="receiver_id" class="form-control" required>
                <!-- Populate this with a list of users/companies -->
                <?php
                $stmt = $pdo->query("SELECT id, username FROM users WHERE id != {$_SESSION['user_id']}");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $selected = ($row['id'] == $receiver_id_prefilled) ? 'selected' : '';
                    echo "<option value='{$row['id']}' $selected>{$row['username']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="message_text" class="form-label">Üzenet</label>
            <textarea name="message_text" id="message_text" class="form-control" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Üzenet Küldés</button>
        <a href="job_listings.php" class="btn btn-primary">Vissza a Listához</a>
    </form>
</div>
</body>
</html>

