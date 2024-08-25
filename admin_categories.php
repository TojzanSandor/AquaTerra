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

// Handling form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $category_name = trim($_POST['category_name']);
        if (empty($category_name)) {
            $errors[] = "The category name cannot be empty.";
        } else {
            try {
                $pdo = new PDO($dsn, $db_username, $db_password, $options);
                $stmt = $pdo->prepare("INSERT INTO job_categories (category_name) VALUES (:category_name)");
                $stmt->execute(['category_name' => $category_name]);
                $success = "Category added successfully!";
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['edit_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = trim($_POST['category_name']);
        if (empty($category_name)) {
            $errors[] = "A kategória név mezője nem lehet üres";
        } else {
            try {
                $pdo = new PDO($dsn, $db_username, $db_password, $options);
                $stmt = $pdo->prepare("UPDATE job_categories SET category_name = :category_name WHERE id = :id");
                $stmt->execute(['category_name' => $category_name, 'id' => $category_id]);
                $success = "Sikeres kategória változtatás!";
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['delete_category'])) {
        $category_id = $_POST['category_id'];
        try {
            $pdo = new PDO($dsn, $db_username, $db_password, $options);
            $stmt = $pdo->prepare("DELETE FROM job_categories WHERE id = :id");
            $stmt->execute(['id' => $category_id]);
            $success = "Sikeres kategória kitörlés!";
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch categories
try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    $stmt = $pdo->query("SELECT * FROM job_categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Munka Kategóriák Kezelése</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Munka Kategóriák Kezelése</h2>
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

    <!-- Add Category -->
    <form method="POST">
        <div class="mb-3">
            <label for="category_name" class="form-label">Kategória Neve</label>
            <input type="text" class="form-control" id="category_name" name="category_name" required>
        </div>
        <button type="submit" name="add_category" class="btn btn-primary">Kategória Hozzáadása</button>
    </form>

    <!-- Existing Categories -->
    <h3 class="mt-5">Kategóriák</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kategória Neve</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><?php echo $category['id']; ?></td>
                    <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                    <td>
                        <!-- Edit Form -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                            <input type="text" name="category_name" value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
                            <button type="submit" name="edit_category" class="btn btn-warning">Edit</button>
                        </form>
                        
                        <!-- Delete Form -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                            <button type="submit" name="delete_category" class="btn btn-danger" onclick="return confirm('Biztos ki akarja törölni ezt a kategóriát?');">Törlés</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
     <!-- Add a link/button to navigate to the job approval page -->
     <a href="admin_job_approval.php" class="btn btn-secondary mt-3">Állások Jóváhagyása</a>
     <a href="job_listings.php" class="btn btn-primary mt-3">Állásajánlatok megtekintése</a>
     <a href="manage_users.php" class="btn btn-primary mt-3">Manage Users</a>
     <a href="logout.php" class="btn btn-danger mt-3">Kijelentkezés</a>
</div>
</body>
</html>
