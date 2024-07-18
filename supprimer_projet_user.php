<?php
session_start(); // Démarrer la session
require_once(__DIR__ . '/admin/db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si un ID de projet est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: user_dash.php");
    exit();
}

$project_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Vérifier si le projet appartient à l'utilisateur
$sql = "SELECT * FROM projects WHERE id = :project_id AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$project = $stmt->fetch(PDO::FETCH_ASSOC);

if ($project) {
    // Supprimer le projet
    $sqlDelete = "DELETE FROM projects WHERE id = :project_id AND user_id = :user_id";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmtDelete->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmtDelete->execute();

    if ($stmtDelete->rowCount() > 0) {
        echo "Projet supprimé avec succès.";
    } else {
        echo "Erreur: La suppression du projet a échoué.";
    }
} else {
    echo "Projet introuvable ou vous n'êtes pas autorisé à le supprimer.";
}

header("Location: user_dash.php");
exit();
?>
