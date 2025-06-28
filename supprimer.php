<?php
require_once 'includes/db.php';

if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM evenements WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: index.php?message=delete_success");
        exit;
    } else {
        echo "Erreur lors de la suppression.";
    }
} else {
    echo "ID invalide.";
}
?>
