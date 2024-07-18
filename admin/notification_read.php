<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

require_once(__DIR__ . '/db.php');

// Vérifier si un ID de notification est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dash_user.php");
    exit();
}


$admin_name = $_SESSION['admin_name'];
$admin_id = $_SESSION['admin_id'];

$propertie_id = $_GET['id'];


// Marquer la notification comme lue
$sqlUpdate = "UPDATE notifications SET is_read = TRUE WHERE id = :id";
$stmtUpdate = $pdo->prepare($sqlUpdate);
$stmtUpdate->bindParam(':id', $propertie_id);
$stmtUpdate->execute();




// Récupérer les détails du projet spécifique
$sqlForPropertie = "SELECT * FROM properties WHERE id = :propertie_id";
$stmtForPropertie = $pdo->prepare($sqlForPropertie);
$stmtForPropertie->bindParam(':propertie_id', $propertie_id);
$stmtForPropertie->execute();
$propertie = $stmtForPropertie->fetch(PDO::FETCH_ASSOC);

// Récupérer is_confirmed ou is_rejected
$sql1 = "SELECT p.is_confirmed, p.is_rejected
         FROM properties p
         WHERE p.id = :propertie_id";
$stmt1 = $pdo->prepare($sql1);
$stmt1->bindParam(':propertie_id', $propertie_id);
$stmt1->execute();
$notificationn1 = $stmt1->fetch(PDO::FETCH_ASSOC);

$sql2 = "SELECT n.id, n.user_id 
         FROM notifications n
         JOIN properties p ON n.propertie_id = p.id
         WHERE p.id = :propertie_id";
$stmt2 = $pdo->prepare($sql2);
$stmt2->bindParam(':propertie_id', $propertie_id, PDO::PARAM_INT);
$stmt2->execute();
$notificationn = $stmt2->fetch(PDO::FETCH_ASSOC);

//Récupérer la table des administrateurs
$sqlAdmin = "SELECT * FROM admin WHERE id = $admin_id";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->execute();
$Admin = $stmtAdmin->fetch();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php require_once(__DIR__.'/link_icons.php') ?>   
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
            object-position: center;
            border-radius: 5px;
        }
        .rendez {
            border-radius: 19px;
        }
        .evaluer {
            margin: 0 10px 20px 30px; /* 0 en haut, 10px à droite, 10px en bas, 10px à gauche */
            font-size: 1.2rem;
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
              <div class="row">
              <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <h1 class="card-title"><?= htmlspecialchars($propertie['title']) ?></h1>
                </div>
              <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <!-- Diaporama pour les images -->
                        <div id="propertyCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <!-- Afficher l'image principale -->
                                    <img src="data:image/jpeg;base64,<?= base64_encode($propertie['main_image']) ?>" class="d-block w-100 property-image" alt="Image principale de la propriété">
                                </div>
                                <!-- Afficher les autres images -->
                                <?php if ($propertie['image1'] != null) : ?>
                                    <div class="carousel-item">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($propertie['image1']) ?>" class="d-block w-100 property-image" alt="Deuxième image de la propriété">
                                    </div>
                                <?php endif; ?>
                                <?php if ($propertie['image2'] != null) : ?>
                                    <div class="carousel-item">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($propertie['image2']) ?>" class="d-block w-100 property-image" alt="Troisième image de la propriété">
                                    </div>
                                <?php endif; ?>
                                <?php if ($propertie['image3'] != null) : ?>
                                    <div class="carousel-item">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($propertie['image3']) ?>" class="d-block w-100 property-image" alt="Quatrième image de la propriété">
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- Contrôles de diaporama -->
                            <a class="carousel-control-prev" href="#propertyCarousel" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Précédent</span>
                            </a>
                            <a class="carousel-control-next" href="#propertyCarousel" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Suivant</span>
                            </a>
                        </div>
                        <div class="container card">
                            <h3 class="my-4 card-title">Description</h3>
                            <p class="card-body"><?= htmlspecialchars($propertie['description']) ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Informations sur la propriété -->
                        <h3 class="my-3 card-title text-uppercase">Informations sur la propriété</h3>
                        <p><strong>Type :</strong> <?php if($propertie['type'] === 'house'){
                                    echo 'Maison';
                                    }elseif($propertie['type'] === 'apartment'){
                                    echo 'Appartement';
                                    }elseif($propertie['type'] === 'condo'){
                                    echo 'Logement';
                                    }elseif($propertie['type'] === 'townhouse'){
                                    echo 'Maison de ville';
                                    }else{
                                    echo 'Terrain';
                                    }
                                    ?></p>
                        <p><strong>Prix :</strong> <?= htmlspecialchars($propertie['price']) ?> fcfa</p>
                        <p><strong>Chambres :</strong> <?= htmlspecialchars($propertie['bedrooms']) ?></p>
                        <p><strong>Salles de bains :</strong> <?= htmlspecialchars($propertie['bathrooms']) ?></p>
                        <p><strong>Superficie :</strong> <?= htmlspecialchars($propertie['area']) ?> m²</p>
                        <p><strong>Quartier :</strong> 
                        <?= htmlspecialchars($propertie['quartier']) ?> 
                        </p>
                        <p><strong>État :</strong> <?= htmlspecialchars($propertie['state']) ?></p>
                        <p class="btn btn-secondary"><strong>Statut :</strong> <?= htmlspecialchars($propertie['status'] === 'for_sale' ? 'A vendre' : 'A louer') ?></p>
                        <div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex">
                    <?php if ($notificationn1['is_confirmed'] == false && $notificationn1['is_rejected'] == false): ?>
                        <a href="accepter_post.php?id=<?=htmlspecialchars($notificationn['id']) ?>&user_id=<?= htmlspecialchars($notificationn['user_id']); ?>" class="btn btn-primary p-3 m-3 rendez">Confirmer</a>
                        <!-- Bouton pour ouvrir le modal -->
                        <button type="button" data-bs-toggle="modal" data-bs-target="#refuserModal"  class="btn btn-danger p-3 m-3 rendez">Refuser</button>

                    <?php else: ?>
                        <div class="evaluer">Ce projet a déjà été évalué</div>
                    <?php endif; ?>
              </div>
              </div>
              </div>
              </div>
              </div>
            </div>
        </div>


        <!-- Modal pour Refuser -->
<div class="modal fade" id="refuserModal" tabindex="-1" aria-labelledby="refuserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="refuserModalLabel">Rejeter la demande</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Formulaire pour refuser la demande -->
        <form action="post_refuser.php" method="POST">
          <input type="hidden" name="id" value="<?= htmlspecialchars($notificationn['id']) ?>">
          <input type="hidden" name="user_id" value="<?= htmlspecialchars($notificationn['user_id']) ?>">
          <div class="mb-3">
            <label for="reason" class="form-label">Raison du refus</label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-danger">Envoyer</button>
        </form>
      </div>
    </div>
  </div>
</div>

    



<?php require_once(__DIR__ . '/footer.php'); ?>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
