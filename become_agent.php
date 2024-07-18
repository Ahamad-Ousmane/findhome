<?php
session_start();
require_once(__DIR__ . '/admin/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $id_card_front = $_FILES['id_card_front'];
    $id_card_back = $_FILES['id_card_back'];
    $phone = $_POST['telephone'];

    // Assurez-vous que le dossier uploads existe et est accessible en écriture
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Générer des noms de fichiers uniques
    //chaque fichier est renommé en utilisant uniqid avec un préfixe ('front_' ou 'back_') suivi d'un identifiant unique basé sur le temps actuel en microsecondes, ce qui garantit un nom de fichier unique.
    $front_image_path = $upload_dir . uniqid('front_', true) . '.' . pathinfo($id_card_front['name'], PATHINFO_EXTENSION);
    $back_image_path = $upload_dir . uniqid('back_', true) . '.' . pathinfo($id_card_back['name'], PATHINFO_EXTENSION);

    if (move_uploaded_file($id_card_front['tmp_name'], $front_image_path) && move_uploaded_file($id_card_back['tmp_name'], $back_image_path)) {
        // Insérer la demande dans la table agent_requests
        $sql = "INSERT INTO agent_requests (user_id, nom, prenom, id_card_front, id_card_back, telephone) VALUES (:user_id, :nom, :prenom, :id_card_front, :id_card_back, :telephone)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $stmt->bindParam(':id_card_front', $front_image_path);
        $stmt->bindParam(':id_card_back', $back_image_path);
        $stmt->bindParam(':telephone', $phone, PDO::PARAM_STR);
        $stmt->execute();

        header("location: index.php");
    } else {
        echo "Erreur lors du téléchargement des fichiers.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindHome | Devenir Agent</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .property-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 15px;
        }
        .property-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 5px;
        }
        .rendez {
            border-radius: 25px;
        }
    </style>
</head>
<body>
    <div class="container mt-5 mx-auto">
        <div class="card">
            <div class="card-header">
                <h1>Demande pour devenir agent</h1>
            </div>
            <div class="property-card">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" class="form-control" id="nom" name="nom" required><br>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required><br>
                    </div>
                    <div class="form-group">
                        <label for="id_card_front">Carte d'identité (recto) :</label>
                        <input type="file" class="form-control" id="id_card_front" name="id_card_front" accept="image/*" required><br>
                    </div>
                    <div class="form-group">
                        <label for="id_card_back">Carte d'identité (verso) :</label>
                        <input type="file" class="form-control" id="id_card_back" name="id_card_back" accept="image/*" required><br>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Numero de telephone</label>
                        <input type="tel" class="form-control" id="phone" name="telephone" required>
                    </div>
                    <div class="d-flex">
                        <button type="submit" class="btn btn-primary mx-auto mt-3">Soumettre la demande</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
