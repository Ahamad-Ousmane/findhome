<?php
session_start();
require_once(__DIR__ . '/admin/db.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


// Fetch user projects and pagination
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$biensParPage = 3;
$offset = ($page - 1) * $biensParPage;

$stmtTotal = $pdo->prepare("SELECT COUNT(*) AS total FROM properties WHERE user_id = :user_id AND is_delete = false");
$stmtTotal->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmtTotal->execute();
$totalBiens = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalBiens / $biensParPage);

$sql = "SELECT * FROM properties WHERE user_id = :user_id  AND is_delete = false LIMIT :offset, :biensParPage";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':biensParPage', $biensParPage, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$userPropertie = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>SafeHome</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
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

        .project {
			font-size: 1.2rem;
			height: 45px;
			padding: 8px;
			border-radius: 7px;
			
		}
        .approbation{
            font-family: system-ui, sans-serif !important;
            font-weight: bold;
            font-size: 1.2rem;
            margin-top: -7px;
            margin-bottom: 10px;
            background-color: #D3D3D3;
            color: white;
            border-radius: 10px;
            padding-left: 10px;
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
                            <span class="text">+ 1235 2355 98</span>
                        </div>
                        <div class="col-md pr-4 d-flex topper align-items-center">
                            <div class="icon mr-2 d-flex justify-content-center align-items-center"><span class="icon-paper-plane"></span></div>
                            <span class="text">youremail@email.com</span>
                        </div>
                        <div class="col-md-5 pr-4 d-flex topper align-items-center text-lg-right justify-content-end">
                            <?php if (!isset($_SESSION['user_id'])) : ?>
                                <p class="mb-0 register-link"><i class="bi bi-person-circle"></i>&nbsp;<a href="signup.php" class="mr-3">S'inscrire</a><a href="login.php">se connecter</p>
                            <?php else: ?>
                                <p class="mb-0 register-link"><a href="logout.php">Se déconnecter</a></p>
                            <?php endif; ?>
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
    <section class="custom-hero-height-2" style="background-image: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),url('images/back.jpg');" data-stellar-background-ratio="0.5">
    <div class="overlay"></div>
    <div class="container">
        <div class="row no-gutters slider-text align-items-center justify-content-start">
            <div class="col-md-9 pb-4">
                <h1 class="mb-3 bread text-light">Gérez vos projets</h1>
            </div>
        </div>
    </div>
</section>

    <section>
        <div class="container mt-5">
            <div class="row">
                <?php foreach ($userPropertie as $propertie): ?>
                    <div class="col-md-4">
                        <div class="property-card">
                            <?php if (!empty($propertie['main_image'])): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($propertie['main_image']) ?>" alt="Image représentative du projet " class="property-image">
                            <?php endif; ?>
                            <h3 class="card-title"><?= $propertie['title']; ?></h3>
                            <p class="text-muted">Prix : <?= $propertie['price'] ?>fcfa</p>
                            <?php if($propertie['is_confirmed']== false && $propertie['is_rejected']== false):?>
                            <div class="approbation">En attente d'approbation</div>
                            <?php endif; ?>
                            <div class="d-flex">
                                <a href="modifier_projet_user.php?id=<?php echo $propertie['id']; ?>"><button class="btn btn-primary">Modifier</button></a>
                                <a class="ms-auto" href="supprimer_bien.php?id=<?php echo $propertie['id']; ?>"><button class="btn btn-primary" onclick="return confirmerDelete()">Supprimer</button></a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <!-- Pagination -->
            <nav aria-label="Page navigation example" class="my-5">
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
    <!-- Chat Icon -->   
    </div>
    <footer class="ftco-footer ftco-section">
        <?php require_once(__DIR__. '/footer.php'); ?>
    </footer>
    <script>
        function confirmerDelete(){
            return confirm("Cette action est irréversible");
        }
    </script>
    <script>
        document.getElementById('chatIcon').addEventListener('click', function() {
            document.getElementById('chatBox').style.display = 'block';
        });
        document.getElementById('closeChat').addEventListener('click', function() {
            document.getElementById('chatBox').style.display = 'none';
        });
    </script>
    <script src="js/main.js"></script>
</body>
</html>
