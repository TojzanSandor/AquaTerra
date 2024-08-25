<?php
require 'db_config.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);

    // Fetch all pending job posts (is_active = 0)
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE is_active = 0 ORDER BY application_deadline DESC");
    $stmt->execute();
    $pending_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$pending_jobs) {
        echo "Nincsenek jóváhagyásra váró állások.";
    }

} catch (PDOException $e) {
    die("Adatbázis hiba: " . $e->getMessage());
}

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['position']) . "</td>";
    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
    echo "<td>
        <a href='approve_job.php?id=" . intval($row['id']) . "' class='btn btn-success'>Elfogadás</a>
        <a href='reject_job.php?id=" . intval($row['id']) . "' class='btn btn-danger'>Elutasítás</a>
    </td>";
    echo "</tr>";
}

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'approved') {
        echo "<div class='alert alert-success'>A munka elfogadva.</div>";
    } elseif ($_GET['status'] == 'rejected') {
        echo "<div class='alert alert-danger'>A munka elutasítva.</div>";
    }
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Állásajánlatok Jóváhagyása</title>
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="container mt-5">
    <h2>Állásajánlatok Jóváhagyása</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Pozíció</th>
                <th>Leírás</th>
                <th>Cég</th>
                <th>Helyszín</th>
                <th>Határidő</th>
                <th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($pending_jobs)) : ?>
            <?php foreach ($pending_jobs as $job) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['job_title']); ?></td>
                    <td><?php echo htmlspecialchars($job['job_description']); ?></td>
                    <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($job['location']); ?></td>
                    <td><?php echo htmlspecialchars($job['application_deadline']); ?></td>
                    <td>
                        <a href="approve_job.php?id=<?php echo $job['id']; ?>" class="btn btn-success">Jóváhagyás</a>
                        <a href="reject_job.php?id=<?php echo $job['id']; ?>" class="btn btn-danger">Elutasítás</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <a href="admin_categories.php" class="btn btn-secondary mt-3">Vissza a Kategóriákhoz</a>
</div>
</body>
</html>