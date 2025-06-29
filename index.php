<?php 
require_once 'includes/db.php';
session_start();

$events_per_page = 6;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $events_per_page;
$totalEvents = $conn->query("SELECT COUNT(*) AS total FROM evenements")->fetch_assoc()['total'];
$totalPages = ceil($totalEvents / $events_per_page);


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

// Récupérer les 3 derniers événements pour le carrousel
$carouselSql = "SELECT * FROM evenements ORDER BY date DESC LIMIT 3";
$carouselResult = $conn->query($carouselSql);

$search = trim($search);

if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $sql = "SELECT * FROM evenements 
            WHERE titre LIKE '%$search_escaped%' 
            OR lieu LIKE '%$search_escaped%' 
            ORDER BY date ASC";
} else {
    $sql = "SELECT * FROM evenements ORDER BY date ASC LIMIT $events_per_page OFFSET $offset";

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
        .card { box-shadow: 0 4px 15px rgba(0,0,0,0.1); border: none; border-radius: 12px; transition: transform 0.3s ease; background: #fff; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .card-title { font-weight: 600; color: #343a40; }
        .card-subtitle { font-style: italic; font-size: 0.9rem; color: #6c757d; }
        .btn-danger, .btn-warning { border-radius: 20px; padding: 6px 20px; font-weight: 600; }
        .search-bar { max-width: 400px; margin: 20px auto 40px; }
        footer { margin-top: auto; background: #343a40; color: #bbb; padding: 20px 0; }
        .comment-block { background: #f1f1f1; padding: 10px; margin-top: 10px; border-radius: 5px; }
        body.dark-mode {
    background-color: #121212;
    color: #eee;
}

body.dark-mode .navbar {
    background-color: #1f1f1f !important;
}

body.dark-mode .card {
    background-color: #1e1e1e;
    color: #ccc;
}

body.dark-mode .card-subtitle {
    color: #aaa;
}

body.dark-mode footer {
    background-color: #1f1f1f;
    color: #999;
}

body.dark-mode .form-control {
    background-color: #2c2c2c;
    color: #eee;
    border-color: #444;
}

    </style>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon" />
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
    <img src="assets/logo.png" alt="Logo" style="height: 30px; margin-right: 10px;">
    Event Manager
</a>

   <div class="ms-auto d-flex align-items-center gap-2">
    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <a href="ajouter.php" class="btn btn-primary me-2">Ajouter un événement</a>
        <a href="logout.php" class="btn btn-secondary">Déconnexion</a>
    <?php else: ?>
        <a href="login.php" class="btn btn-primary">Connexion</a>
    <?php endif; ?>

    <button id="themeToggle" class="btn btn-outline-light btn-sm" title="Changer le thème">🌓</button>
</div>

</nav>

<div class="container mt50">

    <?php if ($message): ?>
        <div class="alert <?= $message_class ?> rounded-3" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <!-- Début Carrousel Bootstrap -->
    <?php if ($carouselResult && $carouselResult->num_rows > 0): ?>
    <div id="eventsCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php
            $active = "active";
            while ($slide = $carouselResult->fetch_assoc()):
                $dateFormatted = date('d M Y', strtotime($slide['date']));
                $imgPath = !empty($slide['image']) ? "uploads/" . htmlspecialchars($slide['image']) : null;
            ?>
            <div class="carousel-item <?= $active ?>">
                <?php if ($imgPath): ?>
                    <img src="<?= $imgPath ?>" class="d-block w-100" alt="Image de l'événement" style="max-height:400px; object-fit:cover;">
                <?php else: ?>
                    <div style="height:400px; background:#ddd; display:flex; align-items:center; justify-content:center;">
                        <span class="text-muted">Pas d'image disponible</span>
                    </div>
                <?php endif; ?>
                <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-2">
                    <h5><?= htmlspecialchars($slide['titre']) ?></h5>
                    <p><?= htmlspecialchars($slide['lieu']) ?> — <?= $dateFormatted ?></p>
                </div>
            </div>
            <?php
            $active = "";
            endwhile;
            ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#eventsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#eventsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
    <?php endif; ?>
    <!-- Fin Carrousel Bootstrap -->

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
                        " . (!empty($row['image']) ? "<img src='uploads/{$row['image']}' class='card-img-top' alt='Image de l’événement' style='max-height:200px; object-fit:cover;'>" : "") . "
                        <div class='card-body'>
                            <h5 class='card-title'>{$row['titre']}</h5>
                            <h6 class='card-subtitle mb-3'>{$row['lieu']} - {$dateFormatted}</h6>
                            <p class='card-text'>{$row['description']}</p>";

            if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
                echo "<div class='d-flex justify-content-between mb-3'>
                        <a href='modifier.php?id={$row['id']}' class='btn btn-warning btn-sm'>Modifier</a>
                        <a href='supprimer.php?id={$row['id']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');\">Supprimer</a>
                    </div>";
            }

            $event_id = (int)$row['id'];
            $comms = $conn->query("SELECT nom, commentaire, date_commentaire FROM commentaires WHERE evenement_id = $event_id ORDER BY date_commentaire DESC");
            if ($comms && $comms->num_rows > 0) {
                echo "<hr><h6>Commentaires :</h6>";
                while ($c = $comms->fetch_assoc()) {
                    echo "<div class='comment-block'><strong>" . htmlspecialchars($c['nom']) . "</strong><br>" . nl2br(htmlspecialchars($c['commentaire'])) . "</div>";
                }
            }

            echo "<hr>
                  <form method='POST' action='ajouter_commentaire.php'>
                      <input type='hidden' name='evenement_id' value='{$row['id']}'>
                      <div class='mb-2'>
                          <input type='text' name='nom' class='form-control form-control-sm' placeholder='Votre nom' required>
                      </div>
                      <div class='mb-2'>
                          <textarea name='commentaire' class='form-control form-control-sm' rows='2' placeholder='Votre commentaire' required></textarea>
                      </div>
                      <button type='submit' class='btn btn-outline-primary btn-sm'>Commenter</button>
                  </form>";

            echo "      </div>
                    </div>
                </div>";
        }
        ?>
    </div>
</div>
<?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination">
        <ul class="pagination justify-content-center mt-4">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>">&laquo;</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&q=<?= urlencode($search) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>">&raquo;</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<footer class="text-center">
    &copy; 2025 - Zeineb Ben Jeddou | <a href="https://github.com/ZEINEB-BJ" target="_blank" style="color:#bbb;">Mon GitHub</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Appliquer le thème sauvegardé
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
  }

  document.getElementById('themeToggle').addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
  });

  // Cacher le message automatiquement
  const alert = document.querySelector('.alert');
  if (alert) {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 3000);
  }
</script>


</body>
</html>
