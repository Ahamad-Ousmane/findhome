
<?php

session_start();
// Inclure le fichier de configuration de la base de données
require_once (__DIR__. '/admin/db.php');

// Déterminer le numéro de page actuel
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Nombre de biens par page
$biensParPage = 6;

// Calculer l'offset pour la première table (properties)
$offset_properties = ($page - 1) * $biensParPage;

// Calculer l'offset pour la deuxième table (properties_admin)
$offset_properties_admin = ($page - 1) * $biensParPage;

// Récupérer les biens de la table properties et properties_admin pour la page actuelle, ordonnés par date d'ajout
$sql_properties = "
    (SELECT id, title, main_image, price, status, 'properties' AS source
     FROM properties 
     WHERE is_confirmed=true AND is_delete = false)
    UNION
    (SELECT id, title, main_image, price, status, 'properties_admin' AS source
     FROM properties_admin WHERE is_delete = false)
    ORDER BY id DESC
    LIMIT :offset, :biensParPage
";
$stmt_properties = $pdo->prepare($sql_properties);
$stmt_properties->bindParam(':offset', $offset_properties, PDO::PARAM_INT);
$stmt_properties->bindParam(':biensParPage', $biensParPage, PDO::PARAM_INT);
$stmt_properties->execute();
$properties = $stmt_properties->fetchAll(PDO::FETCH_ASSOC);

// Calculer le nombre total de biens pour la pagination
$total = $pdo->query("SELECT COUNT(*) AS total FROM (
    SELECT id FROM properties WHERE is_confirmed=true AND is_delete = false
    UNION ALL
    SELECT id FROM properties_admin WHERE is_delete = false
) AS combined")->fetch(PDO::FETCH_ASSOC)['total'];

$totalPages = ceil($total / $biensParPage);


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
    <title>FindHome</title>
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
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease-in-out;
        }
        .property-card:hover {
            transform: translateY(-10px);
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
            font-size: 10px;
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
						    <span class="text">+ 229 59 58 52 83</span>
					    </div>
					    <div class="col-md pr-4 d-flex topper align-items-center">
					    	<div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-paper-plane"></span></div>
						    <span class="text">youremail@email.com</span>
					    </div>
					    <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right justify-content-end">
						<?php if (!isset($_SESSION['user_id']) && !isset($_SESSION['username'])) : ?>
						    <p class="mb-0 register-link"><i class="bi bi-person-circle"></i>&nbsp;<a href="signup.php" class="mr-3">S'inscrire</a><a href="login.php">Se connecter</p>
						<?php else:?>
							<p class="mb-0 register-link"><a href="logout.php">Se déconnecter</a></p>
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
	    </div>
	  </nav>
	  
	  <section class=" custom-hero-height-2" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('images/back.jpg');" data-stellar-background-ratio="0.5">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text align-items-end justify-content-start">
          <div class="col-md-9 ftco-animate pb-4">
            <h1 class="mb-3 bread text-light">Propriétés</h1>
            
          </div>
        </div>
      </div>
    </section>

		<section class="ftco-section ftco-properties" id="properties-section">
    	<div class="container-fluid px-md-5">
    		<div class="row">
			<div class="col-lg-3 pr-lg-4">
						<div class="search-wrap">
							<h3 class="mb-5">Recherche avancée</h3>
							<form action="advanced_search.php#properties-section" method="GET" class="search-property">
                                <div class="row">
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="city">Localite</label>
                                            <div class="form-field">
                                                <input type="text" name="quartier" class="form-control mb-3" placeholder="Nom de la localite">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="type">Type de propriété</label>
                                            <div class="form-field">
                                                <div class="select-wrap">
                                                    <select name="type" id="type" class="form-control mb-3">
                                                        <option value="">sélectionnez un type</option>
                                                        <option value="house">Maison</option>
                                                        <option value="apartment">Appartement</option>
                                                        <option value="condo">Condo</option>
                                                        <option value="townhouse">Maison de ville</option>
                                                        <option value="land">Terrain</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="status">Statut</label>
                                            <div class="form-field">
                                                <div class="select-wrap">
                                                    <select name="status" id="status" class="form-control mb-3">
                                                        <option value="">selectionnez le statut</option>
                                                        <option value="for_sale">À vendre</option>
                                                        <option value="for_rent">À louer</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="bedrooms">Min Chambres</label>
                                            <div class="form-field">
                                                <div class="select-wrap">
                                                    <select name="bedrooms" id="bedrooms" class="form-control mb-3">
                                                        <option value="">choisir le nombre</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="bathrooms">Min Salles de bain</label>
                                            <div class="form-field">
                                                <div class="select-wrap">
                                                    <select name="bathrooms" id="bathrooms" class="form-control mb-3">
                                                        <option value="">choisir le nombre</option>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="min_price">Min Prix</label>
                                            <div class="form-field">
                                                <div class="select-wrap">
                                                    <select name="min_price" id="min_price" class="form-control mb-3">
                                                        <option value="">choisir</option>
                                                        <option value="5000">5,000 fcfa</option>
                                                        <option value="10000">10,000 fcfa</option>
                                                        <option value="50000">50,000 fcfa</option>
                                                        <option value="100000">100,000 fcfa</option>
                                                        <option value="200000">200,000 fcfa</option>
                                                        <option value="300000">300,000 fcfa</option>
                                                        <option value="400000">400,000 fcfa</option>
                                                        <option value="500000">500,000 fcfa</option>
                                                        <option value="600000">600,000 fcfa</option>
                                                        <option value="700000">700,000 fcfa</option>
                                                        <option value="800000">800,000 fcfa</option>
                                                        <option value="900000">900,000 fcfa</option>
                                                        <option value="1000000">1,000,000 fcfa</option>
                                                        <option value="2000000">2,000,000 fcfa</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="max_price">Max Prix</label>
                                            <div class="form-field">
                                                <div class="select-wrap">
                                                    <select name="max_price" id="max_price" class="form-control mb-3">
                                                        <option value="">choisir</option>
                                                        <option value="5000">5,000 fcfa</option>
                                                        <option value="10000">10,000 fcfa</option>
                                                        <option value="50000">50,000 fcfa</option>
                                                        <option value="100000">100,000 fcfa</option>
                                                        <option value="200000">200,000 fcfa</option>
                                                        <option value="300000">300,000 fcfa</option>
                                                        <option value="400000">400,000 fcfa</option>
                                                        <option value="500000">500,000 fcfa</option>
                                                        <option value="600000">600,000 fcfa</option>
                                                        <option value="700000">700,000 fcfa</option>
                                                        <option value="800000">800,000 fcfa</option>
                                                        <option value="900000">900,000 fcfa</option>
                                                        <option value="1000000">1,000,000 fcfa</option>
                                                        <option value="2000000">2,000,000 fcfa</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="min_area">Min Area</label>
                                            <div class="form-field">
                                                <input type="text" name="min_area" class="form-control mb-3" placeholder="Min Area">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-items-end ftco-animate">
                                        <div class="form-group">
                                            <label for="max_area">Max Area</label>
                                            <div class="form-field">
                                                <input type="text" name="max_area" class="form-control mb-3" placeholder="Max Area">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 align-self-end ftco-animate">
                                        <div class="form-group">
                                            <div class="form-field">
                                                <input type="submit" value="Rechercher" class="form-control btn btn-primary">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
			</div>
				<!-- end -->
<div class="col">
    <div class="row">
        <?php foreach ($properties as $property) : ?>
            <div class="col-md-4">
                <div class="property-card">
                    <!-- Afficher l'image principale -->
                    <img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" alt="Image principale de la propriété" class="property-image">
                    <!-- Afficher le statut -->
                    <div class="status-circle <?= $property['status'] === 'for_sale' ? 'status-for-sale' : 'status-for-rent'; ?>">
                        <?= $property['status'] === 'for_sale' ? 'A Vendre' : 'A louer'; ?>
                    </div>
                    <!-- Afficher le titre -->
                    <h3 class="card-title text-dark"><?= $property['title'] ?></h3>
                    <!-- Afficher le prix -->
                    <p class="text-muted">Prix : <?= $property['price'], ' fcfa' ?><?= $property['status'] === 'for_rent ' ? ' par mois' : ''; ?></p>
                    <!-- Lien vers les détails du bien -->
                    <?php if ($property['source'] === 'properties') : ?>
                        <a href="single.php?id=<?= $property['id'] ?>" class="btn btn-primary">Voir les détails</a>
                    <?php else : ?>
                        <a href="single.php?id_admin=<?= $property['id'] ?>" class="btn btn-primary">Voir les détails</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <!-- Pagination -->
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1) : ?>
                <li class="page-item">
                    <a class="page-link rounded-circle border mr-1" href="?page=<?= $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= $page === $i ? 'active' : ''; ?>"><a class="page-link rounded-circle border mr-1" href="?page=<?= $i; ?>"><?= $i; ?></a></li>
            <?php endfor; ?>
            <?php if ($page < $totalPages) : ?>
                <li class="page-item">
                    <a class="page-link rounded-circle border" href="?page=<?= $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
    </section>


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