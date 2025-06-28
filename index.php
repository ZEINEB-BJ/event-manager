<?php
require_once 'includes/db.php';
session_start();

$message = '';
$message_class = '';

if (isset($_GET['message'])) {
    if ($_GET['message'] === 'success') {
        $message = "Événement ajouté avec succès !";
        $message_class = "alert-success";
    } elseif ($_GET['message'] === 'delete_success') {
        $message = "Événement supprimé avec succès !";
        $message_class = "alert-success";
    } elseif ($_GET['message'] === 'update_success') {
        $message = "Événement modifié avec succès !";
        $message_class = "alert-success";
    }
}

$search = $_GET['q'] ?? '';
$search = trim($search);

if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $sql = "SELECT * FROM evenements 
            WHERE titre LIKE '%$search_escaped%' 
            OR lieu LIKE '%$search_escaped%' 
            ORDER BY date ASC";
} else {
    $sql = "SELECT * FROM evenements ORDER BY date ASC";
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background: #f5f7fa; min-height: 100vh; display: flex; flex-direction: column; }
        .mt50 { margin-top: 50px; }
        a { text-decoration: none; }
        .navbar-brand { font-weight: 700; font-size: 1.8rem; letter-spacing: 1px; }
        .card { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: none; border-radius: 12px; transition: transform 0.3s ease, box-shadow 0.3s ease; background: #fff; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .card-title { font-weight: 600; color: #343a40; }
        .card-subtitle { font-style: italic; font-size: 0.9rem; color: #6c757d; }
        .btn-danger, .btn-warning { border-radius: 20px; padding: 6px 20px; font-weight: 600; }
        .search-bar { max-width: 400px; margin: 0 auto 40px; }
        footer { margin-top: auto; background: #343a40; color: #bbb; padding: 20px 0; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="index.php">Event Manager</a>
    <div class="ms-auto">
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <a href="ajouter.php" class="btn btn-primary me-2">Ajouter un événement</a>
            <a href="logout.php" class="btn btn-secondary">Déconnexion</a>
        <?php else: ?>
            <a href="login.php" class="btn btn-primary">Connexion</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container mt50">

    <?php if ($message): ?>
        <div class="alert <?= $message_class ?> rounded-3" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form class="search-bar" method="GET" action="index.php">
        <input class="form-control form-control-lg" type="search" name="q" placeholder="Rechercher un événement..." value="<?= htmlspecialchars($search) ?>" />
    </form>

    <div class="row justify-content-center">
        <?php
        if ($result->num_rows === 0) {
            echo "<p class='text-center text-muted'>Aucun événement trouvé pour <strong>" . htmlspecialchars($search) . "</strong>.</p>";
        }

        while ($row = $result->fetch_assoc()) {
            $dateFormatted = date('d M Y', strtotime($row['date']));
            echo "<div class='col-md-4 mb-5'>
                    <div class='card'>
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['titre']}</h5>
                            <h6 class='card-subtitle mb-3'>{$row['lieu']} - {$dateFormatted}</h6>
                            <p class='card-text'>{$row['description']}</p>";

            // Affiche les boutons uniquement si admin connecté
            if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                echo "<div class='d-flex justify-content-between'>
                        <a href='modifier.php?id={$row['id']}' class='btn btn-warning btn-sm'>Modifier</a>
                        <a href='supprimer.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');\">Supprimer</a>
                    </div>";
            }

            echo    "</div>
                    </div>
                </div>";
        }
        ?>
    </div>
</div>

<footer class="text-center">
    &copy; 2025 - Zeineb Ben Jeddou | <a href="https://github.com/ZEINEB-BJ" target="_blank" style="color:#bbb;">Mon GitHub</a>
</footer>

<script>
  window.addEventListener('DOMContentLoaded', () => {
    const alert = document.querySelector('.alert');
    if (alert) {
      setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      }, 3000);
    }
  });
</script>

</body>
</html>
