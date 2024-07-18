<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
  header("Location: loginadmin.php");
  exit();
}

include 'db.php';
$admin_id = $_SESSION['admin_id'];



// Récupérer les biens pour la page actuelle
$sql = "(SELECT id, title, main_image, price, status, is_confirmed, is_rejected, 'properties_admin' AS source, created_at 
         FROM properties_admin 
         WHERE admin_id = :admin_id AND is_delete = false)
        UNION 
        (SELECT id, title, main_image, price, status, is_confirmed, is_rejected, 'properties' AS source, created_at 
         FROM properties 
         WHERE is_delete = false)
        ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([':admin_id' => $admin_id]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);




?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>FindHome</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <?php require_once (__DIR__. '/link_icons.php') ?>
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
            background-color: #ffc107; /* Jaune pour "À louer" */
            color: #000;
        }
        .bien{
          margin-top: 19px;
          border-radius: 10px !important;
          font-size: 1.1rem !important;
          font-family: system-ui !important;
          
        }
        tr img {
          height: 40px;
          width: 40px;
          border-radius: 100%;
          object-fit: cover;
          object-position: center;
        }
        .dropdown-toggle {
          border-radius: 10px !important;
        }
        td a{
          padding-right:  30px;
          padding-left: 30px;
        }
        .dropdown-item {
          font-family: system-ui,sans-serif;
          font-weight: 600 !important;
        }
    </style>
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
          <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
            <nav class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
                <form class="form-inline my-2 my-lg-0 d-flex" method="GET" action="search_results.php">
                    <input class="form-control mr-sm-2"  type="search" placeholder="Rechercher un bien..." aria-label="Search" name="query">
                    <button class="btn btn-outline-primary my-2 my-sm-0" type="submit"><i class="fa fa-search"></i></button>
                </form>
            </nav>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
        <div class="container">
        <div class="container">
        <a href="add_biens.php" class ="btn btn-primary bien ms-5"> Ajouter un bien <svg xmlns="http://www.w3.org/2000/svg" width="20" height="" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
        </svg></a>
          <div class="page-inner">
                <div class="row">
                <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <h1 class="card-title">Liste des biens immobiliers</h1>
                  </div>
                  <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover ms-3" id="basic-datatables">
                      <thead>
                        <tr>
                          <th>Image</th>
                          <th>Titre</th>
                          <th>Prix</th>
                          <th>Status</th>
                          <th>Etat</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($properties as $property) : ?>
                          <tr>
                            <td><img src="data:image/jpeg;base64,<?= base64_encode($property['main_image']) ?>" alt="Image principale"></td>
                            <td><a href="property_details.php?<?= ($property['source'] === 'properties') ? 'id=' . $property['id'] : 'id_admin=' . $property['id'] ?>"><?= htmlspecialchars($property['title']) ?></a></td>
                            <td><?= htmlspecialchars($property['price']) ?> fcfa</td>
                            <td><?= ($property['status'] === 'for_sale') ? 'A vendre' : 'A louer' ?></td>
                            <?php if ($property['is_confirmed'] == false && $property['is_rejected'] == false) : ?>
                              <td><em style="color: red">En attente d'approbation</em></td>
                            <?php elseif ($property['is_confirmed'] == true && $property['is_rejected'] == false) : ?>
                              <td><em>Confirmé</em></td>
                            <?php elseif ($property['is_confirmed'] == false && $property['is_rejected'] == true) : ?>
                              <td><em>Rejeté</em></td>
                            <?php endif; ?>
                            <td>
                              <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownActions<?= $property['id'] ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                  Actions
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownActions<?= $property['id'] ?>">
                                <?php if ($property['source'] === 'properties_admin') : ?>
                                  <li><a class="dropdown-item" href="property_edit.php?id=<?= $property['id'] ?>" style="opacity: 0.7">Modifier</a></li>
                                    <li><a class="dropdown-item" href="supprimer_bien.php?id_admin=<?= $property['id'] ?>" style="opacity: 0.7">Supprimer</a></li>
                                  <?php else: ?>
                                    <li><a href="notification_read.php?id=<?= $property['id'] ?>" class="dropdown-item" style="opacity: 0.7">Traiter</a></li>
                                    <li><a class="dropdown-item" href="supprimer_bien.php?id= <?= $property['id'] ?>" style="opacity: 0.7">Supprimer</a></li>
                                  <?php endif; ?>
                                </ul>
                              </div>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>  
                  </div> 
                  </div>     
                </div>
                </div>
                </div>
          </div>
         
        </div>
      </div>
    </div>
  </div>
  <?php require_once(__DIR__ . '/footer.php'); ?>

 


<script>
      $(document).ready(function () {
        $("#basic-datatables").DataTable({});

        $("#multi-filter-select").DataTable({
          pageLength: 5,
          initComplete: function () {
            this.api()
              .columns()
              .every(function () {
                var column = this;
                var select = $(
                  '<select class="form-select"><option value=""></option></select>'
                )
                  .appendTo($(column.footer()).empty())
                  .on("change", function () {
                    var val = $.fn.dataTable.util.escapeRegex($(this).val());

                    column
                      .search(val ? "^" + val + "$" : "", true, false)
                      .draw();
                  });

                column
                  .data()
                  .unique()
                  .sort()
                  .each(function (d, j) {
                    select.append(
                      '<option value="' + d + '">' + d + "</option>"
                    );
                  });
              });
          },
        });

        // Add Row
        $("#add-row").DataTable({
          pageLength: 5,
        });

        var action =
          '<td> <div class="form-button-action"> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#addRowButton").click(function () {
          $("#add-row")
            .dataTable()
            .fnAddData([
              $("#addName").val(),
              $("#addPosition").val(),
              $("#addOffice").val(),
              action,
            ]);
          $("#addRowModal").modal("hide");
        });
      });

      
    </script>
  </body>
</html>
