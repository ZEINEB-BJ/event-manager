<?php
require_once 'includes/db.php';

// 1. Vérifier l'ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("ID invalide.");
}

$id = intval($_GET['id']);

// 2. Récupérer les infos de l'événement
$result = $conn->query("SELECT * FROM evenements WHERE id = $id");
$event = $result->fetch_assoc();

if (!$event) {
    die("Événement introuvable.");
}

// 3. Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'] ?? '';
    $lieu = $_POST['lieu'] ?? '';
    $date = $_POST['date'] ?? '';
    $description = $_POST['description'] ?? '';

    if ($titre && $lieu && $date && $description) {
        $stmt = $conn->prepare("UPDATE evenements SET titre=?, lieu=?, date=?, description=? WHERE id=?");
        $stmt->bind_param("ssssi", $titre, $lieu, $date, $description, $id);
        $stmt->execute();

        header("Location: index.php?message=update_success");
        exit;
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un événement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .container { max-width: 600px; margin-top: 50px; }
    </style>
</head>
<body>

<div class="container">
    <h2 class="mb-4">Modifier l'événement</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Titre</label>
            <input type="text" class="form-control" name="titre" value="<?= htmlspecialchars($event['titre']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Lieu</label>
            <input type="text" class="form-control" name="lieu" value="<?= htmlspecialchars($event['lieu']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="date" value="<?= $event['date'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="4" required><?= htmlspecialchars($event['description']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
