<?php
session_start();

require_once(__DIR__ . '/admin/db.php');


if (isset($_GET['id'])) {
    $property_id = $_GET['id'];

    $sql_property = "SELECT * FROM properties WHERE id = :id";
    $stmt_property = $pdo->prepare($sql_property);
    $stmt_property->bindParam(':id', $property_id);
    $stmt_property->execute();
    $property = $stmt_property->fetch(PDO::FETCH_ASSOC);


    $sql = "SELECT * FROM properties WHERE user_id=:id AND id=:propertie_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->bindParam(':propertie_id', $property_id);
    $stmt->execute();
    $property1 = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql_rdv = "SELECT * FROM rdvs WHERE user_id = :id AND id= :property_id LIMIT 1";
    $stmt_rdv = $pdo->prepare($sql_rdv);
    $stmt_rdv->bindParam(':id', $_SESSION['user_id']);
    $stmt_rdv->bindParam(':property_id', $property_id);
    $stmt_rdv->execute();
    $rdv1 = $stmt_rdv->fetch(PDO::FETCH_ASSOC);
    
   
    
} elseif (isset($_GET['id_admin'])) {
    $property_admin_id = $_GET['id_admin'];

    $sql_property_admin = "SELECT * FROM properties_admin WHERE id = :id";
    $stmt_property_admin = $pdo->prepare($sql_property_admin);
    $stmt_property_admin->bindParam(':id', $property_admin_id);
    $stmt_property_admin->execute();
    $property = $stmt_property_admin->fetch(PDO::FETCH_ASSOC);

   

  

    $sql_rdv_admin = "SELECT * FROM rdvs WHERE user_id = :id AND propertie_admin_id = :property_admin_id LIMIT 1";
    $stmt_rdv_admin = $pdo->prepare($sql_rdv_admin);
    $stmt_rdv_admin->bindParam(':id', $_SESSION['user_id']);
    $stmt_rdv_admin->bindParam(':property_admin_id', $property_admin_id);
    $stmt_rdv_admin->execute();
    $rdv1 = $stmt_rdv_admin->fetch(PDO::FETCH_ASSOC);

    ;
}

$protype = $property['type'];
$user_id = $_SESSION['user_id'];

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
    <title>FindHome | Descriptions d'une propriété </title>
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
       .property-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 5px;
        }
        h3{
            font-family:'Times New Roman', Times, serif;
        }
        .rdv {
          font-family: system-ui;
          font-weight: 500;
          font-size: 1.1rem;
          padding: 10px;
          border-radius: 10px;
          box-shadow: 0 0 6px rgba(0, 0, 0, 0.8); /* Légère ombre pour le badge */
        }
        .post_approuver{
          font-family: system-ui;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: 10px;
            margin-bottom: 10px;
            background-color: #007BFF;
            color: white;
            border-radius: 10px;
            padding-left: 10px;
        }
        .proposed{
          font-family: system-ui, sans-serif !important;
            font-size: 1.2rem;
            margin-bottom: 10px;
            background-color: #D3D3D3;
            margin-top:5px;
            margin-left: 10px;
            color: white;
            border-radius: 10px;
            padding-left: 10px;
            border: none!important;
        }

        .custom-hero-height-2 {
            height: 50vh; /* Ajustez la hauteur selon vos besoins */
            display: flex;
            align-items: center; /* Centre le contenu verticalement */
            justify-content: center; /* Centre le contenu horizontalement (si nécessaire) */
            position: relative; /* Assure que l'overlay et les autres éléments sont bien positionnés */
        }

        .custom-hero-height-2 .overlay {
            position: absolute; /* Assure que l'overlay couvre toute la section */
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        .custom-hero-height-2 .container {
            position: relative; /* Assure que le contenu est bien positionné par rapport à l'overlay */
            z-index: 1; /* S'assure que le contenu est au-dessus de l'overlay */
            margin-top: 20%;
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
						    <span class="text">+229 55288119</span>
					    </div>
					    <div class="col-md pr-4 d-flex topper align-items-center">
					    	<div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-paper-plane"></span></div>
						    <span class="text">findHome@email.com</span>
					    </div>
					    <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right justify-content-end">
						<?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) : ?>
						    <p class="mb-0 register-link"><i class="bi bi-person-circle"></i>&nbsp;<a href="signup.php" class="mr-3">S'inscrire</a><a href="login.php">se connecter</p>
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
	      <a class="navbar-brand" href="index.php">FindHome</a>
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
	  </nav>
	  
	  <section class="custom-hero-height-2" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('images/back.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-4">
            <h1 class="mb-3 bread text-light">Détails</h1>
          </div>
        </div>
      </div>
    </section>

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
                        <div class="container card ">
                            <h3 class="my-2 card-title">Description</h3>
                            <p class="card-body text-dark"><?= $property['description'] ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <!-- Informations sur la propriété -->
                        <h3 class="my-3 card-title text-uppercase text-dark">Informations sur la propriété</h3>
                        <p class="text-dark"><strong class="text-dark">Type :</strong> <?php if($protype === 'house'){
                          echo 'Maison';
                        }elseif($protype === 'apartment'){
                          echo 'Appartement';
                        }elseif($protype === 'condo'){
                          echo 'Logement';
                        }elseif($protype === 'townhouse'){
                          echo 'Maison de ville';
                        }else{
                          echo 'Terrain';
                        }
                        ?></p>
                        <p class="text-dark"><strong class="text-dark">Prix :</strong> <?= $property['price'] ?> fcfa</p>
                        <p class="text-dark"><strong class="text-dark">Chambres :</strong> <?= $property['bedrooms'] ?></p>
                        <p class="text-dark"><strong class="text-dark">Salles de bains :</strong> <?= $property['bathrooms'] ?></p>
                        <p class="text-dark"><strong class="text-dark">Superficie :</strong> <?= $property['area'] ?> m²</p>
                        <p class="text-dark"><strong class="text-dark">Quartier :</strong> <?= $property['quartier'] ?></p>
                        <p class="text-dark"><strong class="text-dark">État :</strong> <?= $property['state'] ?></p>
                        <p class="text-dark"><strong class="text-dark">Pays :</strong> <?= $property['country'] ?></p>
                        <p class="btn <?= $property['status'] === 'for_sale'? 'btn-primary' : 'btn-secondary' ?> "><strong>Statut :</strong> <?= $property['status'] === 'for_sale'? 'A vendre' : 'A louer' ?></p>
                    </div>
                </div>
                  <?php if(isset($_GET['id']) && empty($property1)):?>
                 <a href="rdv.php?id=<?= $_GET['id'] ?>" class="btn btn-primary my-3 rdv">Prendre un rendez-vous</a>
                 <?php if($rdv1): ?>
                 <button class="proposed "> Votre avez deja proposer un rendez-vous pour ce bien</button>
                 <?php endif; ?>
                 <?php elseif(isset($_GET['id']) &&  !empty($property1)): ?>
                  <button class="post_approuver btn btn-primary"> Votre post est bien approuver</button>
                  <?php elseif( isset($_GET['id_admin'])): ?>
                 <a href="rdv.php?id_admin=<?= $_GET['id_admin'] ?>" class="btn btn-primary my-3 rdv">Prendre un rendez-vous</a>
                 <?php if($rdv1): ?>
                 <button class="proposed "> Votre avez deja proposer un rendez-vous pour ce bien</button>
                 <?php endif; ?>
                  <?php endif; ?>
            </div>
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