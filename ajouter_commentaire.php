<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout commentaire</title>
    <link rel="icon" href="assets/favicon.ico" type="image/x-icon" />

</head>
<body>
    
</body>
</html>
<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $evenement_id = intval($_POST['evenement_id']);
    $nom = trim($_POST['nom']);
    $commentaire = trim($_POST['commentaire']);

    if ($nom && $commentaire) {
        $stmt = $conn->prepare("INSERT INTO commentaires (evenement_id, nom, commentaire) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $evenement_id, $nom, $commentaire);
        $stmt->execute();
    }
}

header("Location: index.php");
exit;
?>
