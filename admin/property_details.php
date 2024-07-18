<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
  header("Location: loginadmin.php");
  exit();
}
// Inclure le fichier de configuration de la base de données
include 'db.php';

// Vérifier si un ID de propriété est passé en paramètre
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    // Récupérer les détails de la propriété en fonction de l'ID
    $sql = "(SELECT * FROM properties WHERE id = :id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $property_id);
    $stmt->execute();
    $property = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la propriété existe
    if (!$property) {
        // Redirectionner si la propriété n'est pas trouvée
        header("Location: properties_views.php");
        exit();
    }
} elseif(isset($_GET['id_admin'])) {
  $property_id = $_GET['id_admin'];

  $sql = "(SELECT * FROM properties_admin  WHERE id = :id)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $property_id);
    $stmt->execute();
    $property = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$property) {
      // Redirectionner si la propriété n'est pas trouvée
      header("Location: properties_views.php");
      exit();
  }
} else {

  header('Location: properties_views.php');
  exit;
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>FindHome | Administrateur Dashboard</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
     <?php require_once(__DIR__.'/link_icons.php') ?>
    <style>
        .property-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 5px;
        }
        h3{
            font-family:'Times New Roman', Times, serif;
        }
    </style>
</head>
  <body>
  <?php require_once(__DIR__ .'/sidebar.php'); ?>


      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="index.php" class="logo">
                <img
                  src="assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
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
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
                
              </nav>

              
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
        <div class="container">
                <div class="container">
                <h1 class="my-4"><?= $property['title'] ?></h1>
                <div class="row">
                    <div class="col-md-8">
                        <!-- Diaporama pour les images -->
                        <div id="propertyCarousel" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <!-- Afficher l'image principale -->
                                    <img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" class="d-block w-100 property-image" alt="Image principale de la propriété">
                                </div>
                                <!-- Afficher les autres images -->
                                <?php if ($property['image1'] != null) : ?>
                                    <div class="carousel-item">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($property['image1']) ?>" class="d-block w-100 property-image" alt="Deuxième image de la propriété">
                                    </div>
                                <?php endif; ?>
                                <?php if ($property['image2'] != null) : ?>
                                    <div class="carousel-item">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($property['image2']) ?>" class="d-block w-100 property-image" alt="Troisième image de la propriété">
                                    </div>
                                <?php endif; ?>
                                <?php if ($property['image3'] != null) : ?>
                                    <div class="carousel-item">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($property['image3']) ?>" class="d-block w-100 property-image" alt="Quatrième image de la propriété">
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
                            <p class="card-body"><?= $property['description'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Informations sur la propriété -->
                        <h3 class="my-3 card-title text-uppercase">Informations sur la propriété</h3>
                        <p><strong>Type :</strong> <?php if($property['type'] === 'house'){
                          echo 'Maison';
                        }elseif($property['type'] === 'apartment'){
                          echo 'Appartement';
                        }elseif($property['type'] === 'condo'){
                          echo 'Logement';
                        }elseif($property['type'] === 'townhouse'){
                          echo 'Maison de ville';
                        }else{
                          echo 'Terrain';
                        }
                        ?></p>
                        <p><strong>Prix :</strong> <?= $property['price'] ?> fcfa</p>
                        <p><strong>Chambres :</strong> <?= $property['bedrooms'] ?></p>
                        <p><strong>Salles de bains :</strong> <?= $property['bathrooms'] ?></p>
                        <p><strong>Superficie :</strong> <?= $property['area'] ?> m²</p>
                        <p><strong>Quartier :</strong> <?= $property['quartier'] ?></p>
                        <p><strong>État :</strong> <?= $property['state'] ?></p>
                        <p class="btn btn-secondary"><strong>Statut :</strong> <?= $property['status'] === 'for_sale' ? 'A vendre' : 'A louer'; ?></p>
                    </div>
                </div>
                
                 
                
            </div>
        </div>

        
      </div>
    </div>

   <?php require_once (__DIR__.'/footer.php') ?>
  </body>
</html>
