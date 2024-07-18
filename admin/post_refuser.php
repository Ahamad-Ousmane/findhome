<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
require_once(__DIR__ . '/db.php');

// Vérifier si un ID de notification est passé en paramètre
if (!isset($_POST['id']) || !is_numeric($_POST['id']) || !isset($_POST['user_id']) || !is_numeric($_POST['user_id'])) {
    header("Location: dash_user.php");
    exit();
}

$notification_id = $_POST['id'];
$user_id = $_POST['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '';

    // Redirection vers refuser_post.php avec les paramètres nécessaires
    header("Location: refuser_post.php?id=" . urlencode($notification_id) . 
       "&user_id=" . urlencode($user_id) . 
       "&reason=" . urlencode($reason));
    exit();
}

// Récupérer la table des administrateurs pour potentiellement afficher des informations supplémentaires
$sqlAdmin = "SELECT * FROM admin WHERE id = :admin_id";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->bindParam(':admin_id', $admin_id);
$stmtAdmin->execute();
$Admin = $stmtAdmin->fetch();
?>
