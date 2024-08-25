<?php
require 'db_config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = :token");
    $stmt->execute(['token' => $token]);

    if ($stmt->rowCount()) {
        echo 'Sikeres account aktiváció!';
    } else {
        echo 'Téves aktivációs link, vagy az account már aktiválva van';
    }
} else {
    echo 'Nincs megadva az aktivációs token';
}
?>

