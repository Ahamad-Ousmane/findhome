<?php
include 'db.php'; // Fichier de configuration pour la connexion à la base de données

$pdo->exec("SET GLOBAL max_allowed_packet=67108864"); 

if (isset($_GET['departement_id'])) {
    $departement_id = $_GET['departement_id'];
    

    $stmt = $pdo->prepare("SELECT id_com, lib_com FROM commune WHERE id_dep = :departement_id");
    $stmt->execute(['departement_id' => $departement_id]);
    $communes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($communes);
} else {
    // Ajoutez cette ligne pour voir si l'ID n'est pas reçu
    error_log("departement_id not set");
}
?>
