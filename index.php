<?php
	session_start();
	require_once(__DIR__. '/admin/db.php');

	$sql = "SELECT id, title, city, main_image, price, type, quartier, status FROM properties LIMIT 5";
	$stmt = $pdo->prepare($sql);
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
        /* Add your custom styles here */
.ftco-properties {
    padding: 60px 0;
}

.heading-section {
    margin-bottom: 50px;
}

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

.property-card .img {
    position: relative;
}

.property-card .property-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-bottom: 2px solid #f8f9fa;
}

.property-card .status-circle {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff6f61;
    color: #fff;
    padding: 10px 10px;
    border-radius: 50px;
    font-size: 10px;
	  text-align: center;
    line-height: 13px;
}

.property-card .status-for-sale {
    background: #007bff;
}

.property-card .status-for-rent {
    background: #ff6f61;
}

.property-card .desc {
    padding: 20px;
}

.property-card h3 {
    margin: 0 0 10px;
    font-size: 18px;
    font-weight: 600;
}

.property-card .price {
    font-size: 16px;
    color: #343a40;
}

.property-card .h-info {
    font-size: 14px;
    color: #6c757d;
}

.property-card .location,
.property-card .details {
    display: block;
    margin-top: 5px;
}

.carousel-properties .owl-item {
    padding: 15px;
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
		.agent{
			font-size: 1.2rem;
		}
		.project {
			font-size: 1.2rem;
			height: 45px;
			padding: 8px;
			border-radius: 7px;
			
		}
    
   
        
.hero-wrap {
    position: relative;
    width: 100%;
    height: 75vh;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden; 
}


.container {
    width: 100%;
    padding: 0 15px;
    overflow-x: hidden; 
}

.row {
    margin: 0;
    padding: 0;
    width: 100%;
    overflow-x: hidden; 
}

.search-property {
    position: absolute; 
    top: 150px; 
    right: 75px;
    max-height: 100%;
    overflow-y: auto;
    background: rgba(0, 0, 0, 0.7); 
    padding: 20px;
    border-radius: 8px;
    color: #fff;
    width: 100%; 
    max-width: 350px;
    margin: 0 auto; 
    overflow-x: hidden; 
}


/* Form fields styling */
.search-property .form-group {
    margin-bottom: 15px;
}

.search-property .form-control {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    color: #333;
    padding: 10px 15px;
    border-radius: 5px;
    width: 100%; 
    max-width: 300px; 
    margin: 0 auto; 
}

.search-property .form-control::placeholder {
    color: #777;
}

.search-property .btn-primary {
    background: #007bff;
    border: none;
    color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    width: 100%; 
    max-width: 300px; 
    margin: 0 auto; 
    display: block;
}

.search-property .select-wrap {
    position: relative;
}

.search-property .select-wrap::after {
    content: '\f107';
    font-family: 'FontAwesome';
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    pointer-events: none;
    color: #333;
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
	      <button class="navbar-toggler js-fh5co-nav-toggle fh5co-nav-toggle " type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav nav ml-auto">
	          <li class="nav-item"><a href="#home-section" class="nav-link"><span>Accueil</span></a></li>
	          <li class="nav-item"><a href="#services-section" class="nav-link"><span>Services</span></a></li>
	          <li class="nav-item"><a href="#properties-section" class="nav-link"><span>Actualités</span></a></li>
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
	  
	  <section class="hero-wrap custom-hero-height" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('images/woman-showing-with-one-hand-mini-house-real-state-concept-ai-generative.jpg');" data-section="home" data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text js-fullheight align-items-center justify-content-start" data-scrollax-parent="true">
            <div class="col-md-5 ftco-animate" data-scrollax="properties: { translateY: '70%' }">
                <h1 id="typing-title" class="text-light mb-5" data-scrollax="properties: { translateY: '30%', opacity: 1.6 }"></h1>
            </div>
            <form action="advanced_search.php#properties-section" method="GET" class="search-property col-md-4" style="max-height: 100%; overflow-y: auto;">
                <div class="row">
                    <div class="col-md-12 align-items-end ftco-animate">
                        <div class="form-group">
                            
                            <div class="form-field">
                                <input type="text" name="quartier" class="form-control mb-3" placeholder="Localité" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 align-items-end ftco-animate">
                        <div class="form-group">
                            <div class="form-field">
                                <div class="select">
                                    <select name="type" id="type" class="form-control mb-3">
                                        <option value="">Type de propriété</option>
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
                            <div class="form-field">
                                <div class="select">
                                    <select name="status" id="status" class="form-control mb-3">
                                        <option value="">Statut</option>
                                        <option value="for_sale">À vendre</option>
                                        <option value="for_rent">À louer</option>
                                    </select>
                                </div>
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
</section>




    <section class="ftco-section ftco-properties" id="properties-section">
        <div class="container">
            <!-- <div class="row justify-content-center pb-5">
                <div class="col-md-12 heading-section text-center ftco-animate">
                    <h2 class="mb-4">Nos offres</h2>
                </div>
            </div> -->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="carousel-properties owl-carousel">
                        <?php foreach ($properties as $property): ?>
                            <div class="item">
                                <div class="property-card">
                                    <div class="img">
                                        <img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" class="property-image" alt="Property Image">
                                    </div>
                                    <div class="desc">
                                        <div class="status-circle <?= $property['status'] === 'for_sale' ? 'status-for-sale' : 'status-for-rent'; ?>">
                                            <span><?= $property['status'] === 'for_sale' ? 'A vendre' : 'A louer'; ?></span>
                                        </div>
                                        <div class="d-flex pt-3">
                                            <div>
                                                <h3><a href="properties.php" class="btn btn-primary">
                                                    <?php
                                                    if($property['type'] === 'house'){
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
                                                    ?>
                                                </a></h3>
                                            </div>
                                            <div class="pl-md-4">
                                                <p class="price text-muted"><?= $property['price']; ?>&nbsp;fcfa</p>
                                            </div>
                                        </div>
                                        <p class="h-info text-dark"><span class="location"><?= $property['quartier']; ?></span> <span class="details">&mdash; <?= $property['city']; ?></span></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </div>
            <div class="container d-flex mt-5">
                <a href="properties.php" class="mx-auto"><button class="btn btn-primary project">Voir plus</button></a>
            </div>
        </div>
    </section>

		<section class="ftco-section ftco-services-2 bg-light" id="services-section">
			<div class="container">
				<div class="row justify-content-center pb-5">
          <div class="col-md-12 heading-section text-center ftco-animate">
            <h2 class="mb-4">Nos services</h2>
            <p>À <strong>FindHome</strong>, nous sommes fiers de vous offrir une plateforme complète et conviviale dédiée à la mise en relation entre vendeurs, acheteurs, locataires et propriétaires de biens immobiliers. Notre mission est de faciliter toutes vos transactions immobilières grâce à des services professionnels et personnalisés.</p>
          </div>
        </div>
        <div class="row">
        	<div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services text-center d-block">
              <div class="icon justify-content-center align-items-center d-flex"><span class="flaticon-pin"></span></div>
              <div class="media-body">
                <h3 class="heading mb-3">Recherches de biens</h3>
				<p> Notre moteur de recherche avancé vous permet de trouver rapidement le bien qui correspond à vos critères. Que vous cherchiez une maison, un appartement, ou un local commercial, nous avons ce qu'il vous faut.</p>
              </div>
            </div>      
          </div>
          <div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services text-center d-block mt-lg-5 pt-lg-4">
              <div class="icon justify-content-center align-items-center d-flex"><span class="flaticon-house"></span></div>
              <div class="media-body">
                <h3 class="heading mb-3">Prise de Rendez-vous</h3>
				<p>Prenez rendez-vous en ligne pour visiter un bien ou pour rencontrer un agent immobilier. Notre système de planification intégré vous permet de choisir une date et une heure qui vous conviennent, simplifiant ainsi le processus de prise de contact et de visite. Recevez des confirmations et des rappels automatiques pour ne jamais manquer un rendez-vous important.</p>
              </div>
            </div>      
          </div>
          <div class="col-md d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services text-center d-block">
              <div class="icon justify-content-center align-items-center d-flex"><span class="flaticon-detective"></span></div>
              <div class="media-body">
                <h3 class="heading mb-3">Profil Agent</h3>
				<p>Inscrivez-vous en tant qu'agent immobilier et bénéficiez de fonctionnalités exclusives. Ajoutez et gérez facilement vos annonces, suivez les performances de vos biens, et interagissez avec les clients potentiels directement depuis votre profil. Mettez en avant vos compétences et votre expertise pour attirer plus de prospects et conclure des ventes rapidement.</p>
              </div>
            </div>      
          </div>
		</section>

    <section class="ftco-section ftco-services-2 bg-light" id="workflow-section">
			<div class="container">
				<div class="row">
          <div class="col-md-4 heading-section ftco-animate">
            <h2 class="mb-4">comment ça marche?</h2>
            <p>Cette plateforme intutive et conviviale a ete concue dans le but de faciliter la recherche du bien immobilier contribuant a simplifier les transactions immobilieres</p>
            <div class="media block-6 services text-center d-block pt-md-5 mt-md-5">
              <div class="icon justify-content-center align-items-center d-flex"><span>1</span></div>
              <div class="media-body p-md-3">
                <h3 class="heading mb-3">Trouver votre bien</h3>
                <p class="mb-5">Grace au moteur de recherche avance dont nous disposons trouver votre bien ideale selon vos exigences </p>
                <hr>
              </div>
            </div>
          </div>
          <div class="col-md-4 d-flex align-self-stretch ftco-animate mt-lg-5">
            <div class="media block-6 services text-center d-block mt-lg-5 pt-md-5 pt-lg-4">
              <div class="icon justify-content-center align-items-center d-flex"><span>2</span></div>
              <div class="media-body p-md-3">
                <h3 class="heading mb-3">Rencontrer nos agents</h3>
                <p class="mb-5">Apres avoir trouver le bien ideal vous pouvez maintenant contacter un de nos agents via un formulaire pour prendre un rendez vous et conclure en presentiel </p>
                <hr>
              </div>
            </div>      
          </div>
          <div class="col-md-4 d-flex align-self-stretch ftco-animate">
            <div class="media block-6 services text-center d-block">
              <div class="icon justify-content-center align-items-center d-flex"><span>3</span></div>
              <div class="media-body p-md-3">
                <h3 class="heading mb-3">Conclure le marché</h3>
                <p class="mb-5">Apres la visite si vous etes interesse vous pouvez signer le contrat et conclure ainsi le marche. Felicitations vous venez de trouver votre maison ideale </p>
                <hr>
              </div>
            </div>      
          </div>
        </div>
			</div>
		</section>

		<section class="ftco-intro img" id="hotel-section" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('images/bg.jpg');">
			<div class="overlay"></div>
			<div class="container">
				<div class="row justify-content-end">
					<div class="col-md-7">
						<h2 class="mb-4">Trouver votre maison à un prix <span>imbattable</span></h2>
						<p>Faites vos recherches selon des criteres specifiques et trouver la maison qui vous plait</p>
						<p class="mb-0"><a href="properties.php" class="btn btn-white px-4 py-3">Recherche avanceée</a></p>
					</div>
				</div>
			</div>
		</section>

    <section class="ftco-section contact-section ftco-no-pb" id="contact-section">
      <div class="container">
      	<div class="row justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
            <span class="subheading">Contact</span>
            <h2 class="mb-4">Contactez Nous</h2>
            <p>Remplissez le formulaire ci dessous pour nous contacter</p>
          </div>
        </div>

        <div class="row block-9">
          <div class="col-md-7 order-md-last ftco-animate">
            <form action="#" class="bg-light p-4 p-md-5 contact-form">
              <div class="form-group mb-3">
                <input type="text" class="form-control" placeholder="Votre Nom">
              </div>
              <div class="form-group mb-3">
                <input type="text" class="form-control" placeholder="Votre Email">
              </div>
              <div class="form-group mb-3">
                <input type="text" class="form-control" placeholder="Sujet">
              </div>
              <div class="form-group mb-3">
                <textarea name="" id="" cols="30" rows="7" class="form-control" placeholder="Message"></textarea>
              </div>
              <div class="form-group">
                <input type="submit" value="Envoyer un Message" class="btn btn-primary py-3 px-5">
              </div>
            </form>
          
          </div>

          <div class="col-md-5 d-flex">
          	<div class="row d-flex contact-info mb-5">
		          <div class="col-md-12 ftco-animate">
		          	<div class="box p-2 px-3 bg-light d-flex">
		          		<div class="icon mr-3">
		          			<span class="icon-map-signs"></span>
		          		</div>
		          		<div>
			          		<h3 class="mb-3">Adresse</h3>
				            <p>Benin , Cotonou Gbegamey dans la rue de la banque la Poste</p>
			            </div>
			          </div>
		          </div>
		          <div class="col-md-12 ftco-animate">
		          	<div class="box p-2 px-3 bg-light d-flex">
		          		<div class="icon mr-3">
		          			<span class="icon-phone2"></span>
		          		</div>
		          		<div>
			          		<h3 class="mb-3">Numéro de téléphone</h3>
				            <p><a href="tel://1234567920">+229 59 58 52 83</a></p>
			            </div>
			          </div>
		          </div>
		          <div class="col-md-12 ftco-animate">
		          	<div class="box p-2 px-3 bg-light d-flex">
		          		<div class="icon mr-3">
		          			<span class="icon-paper-plane"></span>
		          		</div>
		          		<div>
			          		<h3 class="mb-3">Adresse Email</h3>
				            <p><a href="mailto:info@yoursite.com">ousmaneadam@annonce.com</a></p>
			            </div>
			          </div>
		          </div>
		          <div class="col-md-12 ftco-animate">
		          	<div class="box p-2 px-3 bg-light d-flex">
		          		<div class="icon mr-3">
		          			<span class="icon-globe"></span>
		          		</div>
		          		<div>
			          		<h3 class="mb-3">Site Web</h3>
				            <p><a href="#">Findhome.com</a></p>
			            </div>
			          </div>
		          </div>
		        </div>
          </div>
        </div>
      </div>
    </section>
		
		

    <footer class="ftco-footer ftco-section">
      <?php require_once(__DIR__. '/footer.php'); ?>
    </footer>
    

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
  
  <script src="js/main.js"></script>
    
  </body>
</html>