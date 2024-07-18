<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

require_once(__DIR__ . '/db.php');

// Vérifier si les paramètres nécessaires sont passés en GET
if (!isset($_GET['id']) || !is_numeric($_GET['id']) || !isset($_GET['user_id']) || !is_numeric($_GET['user_id']) || !isset($_GET['reason'])) {
    header("Location: dash_user.php");
    exit();
}

$notif_id = $_GET['id'];
$notif_user_id = $_GET['user_id'];
$reason = $_GET['reason'];

// Mettre à jour la propriété pour marquer le refus
$sql = "UPDATE properties p
        JOIN notifications n ON n.propertie_id = p.id
        SET p.is_rejected = TRUE
        WHERE n.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $notif_id, PDO::PARAM_INT);
$result = $stmt->execute();

if ($result) {
    // Sélectionner la notification spécifique pour la propriété refusée
    $sql1 = "SELECT n.message, p.title
             FROM notifications n
             JOIN properties p ON n.propertie_id = p.id
             WHERE n.id = :id";
    $stmt1 = $pdo->prepare($sql1);
    $stmt1->bindParam(':id', $notif_id, PDO::PARAM_INT);
    $stmt1->execute();
    $notification = $stmt1->fetch(PDO::FETCH_ASSOC);

    if ($notification) {
        $message = "Votre ajout de bien intitulé '<strong style='font-weight: bold;'>" . htmlspecialchars($notification['title']) . "</strong>' a été refusé.";
        $sqlnotif = "INSERT INTO notifs (message, user_id, reason) VALUES (:message, :user_id, :reason)";
        $stmtnotif = $pdo->prepare($sqlnotif);
        $stmtnotif->bindParam(':message', $message, PDO::PARAM_STR);
        $stmtnotif->bindParam(':user_id', $notif_user_id, PDO::PARAM_INT);
        $stmtnotif->bindParam(':reason', $reason, PDO::PARAM_STR);
        $stmtnotif->execute();
    }

    header("Location: properties_views.php");
    exit();
}
?>
