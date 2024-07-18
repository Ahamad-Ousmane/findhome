<?php
session_start();
require_once(__DIR__ . '/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];
$admin_id = $_SESSION['admin_id'];

$sql = "SELECT r.*, 
pa.id AS admin_propertie_id,
pa.title AS admin_propertie_title,
pa.type AS admin_propertie_type,
pa.price AS admin_propertie_price,
r.status AS rdv_status
FROM rdvs r
LEFT JOIN properties_admin pa ON r.propertie_admin_id = pa.id
WHERE r.admin_id =:id
ORDER BY r.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
$stmt->execute();
$rdvs = $stmt -> fetchAll(PDO::FETCH_ASSOC);

$sql1="UPDATE rdvs SET is_read = true WHERE admin_id=:id";
$stmt1= $pdo->prepare($sql1);
$stmt1->execute([':id'=>$_SESSION['admin_id']]);


//Récupérer la table des administrateurs
$sqlAdmin = "SELECT * FROM admin WHERE id = $admin_id";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->execute();
$Admin = $stmtAdmin->fetch();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>SafeHome</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <?php require_once (__DIR__. '/link_icons.php') ?>   
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
        
        .notification .message {
            margin: 5px 0;
        }
        .notification .created_at {
            color: #6c757d;
            font-size: 0.8em;
        }
        .notification1{
            background-color: #fff;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid red;
            border-radius: 5px;
        }
    </style>
    <?php require_once(__DIR__.'/link_icons.php') ?>
</head>
<body>
<?php require_once(__DIR__ .'/sidebar.php'); ?>

<div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.php" class="logo">
                        <img src="assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                            <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                            <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                </div>
            </div>
            <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
        
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
              
               

                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?= $admin_name?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                          </div>
                          <div class="u-text">
                            <h4><?= $admin_name?></h4>
                            <p class="text-muted"><?= $Admin['email'] ?></p>
                            <!-- <a
                              href="profile.html"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            > -->
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item " id="profile" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">My Balance</a>
                        <a class="dropdown-item" href="#">Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php" onclick="confirmAction(event)">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>

        </div>






    <div class="container">
        <div class="page-inner">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">Les rendez-vous</h1>
                        </div>
                            <div class="card-body">
                                <?php if (count($rdvs) > 0): ?>
                                    <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Message</th>
                                            <th>Telephone</th>
                                            <th>Bien concerné</th>
                                            <th>Date</th>
                                            <th>Statut</th> <!-- Nouvelle colonne Statut -->
                                            <th>Action</th> <!-- Nouvelle colonne Action -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rdvs as $rdv): ?>
                                            <?php $protype = $rdv['admin_propertie_type'];?>
                                            <tr class="<?= $rdv['is_read'] == FALSE ? 'unread' : '' ?>" data-rdv-id="<?= $rdv['id'] ?>">
                                                <td><?= $rdv['nom'] ?></td>
                                                <td><?= $rdv['message'] ?></td>
                                                <td><?= $rdv['telephone'] ?></td>
                                                <td><?= $rdv['admin_propertie_title'] ?></td>
                                                <td><?= $rdv['created_at'] ?></td>
                                                <td class="status">
                                                    <?php
                                                    $statusClass = '';
                                                    if ($rdv['rdv_status'] === 'aboutit') {
                                                        $statusClass = 'badge bg-success';
                                                    } elseif ($rdv['rdv_status'] === 'non aboutit') {
                                                        $statusClass = 'badge bg-danger';
                                                    } else {
                                                        $statusClass = 'badge bg-primary';
                                                    }
                                                    ?>
                                                    <span class="<?= $statusClass ?>"><?= $rdv['rdv_status'] ?></span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <li><a class="dropdown-item action-button" href="#" data-status="aboutit">Aboutit</a></li>
                                                            <li><a class="dropdown-item action-button" href="#" data-status="non aboutit">Non aboutit</a></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>

                                    </table>
                                <?php else: ?>
                                    <p>Aucune notification</p>
                                <?php endif; ?>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once(__DIR__.'/footer.php') ?>
<script>
    $(document).ready(function () {
        $("#basic-datatables").DataTable({});
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll('.action-button').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();

            const status = this.getAttribute('data-status');
            const row = this.closest('tr');
            const statusCell = row.querySelector('.status');

            const rdvId = row.getAttribute('data-rdv-id'); // Assurez-vous d'ajouter un attribut data-rdv-id au tr

            // Mettre à jour le statut dans la base de données
            fetch('update_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: rdvId, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusCell.textContent = status;
                } else {
                    alert('Erreur lors de la mise à jour du statut.');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        });
    });
});
</script>

<?php require_once(__DIR__.'/footer.php') ?>
</body>
</html>
