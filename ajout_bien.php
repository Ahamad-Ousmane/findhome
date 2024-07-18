<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
  exit();
}



include 'admin/db.php';

$sql = "SELECT * from departement";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$departements = $stmt -> fetchAll(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST'){

// Requête d'insertion
$sql = "INSERT INTO properties (title, type, main_image, image1, image2, image3, description, bedrooms, bathrooms, area, quartier, state,  price, status,user_id)
VALUES (:title, :type, :main_image, :image1, :image2, :image3, :description, :bedrooms, :bathrooms, :area, :quartier, :state, :price, :status,:user_id)";

// Préparation de la requête
$stmt = $pdo->prepare($sql);

// Liaison des paramètres
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
$stmt->bindParam(':quartier', $quartier);
$stmt->bindParam(':state', $state);

$stmt->bindParam(':price', $price);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':user_id', $_SESSION['user_id']);

// Assignation des valeurs aux paramètres
$title = $_POST['title'];
$type = $_POST['type'];
$user_id = $_SESSION['user_id'];


$main_image = file_get_contents($_FILES['main_image']['tmp_name']);
$image1 = file_get_contents($_FILES['image1']['tmp_name']);
$image2 = file_get_contents($_FILES['image2']['tmp_name']);
$image3 = file_get_contents($_FILES['image3']['tmp_name']);
$description = $_POST['description'];
$bedrooms = $_POST['bedrooms'];
$bathrooms = $_POST['bathrooms'];
$area = $_POST['area'];
$quartier = $_POST['quartier'];
$state = $_POST['state'];

$price = $_POST['price'];
$status = $_POST['status'];

// Exécution de la requête
try {
    // Exécutez la première requête SQL pour insérer dans la table properties
    if ($stmt->execute()) {
        // Récupérez l'ID inséré dans la table properties
        $propertie_id = $pdo->lastInsertId();

        // Construisez le message pour la notification
        $message = " <strong> Titre du bien </strong> : $title.";  // Assurez-vous que $title est défini correctement dans votre code

        // Préparez et exécutez la requête SQL pour insérer dans la table notifications
        $sqlNotification = "INSERT INTO notifications (user_id, propertie_id, message) VALUES (:user_id, :propertie_id, :message)";
        $stmtNotification = $pdo->prepare($sqlNotification);
        $stmtNotification->bindParam(':user_id', $user_id);  // Assurez-vous que $user_id est défini correctement dans votre code
        $stmtNotification->bindParam(':propertie_id', $propertie_id);
        $stmtNotification->bindParam(':message', $message);
        $stmtNotification->execute();

        header("Location: index.php");
        exit;
    }

} catch (PDOException $e) {
    // Capturez les erreurs PDO
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
    <title>FindHome</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


      <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


      <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    

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
            
                  <h1 class="" style="font-weight:bold;">Ajouter une propriété</h1>
                  <form action="" method="POST"   enctype="multipart/form-data">
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="title">Titre :</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                          </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="type">Type :</label>
                            <select class="form-control" id="type" name="type" required>
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
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        <div class="row">
                          <div class="col-md-4">
                          <div class="form-group">
                            <label for="bedrooms">Chambres :</label>
                            <input type="number" class="form-control" id="bedrooms" name="bedrooms" min="0" required>
                        </div>
                          </div>
                          <div class="col-md-4">
                          <div class="form-group">
                            <label for="bathrooms">Salles de bains :</label>
                            <input type="number" class="form-control" id="bathrooms" name="bathrooms" min="0" required>
                        </div>
                          </div>
                          <div class="col-md-4">
                          <div class="form-group">
                            <label for="area">Superficie (m²) :</label>
                            <input type="number" class="form-control" id="area" name="area" min="0" step="0.01" required>
                        </div>
                          </div>

                        </div>
                        <div class="row">
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="state">État :</label>
                            <input type="text" class="form-control" id="state" name="state" required>
                        </div>
                          </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label for="price">Prix :</label>
                            <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
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
                                <option value="for_sale">À vendre</option>
                                <option value="for_rent">À louer</option>
                            </select>
                        </div>
                        <div class="d-flex">
                        <button type="submit" class="btn btn-primary mt-2">Ajouter la propriété</button>
                         <a href="index.php" class="btn btn-danger mt-2 mx-2" >Annuler</a> 
                        </div>   
                    </form>

            </div>
          </div>
  

        
      </div>
    </div>
    <!-- JavaScript Files -->
    <script src="js/core/jquery.3.2.1.min.js"></script>
    <script src="js/core/popper.min.js"></script>
    <script src="js/core/bootstrap.min.js"></script>
    <script src="js/plugins/bootstrap-notify.min.js"></script>
    <script src="js/kaiadmin.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#departement,#type, #commune, #arrondissement, #quartier, #status').select2();
        
        $('#departement').on('change', function() {
            var departementId = $(this).val();
            if (departementId) {
                fetch('get_communes.php?departement_id=' + departementId)
                    .then(response => response.json())
                    .then(data => {
                        var communeSelect = $('#commune');
                        communeSelect.empty().append('<option selected disabled>Choisir une commune</option>');
                        data.forEach(function (commune) {
                            communeSelect.append('<option value="' + commune.id_com + '">' + commune.lib_com + '</option>');
                        });
                        $('#arrondissement').empty().append('<option selected disabled>Choisir un arrondissement</option>');
                        $('#quartier').empty().append('<option selected disabled>Choisir un quartier</option>');
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des communes : ', error);
                    });
            } else {
                $('#commune').empty().append('<option selected disabled>Choisir une commune</option>');
                $('#arrondissement').empty().append('<option selected disabled>Choisir un arrondissement</option>');
                $('#quartier').empty().append('<option selected disabled>Choisir un quartier</option>');
            }
        });

        $('#commune').on('change', function() {
            var communeId = $(this).val();
            if (communeId) {
                fetch('get_arrondissements.php?commune_id=' + communeId)
                    .then(response => response.json())
                    .then(data => {
                        var arrondissementSelect = $('#arrondissement');
                        arrondissementSelect.empty().append('<option selected disabled>Choisir un arrondissement</option>');
                        data.forEach(function (arrondissement) {
                            arrondissementSelect.append('<option value="' + arrondissement.id_arrond + '">' + arrondissement.lib_arrond + '</option>');
                        });
                        $('#quartier').empty().append('<option selected disabled>Choisir un quartier</option>');
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des arrondissements : ', error);
                    });
            } else {
                $('#arrondissement').empty().append('<option selected disabled>Choisir un arrondissement</option>');
                $('#quartier').empty().append('<option selected disabled>Choisir un quartier</option>');
            }
        });

        $('#arrondissement').on('change', function() {
            var arrondissementId = $(this).val();
            if (arrondissementId) {
                fetch('get_quartiers.php?arrondissement_id=' + arrondissementId)
                    .then(response => response.json())
                    .then(data => {
                        var quartierSelect = $('#quartier');
                        quartierSelect.empty().append('<option selected disabled>Choisir un quartier</option>');
                        data.forEach(function (quartier) {
                            quartierSelect.append('<option value="' + quartier.lib_quart + '">' + quartier.lib_quart + '</option>');
                        });
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des quartiers : ', error);
                    });
            } else {
                $('#quartier').empty().append('<option selected disabled>Choisir un quartier</option>');
            }
        });
    });
</script>
  </body>
</html>
