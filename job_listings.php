<?php
session_start();
require 'db_config.php';

try {
    $pdo = new PDO($dsn, $db_username, $db_password, $options);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $query = "SELECT * FROM jobs WHERE is_active = 1";
    $params = [];


    if (!empty($_GET['title'])) {
        $query .= " AND job_title LIKE :title";
        $params[':title'] = "%" . $_GET['title'] . "%";
    }

    if (!empty($_GET['location'])) {
        $query .= " AND location LIKE :location";
        $params[':location'] = "%" . $_GET['location'] . "%";
    }

    if (!empty($_GET['category'])) {
        $query .= " AND category = :category";
        $params[':category'] = $_GET['category'];
    }

    if (!empty($_GET['deadline'])) {
        $query .= " AND application_deadline <= :deadline";
        $params[':deadline'] = $_GET['deadline'];
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $pdo->query("SELECT * FROM jobs WHERE is_active = 1 AND is_expired = 0");
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Adatbázis hiba: " . $e->getMessage();
    $jobs = [];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="startbootstrap-agency-gh-pages\css\styles.css" rel="stylesheet" type="text/css">
    <title>Munka Listázás</title>
    <style>
        .expired {
            background-color: #f8d7da;
            color: #721c24;
        }
        .expired .card-title {
            text-decoration: line-through;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1>Munka Listázás</h1>

    <form method="GET" action="job_listings.php" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <input type="text" name="title" class="form-control" placeholder="Munka Cím">
            </div>
            <div class="col-md-3">
                <input type="text" name="location" class="form-control" placeholder="Lokáció">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-control">
                    <option value="">Kategória Kiválasztás</option>
                    <option value="IT">IT</option>
                    <option value="Marketing">Marketing</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="deadline" class="form-control" placeholder="Határidő">
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Keresés</button>
    </form>

    <div class="row">
        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <?php
                    $is_expired = strtotime($job['application_deadline']) < time();
                    $card_class = $is_expired ? 'expired' : '';
                ?>
                <div class="col-md-4 mb-3">
                    <div class="card <?php echo $card_class; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($job['job_title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($job['job_description']); ?></p>
                            <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                            <?php if ($is_expired): ?>
                                <p><strong>Status:</strong> Expired</p>
                            <?php endif; ?>
                            <a href="job_details.php?id=<?php echo $job['id']; ?>" class="btn btn-primary">Részletek Megtekintése</a>
                            <a href="send_message.php?receiver_id=<?php echo $job['company_id']; ?>" class="btn btn-primary">Üzenet Küldés</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Jelenleg nincs elérhető munka</p>
        <?php endif; ?>
    </div>


      <div class="mt-4">
        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_categories.php" class="btn btn-secondary">Visszatérés az Admin Műszerfalára</a>
            <?php elseif ($_SESSION['role'] === 'company'): ?>
                <a href="company_dashboard.php" class="btn btn-secondary">Visszatérés a Cég Műszerfalára</a>
            <?php else: ?>
                <a href="user_dashboard.php" class="btn btn-secondary">Visszatérés a Felhasználó Műszerfalára</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="Urlap.php" class="btn btn-secondary">Regisztrálás</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>