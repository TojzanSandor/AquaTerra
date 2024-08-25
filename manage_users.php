<?php
require 'db_config.php';
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$errors = [];
$success = '';

// Handle user banning and unbanning
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ban_user'])) {
        $user_id = $_POST['user_id'];
        try {
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $stmt = $pdo->prepare("UPDATE users SET banned = 1 WHERE id = :id");
            $stmt->execute(['id' => $user_id]);
            $success = "User has been banned successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    } elseif (isset($_POST['unban_user'])) {
        $user_id = $_POST['user_id'];
        try {
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $stmt = $pdo->prepare("UPDATE users SET banned = 0 WHERE id = :id");
            $stmt->execute(['id' => $user_id]);
            $success = "User has been unbanned successfully!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch all users
try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    $stmt = $pdo->query("SELECT id, username, email, banned FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Felhasználók Kezelése</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Felhasználók Kezelése</h2>
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

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Felhasználónév</th>
                <th>Email</th>
                <th>Státusz</th>
                <th>Ban/Unban</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['banned'] ? 'Banned' : 'Active'; ?></td>
                    <td>
                        <?php if (!$user['banned']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="ban_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to ban this user?');">Ban</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="unban_user" class="btn btn-success" onclick="return confirm('Are you sure you want to unban this user?');">Unban</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Add a link to navigate back to the admin dashboard -->
    <a href="admin_categories.php" class="btn btn-secondary mt-3">Vissza a Dashboard-ra</a>

</div>
</body>
</html>

