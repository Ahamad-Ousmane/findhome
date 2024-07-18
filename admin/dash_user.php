<?php
session_start();
require_once(__DIR__ . '/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];
$admin_id = $_SESSION['admin_id'];

// Récupérer les notifications
$sql = "SELECT n.id, n.message, n.created_at, n.is_read, u.username, p.is_confirmed, p.is_rejected
FROM notifications n 
JOIN user u ON n.user_id = u.id 
LEFT JOIN properties p ON n.propertie_id = p.id 
WHERE p.is_confirmed = false AND p.is_rejected = false
ORDER BY n.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);


//Les biens de l'admin connecté
$postAdminSql = "SELECT * FROM properties_admin WHERE admin_id = $admin_id";
$stmtpostAdmin = $pdo->prepare($postAdminSql);
$stmtpostAdmin->execute();
$propertiesAdmin = $stmtpostAdmin->fetchAll(PDO::FETCH_ASSOC);



// Récupérer les demandes non confirmées
$sql3 = "SELECT ar.id, ar.user_id, u.username, u.email, ar.id_card_front, ar.id_card_back, ar.telephone
        FROM agent_requests ar 
        JOIN user u ON ar.user_id = u.id 
        WHERE ar.confirmed = FALSE AND ar.refused = FALSE";
$stmt3 = $pdo->prepare($sql3);
$stmt3->execute();
$requests = $stmt3->fetchAll(PDO::FETCH_ASSOC);

$sql4 = "SELECT r.*, 
pa.id AS admin_propertie_id,
pa.title AS admin_propertie_title,
pa.type AS admin_propertie_type,
pa.price AS admin_propertie_price,
r.status AS rdv_status
FROM rdvs r
LEFT JOIN properties_admin pa ON r.propertie_admin_id = pa.id
WHERE r.admin_id =:id AND r.status = 'en attente'
ORDER BY r.created_at DESC";
$stmt4 = $pdo->prepare($sql4);
$stmt4->bindValue(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
$stmt4->execute();
$rdvs = $stmt4 -> fetchAll(PDO::FETCH_ASSOC);



// Récupérer les notifications non lues
$sql1 = "SELECT n.id, n.message, n.created_at, u.username 
        FROM notifications n 
        JOIN user u ON n.user_id = u.id 
        WHERE n.is_read = FALSE
        ORDER BY n.created_at DESC";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute();
$notification = $stmt1->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le total des utilisateurs
$usersql = "SELECT * FROM user ";
$userstmt = $pdo->prepare($usersql);
$userstmt->execute();
$user = $userstmt->fetchAll(PDO::FETCH_ASSOC);


//recuperer les clients
$usersql1 = "SELECT * FROM user WHERE profile=false";
$userstmt1 = $pdo->prepare($usersql1);
$userstmt1->execute();
$user1 = $userstmt1->fetchAll(PDO::FETCH_ASSOC);


// recuperer les agents
$usersql2 = "SELECT * FROM user WHERE profile = true";
$userstmt2 = $pdo->prepare($usersql2);
$userstmt2->execute();
$user2 = $userstmt2->fetchAll(PDO::FETCH_ASSOC);

$sqlConfirmAgent = "SELECT * FROM agent_requests WHERE confirmed = false AND refused = false";
$stmtConfirmAgent = $pdo->prepare($sqlConfirmAgent);
$stmtConfirmAgent->execute();
$notificationConfirmAgent = $stmtConfirmAgent->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les biens de la base de données
$sql = "(SELECT id, title, main_image, price, status, is_confirmed, is_rejected, 'properties_admin' AS source, created_at 
         FROM properties_admin 
         WHERE admin_id = :admin_id AND is_delete = false)
        UNION 
        (SELECT id, title, main_image, price, status, is_confirmed, is_rejected, 'properties' AS source, created_at 
         FROM properties 
         WHERE is_delete = false)
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':admin_id' => $admin_id]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);





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
    <title>FindHome | Dashboard Administrateur</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <?php require_once(__DIR__.'/link_icons.php') ?>
     <style>
      tr img {
          height: 40px;
          width: 40px;
          border-radius: 100%;
          object-fit: cover;
          object-position: center;
        }
     </style>
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
                <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                    <div>
                    <h3 class="fw-bold mb-3">Dashboard</h3>
                <h6 class="op-7 mb-2"></h6>
              </div>
              <div class="ms-md-auto py-2 py-md-0">
                <a href="./ajouter_agent.php" class="btn btn-primary btn-round">Ajouter un Agent <svg xmlns="http://www.w3.org/2000/svg" width="20" height="" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                </svg>
                </a>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-info bubble-shadow-small"
                        >
                          <i class="fas fa-user-check"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Clients</p>
                          <h4 class="card-title"><?= count($user1) ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-success bubble-shadow-small"
                        >
                          <i class="fas fa-luggage-cart"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Agents</p>
                          <h4 class="card-title"><?= count($user2)  ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              

              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-secondary bubble-shadow-small"
                        >
                          <i class="far fa-check-circle"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Demandes</p>
                          <h4 class="card-title"><?= count($notificationConfirmAgent) ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 col-md-3">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div
                          class="icon-big text-center icon-primary bubble-shadow-small"
                        >
                        <i class="fas fa-home"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Total de biens</p>
                          <h4 class="card-title"><?= count($properties)?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!--card-->
            
            <div class="row">
              <div class="col-sm-6 col-md-4 p-2">
                <div class="card card-stats card-primary card-round">
                  <a href="properties_views.php">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-5">
                        <div class="icon-big text-center">
                        <i class="fas fa-home"></i>
                        </div>
                      </div>
                      <div class="col-7 col-stats">
                        <div class="numbers">
                          <p class="card-category">Biens en attente de confirmation</p>
                          <h4 class="card-title"><?= count($notifications) ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                  </a>
                </div>
              </div>
              
              <div class="col-sm-6 col-md-4 p-2">
                <div class="card card-stats card-success card-round">
                  <a href="admin_confirm_agents.php">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-5">
                        <div class="icon-big text-center">
                        <i class="fas fa-user-check"></i>
                        </div>
                      </div>
                      <div class="col-7 col-stats">
                        <div class="numbers">
                          <p class="card-category">Validation de profil en attente</p>
                          <h4 class="card-title"><?= count($requests) ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                  </a>
                </div>
              </div>
              <div class="col-sm-6 col-md-4 p-2">
                <div class="card card-stats card-secondary card-round">
                  <a href="gerer_rdv.php">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-5">
                        <div class="icon-big text-center">
                        <i class="fas fa-handshake"></i>
                        </div>
                      </div>
                      <div class="col-7 col-stats">
                        <div class="numbers">
                          <p class="card-category">Demandes de rendez-vous</p>
                          <h4 class="card-title"><?= count($rdvs) ?></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                  </a>
                </div>
              </div>
            </div>



            <div class="row">
              <div class="col-md-6">
              <div class="card">
                  <div class="card-header">
                    <h1 class="card-title">Biens en attente d'approbation</h1>
                  </div>
                  <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover ms-3" id="basic-datatables">
                      <thead>
                        <tr>
                          <th>Image</th>
                          <th>Titre</th>
                          <th>Status</th>
                          <th>Etat</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($properties as $property) : ?>
                          <?php if ($property['is_confirmed'] == false && $property['is_rejected'] == false) : ?>
                          <tr>
                            <td><img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" alt="Image principale"></td>
                            <td><a href="property_details.php?<?= ($property['source'] === 'properties') ? 'id=' . $property['id'] : 'id_admin=' . $property['id'] ?>"><?= htmlspecialchars($property['title']) ?></a></td>
                            <td><?= ($property['status'] === 'for_sale') ? 'A vendre' : 'A louer' ?></td>
                              <td><em style="color: red">En attente d'approbation</em></td>
                          <?php endif; ?>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>  
                  </div> 
                  </div>     
                </div>
              </div>
              <div class="col-md-6">
       
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Profils en attente de confirmation</h4>
              </div>
              <div class="card-body">
                  <div class="table-responsive">
                      <table id="basic-datatables" class="display table table-striped table-hover">
                          <thead>
                              <tr>
                                  <th>Utilisateur</th>
                                  <th>Téléphone</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php if ($requests): ?>
                              <?php foreach ($requests as $request): ?>
                              <tr>
                                  <td><a href="admin_confirm_agents.php"><?= htmlspecialchars($request['username']) ?></a></td>
                                  <td><?= htmlspecialchars($request['telephone']) ?></td>
                                  <td>
                                      <div class="dropdown">
                                      <button class='btn btn-secondary dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                                  Actions
                                              </button>
                                          <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                              <form method="POST" style="display:inline;">
                                                  <input type="hidden" name="confirm_request_id" value="<?= $request['id'] ?>">
                                                  <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                                  <button class="dropdown-item" type="submit"  id="alert_demo_3_4">Confirmer</button>
                                              </form>
                                              <form method="POST" style="display:inline;">
                                                  <input type="hidden" name="refuse_request_id" value="<?= $request['id'] ?>">
                                                  <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                                  <button class="dropdown-item" type="submit">Rejeter</button>
                                              </form>
                                          </div>
                                      </div>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                              <?php else: ?>
                              <tr>
                                  <td colspan="6">Aucune demande</td>
                              </tr>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
      </div>


      <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">Dernières demandes de visite</h1>
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
        

        <?php require_once(__DIR__.'/footer.php') ?>



        
    
</body>
</html>

