<?php 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once(__DIR__.'/admin/db.php');

// Marquer la notification comme lue 
$sql1 = "UPDATE notifs SET is_read = 1 WHERE user_id = :id";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute([':id' => $_SESSION['user_id']]);

$sql3 = "UPDATE rdvs SET is_read = 1 WHERE agent_id = :id";
$stmt3 = $pdo->prepare($sql3);
$stmt3->execute([':id' => $_SESSION['user_id']]);

$sql = "SELECT * FROM notifs WHERE user_id = :id AND is_delete=false ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $_SESSION['user_id']]);
$notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sql2 = "SELECT r.*,
p.id AS propertie_id,
p.title AS propertie_title,
p.type AS propertie_type,
p.price AS propertie_price 
FROM rdvs r
LEFT JOIN properties p ON r.propertie_id = p.id
WHERE r.agent_id =:id AND r.is_delete = false
ORDER BY r.created_at DESC";
$stmt2 = $pdo->prepare($sql2);
$stmt2->execute([':id' => $_SESSION['user_id']]);
$rdvs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD']==='POST'){
    $id = $_POST['id'];
    $sql="UPDATE notifs SET is_delete=true WHERE id=:id";
    $stmt = $pdo->prepare($sql);
    $stmt -> execute([':id'=>$id]);
    header("Location: notifications.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        .container {
            margin-top: 20px;
             height: 100%;
        }
        h1 {
            font-weight: bold;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 10px;
        }
        .card-text {
            font-size: 1rem;
            margin-bottom: 10px;
        }
        .text-muted {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .btn-link {
            font-size: 0.9rem;
            text-decoration: none;
            color: #dc3545;
        }
        .btn-link:hover {
            color: #bd2130;
        }
        .delete-btn {
            border: none;
            background: none;
            cursor: pointer;
        }
        
        .delete-btn:hover{
            background-color: #dc3545;
            padding: 10px;
            color: #f8f9fa;
        }

        .delete-btn:hover{
          font-family: system-ui;
          font-weight: 500;
          font-size: 1.1rem;
          padding: 10px;
          border-radius: 10px;
          box-shadow: 0 0 6px rgba(0, 0, 0, 0.5);
          background-color: #dc3545;
          color:white;
        }
        .scrollable-column {
            height: 75vh; /* Adjust height as necessary */
            overflow-y: auto;
            padding-right: 15px; /* Compensate for scrollbar width */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
        <h1>Notifications</h1>
            <div class="col-md-8  scrollable-column">
                
                <?php if (!empty($notifs)): ?>
                    <?php foreach ($notifs as $notif): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Ajout de biens</h5>
                                <?php if(!$notif['reason']): ?>
                                    <div class="card-text"><?php echo $notif['message']; ?></div>
                                <?php else: ?>
                                    <div class="card-text"><?= $notif['message'] ?></div>
                                    <span class="text-muted"><strong>Raison :</strong> <?= $notif['reason'] ?></span>
                                <?php endif; ?>
                                <em><p class="text-muted"><?php echo $notif['created_at']; ?></p></em>
                                <form action="" method="post" style="display: inline;">
                                    <input type="hidden" value="<?= htmlspecialchars($notif['id']) ?>" name="id">
                                    <button class="delete-btn btn-link" type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body">
                            Aucune notification.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <h1>Vos rendez-vous</h1>
                <div class="div scrollable-column">
                    <?php if (!empty($rdvs)): ?>
                        <?php foreach ($rdvs as $rdv): ?>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Rendez-vous</h5>
                                    <div class="card-text"><strong>Nom : </strong><?php echo $rdv['nom']; ?></div>
                                    <div class="card-text"><strong>Email : </strong><?= $rdv['email'] ?></div>
                                    <div class="card-text"><strong>Téléphone : </strong><?= $rdv['telephone'] ?></div>
                                    <div class="card-text"><strong>Message : </strong><?= $rdv['message'] ?></div>
                                    <div class="card-text"><strong>Bien concerné : </strong><?= $rdv['propertie_title'] ?></div>
                                    <div class="card-text"><strong>Type : </strong><?= $rdv['propertie_type'] ?></div>
                                    <div class="card-text"><strong>Prix : </strong><?= $rdv['propertie_price'] ?></div>
                                    <em><p class="text-muted"><?php echo $rdv['created_at']; ?></p></em>
                                    <form action="suppress.php" method="post" style="display: inline;">
                                        <input type="hidden" value="<?= htmlspecialchars($rdv['id']) ?>" name="id">
                                        <button class="delete-btn" type="submit">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                Aucun rendez-vous.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-srLAAqpJxHs5fjg7WBAfZr6IU9N4MRJJY4RCeGBt7HEecruO+Bl9CrRsywb35d2Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-LOHZ21pyiu5uL73F6jd0GrUiTQXwAH4rfm9MQf9R9eR6CQ2r3XS74wuJnAYK5H0/" crossorigin="anonymous"></script>
</body>
</html>
