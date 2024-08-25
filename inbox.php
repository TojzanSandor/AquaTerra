<?php
session_start();
require 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    
    $stmt = $pdo->prepare("SELECT m.id, m.message_text, timestamp, u.username as sender_name
                           FROM messages m
                           JOIN users u ON m.sender_id = u.id
                           WHERE m.receiver_id = :user_id
                           ORDER BY timestamp DESC");
    $stmt->execute(['user_id' => $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Adatbázis hiba: " . $e->getMessage();
    $messages = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
    <title>Inbox</title>
</head>
<body>
<div class="container mt-5">
    <h1>Inbox</h1>
    
    <?php if (!empty($messages)): ?>
        <ul class="list-group">
            <?php foreach ($messages as $message): ?>
                <li class="list-group-item">
                    <strong>Küldő:</strong> <?php echo htmlspecialchars($message['sender_name']); ?><br>
                    <strong>Üzenet:</strong> <?php echo htmlspecialchars($message['message_text']); ?><br>
                    <small><strong>Küldési Dátum:</strong> <?php echo htmlspecialchars($message['timestamp']); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Nincs megkapott üzenet</p>
    <?php endif; ?>
</div>
</body>
</html>
