<?php
session_start();
include 'db.php';

// Vérifier si un identifiant de propriété a été passé en paramètre
if (isset($_GET['id'])) {
    $propertyId = intval($_GET['id']);
} else {
    die("Erreur : Aucun identifiant de propriété fourni.");
}

// Récupérer les informations de la propriété depuis la base de données
$sql = "SELECT * FROM properties_admin WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $propertyId, PDO::PARAM_INT);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

$sql3 = "SELECT * from departement";
$stmt3 = $pdo->prepare($sql3);
$stmt3->execute();
$departements = $stmt3 -> fetchAll(PDO::FETCH_ASSOC);

// Vérifier si le bien existe
if (!$property) {
    die("Erreur : Propriété non trouvée.");
}

// Si le formulaire est soumis, mettre à jour les informations du bien
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE properties 
            SET title = :title, type = :type, main_image = :main_image, image1 = :image1, image2 = :image2, image3 = :image3, description = :description, bedrooms = :bedrooms, bathrooms = :bathrooms, area = :area, address = :address, city = :city, state = :state, country = :country, price = :price, status = :status
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':main_image', $main_image, PDO::PARAM_LOB);
    $stmt->bindParam(':image1', $image1, PDO::PARAM_LOB);
    $stmt->bindParam(':image2', $image2, PDO::PARAM_LOB);
    $stmt->bindParam(':image3', $image3, PDO::PARAM_LOB);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':bedrooms', $bedrooms);
    $stmt->bindParam(':bathrooms', $bathrooms);
    $stmt->bindParam(':area', $area);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $propertyId, PDO::PARAM_INT);

    // Assignation des valeurs aux paramètres
    $title = $_POST['title'];
    $type = $_POST['type'];
    $main_image = file_get_contents($_FILES['main_image']['tmp_name']);
    $image1 = file_get_contents($_FILES['image1']['tmp_name']);
    $image2 = file_get_contents($_FILES['image2']['tmp_name']);
    $image3 = file_get_contents($_FILES['image3']['tmp_name']);
    $description = $_POST['description'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $area = $_POST['area'];
    $quartier = $_POST['quartier'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $country = $_POST['country'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Exécution de la requête
    try {
        $stmt->execute();
        echo "La propriété a été mise à jour avec succès.";
        header("Location: dash_user.php");
        exit;
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }

    // Fermeture de la connexion
    $pdo = null;


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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
     <?php require_once(__DIR__.'/link_icons.php') ?>
</head>
<body>
<?php require_once(__DIR__ .'/sidebar.php'); ?>

        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
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
                    <!-- End Logo Header -->
                </div>
                <!-- Navbar Header -->
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">
                        <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">

                        </nav>
                    </div>
                </nav>
                <!-- End Navbar -->
            </div>
            <div class="container">
                <div class="page-inner">
                    <h2 class="mt-4">Modifier une propriété</h2>
                    <form action="" method="POST"   enctype="multipart/form-data">
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="title">Titre :</label>
                            <input type="text" class="form-control" id="title" name="title"  value = "<?= htmlspecialchars($property['title']) ?>"required>
                        </div>
                          </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="type">Type :</label>
                            <select class="form-control" id="type" name="type" value = <?= htmlspecialchars($property['type']) ?> required>
                                <option value="house">Maison</option>
                                <option value="apartment">Appartement</option>
                                <option value="condo">Condo</option>
                                <option value="townhouse">Maison de ville</option>
                                <option value="land">Terrain</option>
                            </select>
                        </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="main_image_url">URL de l'image principale :</label>
                            <input type="file" class="form-control" id="main_image_url" name="main_image" required>
                        </div>
                          </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="image1_url">image 1 :</label>
                            <input type="file" class="form-control" id="image1_url" name="image1">
                        </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="image2_url">image 2 :</label>
                            <input type="file" class="form-control" id="image2_url" name="image2">
                        </div>
                          </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="image3_url">image 3 :</label>
                            <input type="file" class="form-control" id="image3_url" name="image3">
                        </div>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description :</label>
                            <textarea class="form-control" id="description" name="description" rows="3" ><?= htmlspecialchars( $property['description']) ?></textarea>

                        </div>
                        <div class="row">
                          <div class="col-md-4">
                          <div class="form-group">
                            <label for="bedrooms">Chambres :</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms" min="0" value="<?=  htmlspecialchars($property['bedrooms']) ?>" required>

                        </div>
                          </div>
                          <div class="col-md-4">
                          <div class="form-group">
                            <label for="bathrooms">Salles de bains :</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" min="0" value = "<?= htmlspecialchars( $property['bathrooms']) ?>" required>

                        </div>
                          </div>
                          <div class="col-md-4">
                          <div class="form-group">
                            <label for="area">Superficie (m²) :</label>
                            <input type="number" class="form-control" id="area" name="area" min="0" value = "<?= htmlspecialchars($property['area']) ?>step="0.01" required>
                        </div>
                          </div>

                        </div>
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="state">État :</label>
                            <input type="text" class="form-control" id="state" name="state" value = "<?= htmlspecialchars( $property['state']) ?>" required>
                        </div>
                          </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="price">Prix :</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="<?= htmlspecialchars( $property['price']) ?>" required>
                        </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-3">
                            <div class="form-group">
                              <label for="departement">Département :</label>
                                <select class="form-control" id="departement" name="departement" required>
                                  <option value="">Sélectionnez un département</option>
                                  <?php foreach ($departements as $departement): ?>
                                    <option value="<?= $departement['id_dep'] ?>"><?= $departement['lib_dep'] ?></option>
                                  <?php endforeach; ?>
                                </select>
                            </div>
                          </div>
                          <div class="col-md-3">
                          <div class="form-group">
                                                <label for="commune">Commune :</label>
                                                <select class="form-control" id="commune" name="commune" required>
                                                    <option value="">Sélectionnez une commune</option>
                                                    <!-- Options de commune seront chargées via AJAX -->
                                                </select>
                                            </div>
                          </div>
                          <div class="col-md-3">
                          <div class="form-group">
                                                <label for="arrondissement">Arrondissement :</label>
                                                <select class="form-control" id="arrondissement" name="arrondissement" required>
                                                    <option value="">Sélectionnez un arrondissement</option>
                                                    <!-- Options d'arrondissement seront chargées via AJAX -->
                                                </select>
                                            </div>
                          </div>
                          <div class="col-md-3">
                          <div class="form-group">
                                        <label for="quartier">Quartier :</label>
                                        <select class="form-control" id="quartier" name="quartier" required>
                                            <option value="">Sélectionnez un quartier</option>
                                            <!-- Options de quartier seront chargées via AJAX -->
                                        </select>
                                    </div>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="status">Statut :</label>
                            <select class="form-control" id="status" name="status" value= "<?= htmlspecialchars($property['status']) ?>" required>
                                <option value="for_sale">À vendre</option>
                                <option value="for_rent">À louer</option>
                            </select>
                        </div>
                        <div class="d-flex">
                        <button type="submit" class="btn btn-primary mt-2">Modifier la propriété</button>
                         <a href="properties_views.php" class="btn btn-danger mt-2 mx-2" >Annuler</a> 
                        </div>   
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('departement').addEventListener('change', function () {
            var departementId = this.value;
            if (departementId) {
                fetch('get_communes.php?departement_id=' + departementId)
                    .then(response => response.json())
                    .then(data => {
                        var communeSelect = document.getElementById('commune');
                        communeSelect.innerHTML = '<option selected disabled>Choisir une commune</option>';
                        data.forEach(function (commune) {
                            communeSelect.innerHTML += '<option value="' + commune.id_com + '">' + commune.lib_com + '</option>';
                        });
                        document.getElementById('arrondissement').innerHTML = '<option selected disabled>Choisir un arrondissement</option>';
                        document.getElementById('quartier').innerHTML = '<option selected disabled>Choisir un quartier</option>';
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des communes : ', error);
                    });
            } else {
                document.getElementById('commune').innerHTML = '<option selected disabled>Choisir une commune</option>';
                document.getElementById('arrondissement').innerHTML = '<option selected disabled>Choisir un arrondissement</option>';
                document.getElementById('quartier').innerHTML = '<option selected disabled>Choisir un quartier</option>';
            }
        });

        document.getElementById('commune').addEventListener('change', function () {
            var communeId = this.value;
            if (communeId) {
                fetch('get_arrondissements.php?commune_id=' + communeId)
                    .then(response => response.json())
                    .then(data => {
                        var arrondissementSelect = document.getElementById('arrondissement');
                        arrondissementSelect.innerHTML = '<option selected disabled>Choisir un arrondissement</option>';
                        data.forEach(function (arrondissement) {
                            arrondissementSelect.innerHTML += '<option value="' + arrondissement.id_arrond + '">' + arrondissement.lib_arrond + '</option>';
                        });
                        document.getElementById('quartier').innerHTML = '<option selected disabled>Choisir un quartier</option>';
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des arrondissements : ', error);
                    });
            } else {
                document.getElementById('arrondissement').innerHTML = '<option selected disabled>Choisir un arrondissement</option>';
                document.getElementById('quartier').innerHTML = '<option selected disabled>Choisir un quartier</option>';
            }
        });

        document.getElementById('arrondissement').addEventListener('change', function () {
            var arrondissementId = this.value;
            if (arrondissementId) {
                fetch('get_quartiers.php?arrondissement_id=' + arrondissementId)
                    .then(response => response.json())
                    .then(data => {
                        var quartierSelect = document.getElementById('quartier');
                        quartierSelect.innerHTML = '<option selected disabled>Choisir un quartier</option>';
                        data.forEach(function (quartier) {
                            quartierSelect.innerHTML += '<option value="' + quartier.lib_quart + '">' + quartier.lib_quart + '</option>';
                        });
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des quartiers : ', error);
                    });
            } else {
                document.getElementById('quartier').innerHTML = '<option selected disabled>Choisir un quartier</option>';
            }
        });
    </script>

    <?php require_once(__DIR__. '/footer.php'); ?>
</body>
</html>
