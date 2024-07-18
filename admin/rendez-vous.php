<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}
require_once(__DIR__. '/db.php');

$user_id = null;
$error = null;

// Vérification de l'existence et de la validité de l'ID de notification
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dash_user.php"); 
    exit();
}

$notification_id = $_GET['id'];

// Sélection du nom d'utilisateur et user_id à partir de la notification
$sql = "SELECT u.id as user_id, u.username
        FROM notifications n
        JOIN user u ON n.user_id = u.id
        WHERE n.id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $notification_id);
$stmt->execute();
$notification = $stmt->fetch(PDO::FETCH_ASSOC);

$user_id = $notification['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données soumises
    $jour = $_POST['jour'];
    $heure = $_POST['heure'];
    $message = $_POST['message'];

    // Validation des données (vous pouvez ajouter plus de validation selon vos besoins)
    if (empty($jour) || empty($heure) || empty($message)) {
        // Gérer les erreurs, par exemple :
        $error = "Tous les champs doivent être remplis.";
    } else {
        // Insérer les données dans la base de données ou effectuer d'autres opérations nécessaires
        // Exemple d'insertion simplifié, assurez-vous d'adapter cela à votre structure de base de données
        $insert_sql = "INSERT INTO rdv (user_id, jour, heure, message) 
                       VALUES (:user_id, :jour, :heure, :message)";
        $stmt = $pdo->prepare($insert_sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':jour', $jour);
        $stmt->bindParam(':heure', $heure);
        $stmt->bindParam(':message', $message);
        
        if ($stmt->execute()) {
            // Rediriger après succès
            header("Location: dash_user.php");
            exit();
        } else {
            // Gérer les erreurs d'insertion
            $error = "Erreur lors de l'enregistrement du rendez-vous.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container p-4">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card shadow-sm p-4">
                    <h2 class="text-center mb-4">Formulaire de rendez-vous pour le projet de <?= strtoupper($notification['username']) ?></h2>
                    <form action="" method="POST">
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
                        <div class="mb-3">
                            <label for="jour" class="form-label">Jour pour le rendez-vous</label>
                            <select name="jour" id="jour" class="form-select" required>
                                <option value="" selected disabled>Choisir un jour pour le rendez-vous</option>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                                <option value="Samedi">Samedi</option>
                                <option value="Dimanche">Dimanche</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="heure" class="form-label">Heure</label>
                            <input type="time" name="heure" id="heure" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control" placeholder="Entrer un petit message..." rows="3" required></textarea>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary me-3">Envoyer</button>
                            <a href="dash_user.php" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12tqnip8s3jXJOnxFs9bmgxXB7vL60sP81YeGPudb1kEhxS8" crossorigin="anonymous"></script>
</body>
</html>
