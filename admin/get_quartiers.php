<?php
include 'db.php'; // Fichier de configuration pour la connexion à la base de données

$pdo->exec("SET GLOBAL max_allowed_packet=67108864"); 


if (isset($_GET['arrondissement_id'])) {
    $arrondissement_id = $_GET['arrondissement_id'];
    $stmt = $pdo->prepare("SELECT id_quart, lib_quart FROM quartier WHERE id_arrond = :arrondissement_id");
    $stmt->execute(['arrondissement_id' => $arrondissement_id]);
    $quartiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formater la réponse pour récupérer les noms des quartiers
    $result = array_map(function($quartier) {
        return [
            'id_quart' => $quartier['id_quart'],
            'lib_quart' => $quartier['lib_quart']
        ];
    }, $quartiers);

    echo json_encode($result);
}

?>
