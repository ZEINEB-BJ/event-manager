<?php
require_once 'includes/db.php';
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
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
