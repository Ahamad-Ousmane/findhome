<?php

session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

require_once(__DIR__ . '/db.php');

// Vérifier si un ID de notification et un ID d'utilisateur sont passés en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    header("Location: dash_user.php");
    exit();
}

$notif_id = $_GET['id'];
$notif_user_id = $_GET['user_id'];

// Mettre à jour la propriété pour confirmer son ajout
$sql = "UPDATE properties p 
        JOIN notifications n ON n.propertie_id = p.id 
        SET p.is_confirmed = true 
        WHERE n.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $notif_id, PDO::PARAM_INT);
$result = $stmt->execute();

if ($result) {
    // Récupérer l'ID de la propriété associée à la notification
    $sql_propertie_id = "SELECT propertie_id FROM notifications WHERE id = :id";
    $stmt_propertie_id = $pdo->prepare($sql_propertie_id);
    $stmt_propertie_id->bindParam(':id', $notif_id, PDO::PARAM_INT);
    $stmt_propertie_id->execute();
    $propertie_id = $stmt_propertie_id->fetchColumn();

    // Récupérer le titre de la propriété
    $sql1 = "SELECT title FROM properties WHERE id = :id";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindParam(':id', $propertie_id, PDO::PARAM_INT);
    $stmt1->execute();
    $propertie = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($propertie) {
        $message = "Votre ajout de bien intitulé '<strong style='font-weight: bold;'>" . htmlspecialchars($propertie['title']) . "</strong>' a été approuvé. Il est maintenant visible sur la plateforme.";
        $sqlnotif = "INSERT INTO notifs (message, user_id) VALUES (:message, :user_id)";
        $stmtnotif = $pdo->prepare($sqlnotif);
        $stmtnotif->bindParam(':message', $message, PDO::PARAM_STR);
        $stmtnotif->bindParam(':user_id', $notif_user_id, PDO::PARAM_INT);
        $stmtnotif->execute();
    }

    header("Location: properties_views.php");
    exit();
}
?>
