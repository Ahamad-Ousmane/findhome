<?php
session_start(); // Démarrer la session
require_once(__DIR__ . '/admin/db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si un ID de projet est passé en paramètre
$propertie_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Vérifier si le projet appartient à l'utilisateur
$sql = "SELECT * FROM properties WHERE id = :propertie_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':propertie_id', $propertie_id, PDO::PARAM_INT);
$stmt->execute();
$property = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT * from departement";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$departements = $stmt -> fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "UPDATE properties SET 
                title = :title, 
                type = :type, 
                description = :description, 
                bedrooms = :bedrooms, 
                bathrooms = :bathrooms, 
                area = :area, 
                quartier = :quartier, 
                state = :state, 
                country = :country, 
                price = :price, 
                status = :status, 
                is_confirmed = false,
                created_at = CURRENT_TIMESTAMP";

    // Gestion des images
    if (isset($_FILES['main_image']) && $_FILES['main_image']['tmp_name'] !== '') {
        $sql .= ", main_image = :main_image";
    }
    if (isset($_FILES['image1']) && $_FILES['image1']['tmp_name'] !== '') {
        $sql .= ", image1 = :image1";
    }
    if (isset($_FILES['image2']) && $_FILES['image2']['tmp_name'] !== '') {
        $sql .= ", image2 = :image2";
    }
    if (isset($_FILES['image3']) && $_FILES['image3']['tmp_name'] !== '') {
        $sql .= ", image3 = :image3";
    }

    $sql .= " WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':type', $type);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':bedrooms', $bedrooms, PDO::PARAM_INT);
    $stmt->bindParam(':bathrooms', $bathrooms, PDO::PARAM_INT);
    $stmt->bindParam(':area', $area, PDO::PARAM_INT);
    $stmt->bindParam(':quartier', $quartier);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':price', $price, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $propertie_id, PDO::PARAM_INT);

    // Assignation des valeurs aux paramètres
    $title = $_POST['title'];
    $type = $_POST['type'];
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

    // Gestion des images
    if (isset($_FILES['main_image']) && $_FILES['main_image']['tmp_name'] !== '') {
        $main_image = file_get_contents($_FILES['main_image']['tmp_name']);
        $stmt->bindParam(':main_image', $main_image, PDO::PARAM_LOB);
    }
    if (isset($_FILES['image1']) && $_FILES['image1']['tmp_name'] !== '') {
        $image1 = file_get_contents($_FILES['image1']['tmp_name']);
        $stmt->bindParam(':image1', $image1, PDO::PARAM_LOB);
    }
    if (isset($_FILES['image2']) && $_FILES['image2']['tmp_name'] !== '') {
        $image2 = file_get_contents($_FILES['image2']['tmp_name']);
        $stmt->bindParam(':image2', $image2, PDO::PARAM_LOB);
    }
    if (isset($_FILES['image3']) && $_FILES['image3']['tmp_name'] !== '') {
        $image3 = file_get_contents($_FILES['image3']['tmp_name']);
        $stmt->bindParam(':image3', $image3, PDO::PARAM_LOB);
    }

    // Exécution de la requête
    try {
        if ($stmt->execute()) {
            $sqlLastUpdated = "SELECT id FROM properties ORDER BY created_at DESC LIMIT 1";
            $stmtLastUpdated = $pdo->prepare($sqlLastUpdated);
            $stmtLastUpdated->execute();
            $lastUpdatedProperty = $stmtLastUpdated->fetch(PDO::FETCH_ASSOC);

            $message = "Projet " . $lastUpdatedProperty['id'] . " mis à jour";
            $sqlupdateNotification = "UPDATE notifications SET message = :message, is_read = false WHERE propertie_id = :id";
            $stmtupdateNotification = $pdo->prepare($sqlupdateNotification);
            $stmtupdateNotification->bindParam(':id', $lastUpdatedProperty['id']);
            $stmtupdateNotification->bindParam(':message', $message);

            $stmtupdateNotification->execute();
        }

        header("Location: user_dash.php");
        exit();
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }

    // Fermeture de la connexion
    $pdo = null;
}

// Afficher le formulaire de modification avec les données existantes
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Biens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <style>

.container {
        backdrop-filter: blur(20px);
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 1000px;
        width: 100%;
    }
    .page-inner {
        margin-bottom: 20px;
    }
    .page-title {
        font-weight: bold;
        color: #495057;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    label {
        font-weight: bold;
    }
    .form-control {
        border-color: #ced4da;
        box-shadow: none;
    }
    .form-control:focus {
        border-color: #6cb2eb;
        box-shadow: 0 0 0 0.25rem rgba(108, 178, 235, 0.25);
    }
    .btn-primary {
        background-color: #6cb2eb;
        border-color: #6cb2eb;
        border-radius: 10px;
    }
    
    .btn-primary:hover, .btn-primary:focus {
        background-color: #559bd7;
        border-color: #559bd7;
    }
</style>
</head>

<body>
<div class="container">
            <div class="page-inner">
            
                  <h1 class="" style="font-weight:bold;">Modifier une propriete</h1>
                  <form action="" method="POST"   enctype="multipart/form-data">
                  <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="title">Titre :</label>
                          <input type="text" class="form-control" id="title" value="<?= htmlspecialchars($property['title']) ?>" name="title" required>
                      </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="type">Type :</label>
                          <select class="form-control" id="type" name="type" required>
                              <option value="house" <?= $property['type'] == 'house' ? 'selected' : '' ?>>Maison</option>
                              <option value="apartment" <?= $property['type'] == 'apartment' ? 'selected' : '' ?>>Appartement</option>
                              <option value="condo" <?= $property['type'] == 'condo' ? 'selected' : '' ?>>Logement</option>
                              <option value="townhouse" <?= $property['type'] == 'townhouse' ? 'selected' : '' ?>>Maison de ville</option>
                              <option value="land" <?= $property['type'] == 'land' ? 'selected' : '' ?>>Terrain</option>
                          </select>
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
                          <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($property['description']) ?></textarea>
                      </div>
                      <div class="row">
                        <div class="col-md-4">
                        <div class="form-group">
                          <label for="bedrooms">Chambres :</label>
                          <input type="number" class="form-control" id="bedrooms" name="bedrooms" min="0" value="<?= $property['bedrooms'] ?>" required>
                      </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                          <label for="bathrooms">Salles de bains :</label>
                          <input type="number" class="form-control" id="bathrooms" name="bathrooms" min="0" value="<?= $property['bathrooms'] ?>" required>
                      </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                          <label for="area">Superficie (m²) :</label>
                          <input type="number" class="form-control" id="area" name="area" min="0" step="0.01" value="<?= $property['area'] ?>" required>
                      </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="state">État :</label>
                          <input type="text" class="form-control" id="state" name="state" value="<?= htmlspecialchars($property['state']) ?>" required>
                      </div>
                        </div>
                        <div class="col-md-6">
                        <div class="form-group">
                          <label for="price">Prix :</label>
                          <input type="number" class="form-control" id="price" name="price" value="<?= $property['price'] ?>" min="0" step="0.01" required>
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
                          <select class="form-control" id="status" name="status" required>
                              <option value="for_sale" <?= $property['status'] == 'for_sale' ? 'selected' : '' ?>>À vendre</option>
                              <option value="for_rent" <?= $property['status'] == 'for_rent' ? 'selected' : '' ?>>À louer</option>
                          </select>
                      </div>
                      <div class="d-flex">
                      <button type="submit" class="btn btn-primary mt-2">Mettre a jour la propriete</button>
                      <a href="user_dash.php" class="btn btn-danger mt-2 mx-2" >Annuler</a> 
                      </div>
                      
                  </form>


            </div>
          </div>
  

        
      </div>
    </div>
<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
</body>
</html>
