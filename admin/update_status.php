<?php
require_once(__DIR__ . '/db.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérez les données envoyées
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérifiez si les données sont valides
    if (isset($data['id']) && isset($data['status'])) {
        $rdvId = $data['id'];
        $status = $data['status'];

        // Préparez la requête SQL
        $sql = "UPDATE rdvs SET status = :status WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':id', $rdvId, PDO::PARAM_INT);

        // Exécutez la requête et retournez une réponse JSON
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
}
?>
