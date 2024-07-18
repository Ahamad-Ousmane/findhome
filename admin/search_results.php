<?php
session_start();
// Inclure le fichier de configuration de la base de données
include 'db.php';

// Récupérer les critères de recherche de l'URL
$query = $_GET['query'] ?? '';

// Requête SQL pour rechercher les biens correspondants
$sql = "SELECT id, title, main_image, price 
        FROM properties 
        WHERE title LIKE :query1 
        OR city LIKE :query2 
        OR type LIKE :query3 
        OR price LIKE :query4 
        OR state LIKE :query5";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':query1', '%' . $query . '%', PDO::PARAM_STR);
$stmt->bindValue(':query2', '%' . $query . '%', PDO::PARAM_STR);
$stmt->bindValue(':query3', '%' . $query . '%', PDO::PARAM_STR);
$stmt->bindValue(':query4', '%' . $query . '%', PDO::PARAM_STR);
$stmt->bindValue(':query5', '%' . $query . '%', PDO::PARAM_STR);
$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de la recherche</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Styles personnalisés -->
    <style>
        .property-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
            padding: 15px;
        }
        .property-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
        }
        
    </style>
        <!-- CSS Files -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/plugins.min.css" />
        <link rel="stylesheet" href="../css/kaiadmin.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
       <?php require_once(__DIR__ . '/link_icons.php'); ?>

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
          <!-- <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              

              
            </div>
          </nav> -->
          <!-- End Navbar -->
        </div>
        <div class="container">

                <div class="container">
                <h1 class="my-4">Résultats de la recherche</h1>
                <div class="row">
                    <?php foreach ($properties as $property) : ?>
                        <div class="col-md-4">
                            <div class="property-card">
                                <!-- Afficher l'image principale -->
                                <img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" alt="Image principale de la propriété" class="property-image">
                                <!-- Afficher le titre -->
                                <h3><?= $property['title'] ?></h3>
                                <!-- Afficher le prix -->
                                <p class="text-muted">Prix : <?= $property['price'] ?> €</p>
                                <!-- Lien vers les détails du bien -->
                                <a href="property_details.php?id=<?= $property['id'] ?>" class="btn btn-primary">Voir les détails</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                </div>

        </div>

        
      </div>
    </div>
   


    
    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
