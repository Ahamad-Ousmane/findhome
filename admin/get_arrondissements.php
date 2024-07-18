<?php
include 'db.php'; // Fichier de configuration pour la connexion à la base de données

$pdo->exec("SET GLOBAL max_allowed_packet=67108864"); 


if (isset($_GET['commune_id'])) {
    $commune_id = $_GET['commune_id'];

    $stmt = $pdo->prepare("SELECT id_arrond, lib_arrond FROM arrondissement WHERE id_com = :commune_id");
    $stmt->execute(['commune_id' => $commune_id]);
    $arrondissements = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($arrondissements);
}
?>
