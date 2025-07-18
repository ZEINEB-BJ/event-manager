<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
function sanitizeFileName($filename) {
    $filename = preg_replace('/[^A-Za-z0-9_.-]/', '_', $filename); // remplace caractères spéciaux par "_"
    return $filename;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $date = $_POST['date'] ?? '';
    $description = trim($_POST['description'] ?? '');

    $imageName = '';
if (!empty($_FILES['image']['name'])) {
    $originalName = basename($_FILES['image']['name']);
    $cleanName = sanitizeFileName($originalName);
    $imageName = uniqid() . '_' . $cleanName;
    $target = "uploads/" . $imageName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        $error = "Erreur lors de l'enregistrement de l'image.";
    }
}


    if ($titre && $lieu && $date && $description) {
        $stmt = $conn->prepare("INSERT INTO evenements(titre, lieu, date, description, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $titre, $lieu, $date, $description, $imageName);


        if ($stmt->execute()) {
            // Rediriger vers index.php avec message succès
            header("Location: index.php?message=success");
            exit();
        } else {
            $error = "Erreur lors de l'ajout de l'événement.";
        }

        $stmt->close();
    } else {
        $error = "Veuillez remplir tous les champs !";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Ajouter un événement - Event Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body { background-color: #f8f9fa; }
        .form-container {
            max-width: 600px; margin: 60px auto; padding: 25px;
            background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { margin-bottom: 30px; }
    </style>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon" />

</head>
<body>

<div class="form-container">
    <h2 class="text-center">Ajouter un nouvel événement</h2>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-warning" role="alert">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titre" class="form-label">Titre</label>
            <input type="text" class="form-control" name="titre" id="titre" placeholder="Titre de l'événement" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="lieu" class="form-label">Lieu</label>
            <input type="text" class="form-control" name="lieu" id="lieu" placeholder="Lieu" value="<?= htmlspecialchars($_POST['lieu'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" id="date" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="description" rows="4" placeholder="Description de l'événement" required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image de l’événement</label>
            <input type="file" class="form-control" name="image" id="image" accept="image/*">
        </div>
        <div class="d-grid gap-2">
            <button class="btn btn-primary" type="submit">Ajouter</button>
        </div>
    </form>
</div>

</body>
</html>
