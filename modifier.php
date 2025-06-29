<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
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
        $stmt = $conn->prepare("UPDATE evenements SET titre=?, lieu=?, date=?, description=?, image=? WHERE id=?");
        $stmt->bind_param("sssssi", $titre, $lieu, $date, $description, $imageName, $id);
        // Gérer l’image si une nouvelle est envoyée
$imageName = $event['image']; // image actuelle par défaut

if (!empty($_FILES['image']['name'])) {
    $originalName = basename($_FILES['image']['name']);
    $cleanName = preg_replace('/[^A-Za-z0-9_.-]/', '_', $originalName);
    $newImageName = uniqid() . '_' . $cleanName;
    $target = 'uploads/' . $newImageName;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Supprimer l'ancienne image si elle existe
        if (!empty($event['image']) && file_exists("uploads/" . $event['image'])) {
            unlink("uploads/" . $event['image']);
        }
        $imageName = $newImageName;
    } else {
        $message = "Erreur lors du téléchargement de la nouvelle image.";
    }
}

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
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon" />

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

    <form method="POST" enctype="multipart/form-data">
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
        <div class="mb-3">
            <label for="image" class="form-label">Changer l’image (optionnel)</label>
            <input type="file" class="form-control" name="image" id="image" accept="image/*">
        </div>
        <?php if (!empty($event['image'])): ?>
            <p>Image actuelle :</p>
            <img src="uploads/<?= htmlspecialchars($event['image']) ?>" alt="Image actuelle" style="max-width: 100%; height: auto;">
        <?php endif; ?>

        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
