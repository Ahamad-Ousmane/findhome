<?php
session_start();
// Inclure le fichier de configuration de la base de données
require_once(__DIR__ . '/admin/db.php');

// Récupérer les données de recherche
$quartier = isset($_GET['quartier']) ? $_GET['quartier'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$bedrooms = isset($_GET['bedrooms']) ? $_GET['bedrooms'] : '';
$bathrooms = isset($_GET['bathrooms']) ? $_GET['bathrooms'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$min_area = isset($_GET['min_area']) ? $_GET['min_area'] : '';
$max_area = isset($_GET['max_area']) ? $_GET['max_area'] : '';
$sql = "(SELECT id, title, main_image, price, status , 'properties' AS source FROM properties  WHERE is_confirmed = true" ;
if (!empty($quartier)) {
    $sql .= " AND quartier LIKE :quartier";
}
if (!empty($type)) {
    $sql .= " AND type = :type";
}
if (!empty($status)) {
    $sql .= " AND status = :status";
}
if (!empty($bedrooms)) {
    $sql .= " AND bedrooms >= :bedrooms";
}
if (!empty($bathrooms)) {
    $sql .= " AND bathrooms >= :bathrooms";
}
if (!empty($min_price)) {
    $sql .= " AND price >= :min_price";
}
if (!empty($max_price)) {
    $sql .= " AND price <= :max_price";
}
if (!empty($min_area)) {
    $sql .= " AND area >= :min_area";
}
if (!empty($max_area)) {
    $sql .= " AND area <= :max_area";
}
$sql .= ") 
UNION 
(SELECT id, title, main_image, price, status, 'properties_admin' AS source FROM properties_admin ";
if (!empty($quartier) || !empty($type) || !empty($status) || !empty($bedrooms) || !empty($bathrooms) || !empty($min_price) || !empty($max_price) || !empty($min_area) || !empty($max_area)) {
    $sql .= " WHERE 1=1"; // Pour éviter des erreurs de syntaxe si aucun filtre n'est appliqué
}
if (!empty($quartier)) {
    $sql .= " AND quartier LIKE :quartier_admin";
}
if (!empty($type)) {
    $sql .= " AND type = :type_admin";
}
if (!empty($status)) {
    $sql .= " AND status = :status_admin";
}
if (!empty($bedrooms)) {
    $sql .= " AND bedrooms >= :bedrooms_admin";
}
if (!empty($bathrooms)) {
    $sql .= " AND bathrooms >= :bathrooms_admin";
}
if (!empty($min_price)) {
    $sql .= " AND price >= :min_price_admin";
}
if (!empty($max_price)) {
    $sql .= " AND price <= :max_price_admin";
}
if (!empty($min_area)) {
    $sql .= " AND area >= :min_area_admin";
}
if (!empty($max_area)) {
    $sql .= " AND area <= :max_area_admin";
}
$sql .= ")";

$stmt = $pdo->prepare($sql);

// Associer les valeurs des paramètres
if (!empty($quartier)) {
    $stmt->bindValue(':quartier', "%$quartier%");
    $stmt->bindValue(':quartier_admin', "%$quartier%");
}
if (!empty($type)) {
    $stmt->bindValue(':type', $type);
    $stmt->bindValue(':type_admin', $type);
}
if (!empty($status)) {
    $stmt->bindValue(':status', $status);
    $stmt->bindValue(':status_admin', $status);
}
if (!empty($bedrooms)) {
    $stmt->bindValue(':bedrooms', $bedrooms);
    $stmt->bindValue(':bedrooms_admin', $bedrooms);
}
if (!empty($bathrooms)) {
    $stmt->bindValue(':bathrooms', $bathrooms);
    $stmt->bindValue(':bathrooms_admin', $bathrooms);
}
if (!empty($min_price)) {
    $stmt->bindValue(':min_price', $min_price);
    $stmt->bindValue(':min_price_admin', $min_price);
}
if (!empty($max_price)) {
    $stmt->bindValue(':max_price', $max_price);
    $stmt->bindValue(':max_price_admin', $max_price);
}
if (!empty($min_area)) {
    $stmt->bindValue(':min_area', $min_area);
    $stmt->bindValue(':min_area_admin', $min_area);
}
if (!empty($max_area)) {
    $stmt->bindValue(':max_area', $max_area);
    $stmt->bindValue(':max_area_admin', $max_area);
}

$stmt->execute();
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user_id = isset($_SESSION['user_id'])? $_SESSION['user_id'] : null;

//recuperer les utilisateurs 
$sql1 = "SELECT * FROM user WHERE id = :user_id";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute(['user_id'=>$user_id]);
$stmt1->execute();
$user = $stmt1-> fetch(PDO::FETCH_ASSOC);

?>




<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>SafeHome | Résultat de recherche</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">
    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
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
            position: relative;
        }

        .status-circle {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            font-weight: bold;
        }
        .status-for-sale {
            background-color: #007bff; /* Bleu pour "À vendre" */
            color: #fff;
        }
        .status-for-rent {
            background-color: #F28749; /* Jaune pour "À louer" */
            color: #fff;
        }
        .project {
			font-size: 1.2rem;
			height: 45px;
			padding: 8px;
			border-radius: 7px;
			
		}
    </style>
  </head>
  <body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">
  	<div class="py-1 bg-black top">
    	<div class="container">
    		<div class="row no-gutters d-flex align-items-start align-items-center px-md-0">
	    		<div class="col-lg-12 d-block">
		    		<div class="row d-flex">
		    			<div class="col-md pr-4 d-flex topper align-items-center">
					    	<div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-phone2"></span></div>
						    <span class="text">+ 1235 2355 98</span>
					    </div>
					    <div class="col-md pr-4 d-flex topper align-items-center">
					    	<div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-paper-plane"></span></div>
						    <span class="text">youremail@email.com</span>
					    </div>
					    <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right justify-content-end">
						<?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) : ?>
						    <p class="mb-0 register-link"><i class="bi bi-person-circle"></i>&nbsp;<a href="signup.php" class="mr-3">Sign Up</a><a href="login.php">Login</p>
						<?php else:?>
							<p class="mb-0 register-link"><a href="logout.php" onclick="confirmAction(event)">Se déconnecter</a></p>
						<?php endif;?>
					    </div>
				    </div>
			    </div>
		    </div>
		</div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light site-navbar-target" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.php">Stayhome</a>
	      <button class="navbar-toggler js-fh5co-nav-toggle fh5co-nav-toggle" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
		  <ul class="navbar-nav nav ml-auto">
	          <li class="nav-item"><a href="index.php#home-section" class="nav-link"><span>Accueil</span></a></li>
	          <li class="nav-item"><a href="index.php#services-section" class="nav-link"><span>Services</span></a></li>
	          <li class="nav-item"><a href="index.php#properties-section" class="nav-link"><span>Actualités</span></a></li>
	          <li class="nav-item"><a href="properties.php" class="nav-link"><span>Nos Offres</span></a></li>
              
	

    <?php if($user): ?>
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($user['profile'] == true): ?>
	          <li class="nav-item"><a href="user_dash.php" class="nav-link"><span>Mes projets</span></a></li>
            <li class="nav-item"><a href="ajout_bien.php" class="btn btn-secondary project btn-lg mr-3"><span>Ajoutez un Bien</span></a></li>
        <?php else: ?>
            <li class="nav-item"><a href="become_agent.php" class="btn btn-secondary project mt-1 agent"><span>Devenir agent</span></a></li>
        <?php endif; ?>
    <?php else: ?>
        <li class="nav-item"><a href="login.php" class="btn btn-link project mt-1 agent"><span>Devenir agent</span></a></li>
    <?php endif; ?>
<?php else: ?>
    <li class="nav-item"><a href="login.php" class="btn btn-secondary project mt-1 agent"><span>Devenir agent</span></a></li>
<?php endif; ?>
			</ul>
	      </div>
          <?php if(isset($_SESSION['user_id'])): ?>
		<?php require_once(__DIR__. '/notif.php') ?>
		<?php endif; ?>
	    </div>
	  </nav>
	  
	  <section class="hero-wrap hero-wrap-2" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('images/image_4.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-4">
            <h1 class="mb-3 bread text-light">Resultats de la recherche</h1>
             <p class="breadcrumbs"><span class="mr-2"><a href="index.php">Accueil <i class="ion-ios-arrow-forward"></i></a></span> <span>Propriétés <i class="ion-ios-arrow-forward"></i></span></p>
          </div>
        </div>
      </div>
    </section>

		<section class="ftco-section ftco-properties" id="properties-section">
    	<div class="container-fluid px-md-5">
      <a id="results"></a>
    		<div class="row">
				<!-- end -->
					<div class="col">
					<div class="row">
                    <?php if (count($properties) > 0): ?>
                    <?php foreach ($properties as $property) : ?>
                        <div class="col-md-4">
                            <div class="property-card">
                                <!-- Afficher l'image principale -->
                                <img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" alt="Image principale de la propriété" class="property-image">
                                <!-- Afficher le statut -->
                                <div class="status-circle <?= $property['status'] === 'for_sale' ? 'status-for-sale' : 'status-for-rent'; ?>">
                                    <?= $property['status'] === 'for_sale' ? 'Sale' : 'Rent'; ?>
                                </div>
                                <!-- Afficher le titre -->
                                <h3 class="card-title"><?= $property['title'] ?></h3>
                                <!-- Afficher le prix -->
                                <p class="text-muted">Prix : <?= $property['price'],' fcfa'?><?= $property['status'] === 'for_rent ' ? 'par mois' : ' '; ?></p>
                                <!-- Lien vers les détails du bien -->
                                <?php if ($property['source'] === 'properties') : ?>
                                    <a href="single.php?id=<?= $property['id'] ?>" class="btn btn-primary">Voir les détails</a>
                                <?php else : ?>
                                    <a href="single.php?id_admin=<?= $property['id'] ?>" class="btn btn-primary">Voir les détails</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                        <p>Aucune propriété trouvée</p>
                    <?php endif; ?>
                </div>

                <footer class="ftco-footer ftco-section">
                    <?php require_once(__DIR__. '/footer.php'); ?>
                </footer>
    


  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="js/main.js"></script>
    
  </body>
</html>