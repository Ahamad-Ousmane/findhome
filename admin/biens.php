<?php
session_start();
require_once(__DIR__ . '/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

// Récupérer les notifications
$sql = "SELECT n.id, n.message, n.created_at, n.is_read, u.username, p.is_confirmed, p.is_rejected
        FROM notifications n 
        JOIN user u ON n.user_id = u.id 
        LEFT JOIN properties p ON n.propertie_id = p.id 
        ORDER BY n.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>SafeHome</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="assets/img/kaiadmin/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/plugins.min.css" />
    <link rel="stylesheet" href="../css/kaiadmin.min.css" />
    <link rel="stylesheet" href="../css/fonts.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .notification {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
            background-color: #fff;
        }
        .notification.unread {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .notification .username {
           font-weight: bold;
        }
        .notification .message {
            margin: 5px 0;
        }
        .notification .created_at {
            color: #6c757d;
            font-size: 0.8em;
        }
    </style>
    <?php require_once(__DIR__.'/link_icons.php') ?>
</head>
<body>
<?php require_once(__DIR__ .'/sidebar.php'); ?>

<div class="main-panel">
    <div class="content ms-5 mt-3">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Notifications</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="basic-datatables" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom d'utilisateur</th>
                                        <th>Message</th>
                                        <th>Date de création</th>
                                        <th>Statut</th>
                                        <th>État</th>
                                        <th>Détails</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Nom d'utilisateur</th>
                                        <th>Message</th>
                                        <th>Date de création</th>
                                        <th>Statut</th>
                                        <th>État</th>
                                        <th>Détails</th>
                                    </tr>
                                </tfoot>
                                <tbody>
                                    <?php if (count($notifications) > 0): ?>
                                        <?php foreach ($notifications as $notification): ?>
                                            <tr class="<?= $notification['is_read'] == false ? 'unread' : '' ?>">
                                                <td><?= htmlspecialchars($notification['username']) ?></td>
                                                <td><?= htmlspecialchars($notification['message']) ?></td>
                                                <td><?= htmlspecialchars($notification['created_at']) ?></td>
                                                <td><?= $notification['is_read'] == false ? 'Non lu' : 'Lu' ?></td>
                                                <td class="<?= $notification['is_confirmed'] ? '' : ($notification['is_rejected'] ? '' : 'text-danger') ?>">
                                                    <em><?php
                                                    if ($notification['is_confirmed']) {
                                                        echo 'Confirmé';
                                                    } elseif ($notification['is_rejected']) {
                                                        echo 'Rejeté';
                                                    } else {
                                                        echo 'En attente';
                                                    }
                                                    ?></em>
                                                </td>
                                                <td><a href="notification_read.php?id=<?= $notification['id'] ?>" class="btn btn-primary btn-sm">Details</a></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6">Aucune nouvelle notification</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__.'/footer.php') ?>
<script src="assets/js/core/jquery.3.2.1.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/plugins/jquery-ui.min.js"></script>
<script src="assets/js/plugins/bootstrap-notify.min.js"></script>
<script src="assets/js/ready.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });
</script>

</body>
</html>
