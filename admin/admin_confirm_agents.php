<?php
session_start();
require_once(__DIR__ . '/db.php');

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];
$admin_id = $_SESSION['admin_id'];

// Récupérer les demandes non confirmées
$sql = "SELECT ar.id, ar.user_id, u.username, u.email, ar.id_card_front, ar.id_card_back, ar.telephone
        FROM agent_requests ar 
        JOIN user u ON ar.user_id = u.id 
        WHERE ar.confirmed = FALSE AND ar.refused = FALSE
        ORDER BY ar.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_request_id'])) {
    $request_id = $_POST['confirm_request_id'];
    $user_id = $_POST['user_id'];

    $sqlUpdate = "UPDATE user SET profile = TRUE WHERE id = :user_id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':user_id', $user_id);

    if($stmtUpdate->execute()){
     
        $message = "Vous etes maintenant devenu un agent ";

        $sql2 = "INSERT into notifs (user_id,message) VALUES (:user_id,:message)";
        $stmt2 = $pdo -> prepare($sql2);
        $stmt2 -> execute([':user_id'=>$user_id,':message'=>$message]);
    }

    $sqlConfirmRequest = "UPDATE agent_requests SET confirmed = TRUE WHERE id = :request_id";
    $stmtConfirmRequest = $pdo->prepare($sqlConfirmRequest);
    $stmtConfirmRequest->bindParam(':request_id', $request_id);
    $stmtConfirmRequest->execute();
    header("Location: admin_confirm_agents.php");
    exit();

}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['refuse_request_id'])) {
  $request_id = $_POST['refuse_request_id'];
  $user_id = $_POST['user_id'];

  $sqlUpdate = "UPDATE user SET profile = FALSE WHERE id = :user_id";
  $stmtUpdate = $pdo->prepare($sqlUpdate);
  $stmtUpdate->bindParam(':user_id', $user_id);
  $stmtUpdate->execute();

  $sqlrefuseRequest = "UPDATE agent_requests SET refused = TRUE WHERE id = :request_id";
  $stmtrefuseRequest = $pdo->prepare($sqlrefuseRequest);
  $stmtrefuseRequest->bindParam(':request_id', $request_id);
  $stmtrefuseRequest->execute();

  header("Location: admin_confirm_agents.php");
  exit();

}

//Récupérer la table des administrateurs
$sqlAdmin = "SELECT * FROM admin WHERE id = $admin_id";
$stmtAdmin = $pdo->prepare($sqlAdmin);
$stmtAdmin->execute();
$Admin = $stmtAdmin->fetch();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>FindHome | Confirmation des demandes</title>
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <?php require_once (__DIR__. '/link_icons.php') ?>
    <style>
        .confirmation {
            border-radius: 5px;
            padding: 10px;
            margin: 10px ;
        }
        .bien{
          margin-top: 19px;
          border-radius: 10px !important;
          font-size: 1.1rem !important;
          font-family: system-ui !important;
          
        }
    </style>
    
</head>
<body>
  <?php require_once(__DIR__ . '/sidebar.php'); ?>
  <div class="main-panel">
    <div class="main-header">
      <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
            <div class="container-fluid">
        
              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
              
               

                <li class="nav-item topbar-user dropdown hidden-caret">
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?= $admin_name?></span>
                    </span>
                  </a>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M399 384.2C376.9 345.8 335.4 320 288 320H224c-47.4 0-88.9 25.8-111 64.2c35.2 39.2 86.2 63.8 143 63.8s107.8-24.7 143-63.8zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256zm256 16a72 72 0 1 0 0-144 72 72 0 1 0 0 144z"/></svg>
                          </div>
                          <div class="u-text">
                            <h4><?= $admin_name?></h4>
                            <p class="text-muted"><?= $Admin['email'] ?></p>
                            <!-- <a
                              href="profile.html"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            > -->
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item " id="profile" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">My Balance</a>
                        <a class="dropdown-item" href="#">Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="logout.php" onclick="confirmAction(event)">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
      </nav>
    </div>



    <div class="container">
        <a href="./ajouter_agent.php" class ="btn btn-primary bien ms-5"> Ajouter un agent <svg xmlns="http://www.w3.org/2000/svg" width="20" height="" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
          <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
        </svg></a>
      <div class="page-inner">
      <div class="row">
      <div class="col-md-12">
       
          <div class="card">
              <div class="card-header">
                  <h4 class="card-title">Confirmer les agents</h4>
              </div>
              <div class="card-body">
                  <div class="table-responsive">
                      <table id="basic-datatables" class="display table table-striped table-hover">
                          <thead>
                              <tr>
                                  <th>Nom d'utilisateur</th>
                                  <th>Email</th>
                                  <th>Téléphone</th>
                                  <th>Carte d'identité (recto)</th>
                                  <th>Carte d'identité (verso)</th>
                                  <th>Actions</th>
                              </tr>
                          </thead>
                          <tfoot>
                              <tr>
                                  <th>Nom d'utilisateur</th>
                                  <th>Email</th>
                                  <th>Téléphone</th>
                                  <th>Carte d'identité (recto)</th>
                                  <th>Carte d'identité (verso)</th>
                                  <th>Actions</th>
                              </tr>
                          </tfoot>
                          <tbody>
                              <?php if ($requests): ?>
                              <?php foreach ($requests as $request): ?>
                              <tr>
                                  <td><?= htmlspecialchars($request['username']) ?></td>
                                  <td><?= htmlspecialchars($request['email']) ?></td>
                                  <td><?= htmlspecialchars($request['telephone']) ?></td>
                                  <td><a href="#" class="view-id-card" data-toggle="modal" data-target="#idCardFrontModal" data-img-src="../<?= htmlspecialchars($request['id_card_front']) ?>">Voir</a></td>
                                  <td><a href="#" class="view-id-card" data-toggle="modal" data-target="#idCardBackModal" data-img-src="../<?= htmlspecialchars($request['id_card_back']) ?>">Voir</a></td>
                                  <td>
                                      <div class="dropdown">
                                      <button class='btn btn-secondary dropdown-toggle' type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                                  Actions
                                              </button>
                                          <div class="dropdown-menu" aria-labelledby="actionDropdown">
                                              <form method="POST" style="display:inline;">
                                                  <input type="hidden" name="confirm_request_id" value="<?= $request['id'] ?>">
                                                  <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                                  <button class="dropdown-item" type="submit"  id="alert_demo_3_4">Confirmer</button>
                                              </form>
                                              <form method="POST" style="display:inline;">
                                                  <input type="hidden" name="refuse_request_id" value="<?= $request['id'] ?>">
                                                  <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                                  <button class="dropdown-item" type="submit">Rejeter</button>
                                              </form>
                                          </div>
                                      </div>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                              <?php else: ?>
                              <tr>
                                  <td colspan="6">Aucune demande</td>
                              </tr>
                              <?php endif; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    </div>



        
  </div>


    <!-- Modal for Front ID Card -->
<div class="modal fade" id="idCardFrontModal" tabindex="-1" role="dialog" aria-labelledby="idCardFrontModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="idCardFrontModalLabel">Carte d'identité (recto)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="idCardFrontImage" class="img-fluid" alt="Carte d'identité (recto)">
      </div>
    </div>
  </div>
</div>

<!-- Modal for Back ID Card -->
<div class="modal fade" id="idCardBackModal" tabindex="-1" role="dialog" aria-labelledby="idCardBackModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="idCardBackModalLabel">Carte d'identité (verso)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" id="idCardBackImage" class="img-fluid" alt="Carte d'identité (verso)">
      </div>
    </div>
  </div>
</div>


    <?php require_once(__DIR__.'/footer.php') ?>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var viewIdCardLinks = document.querySelectorAll('.view-id-card');

    viewIdCardLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        var imgSrc = this.getAttribute('data-img-src');
        var targetModal = document.querySelector(this.getAttribute('data-target'));
        var modalImg = targetModal.querySelector('img');

        modalImg.src = imgSrc;
      });
    });
  });
</script>

<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


<script src="assets/js/plugin/datatables/datatables.min.js"></script>


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

<script>
      //== Class definition
      var SweetAlert2Demo = (function () {
        //== Demos
        var initDemos = function () {

          $("#alert_demo_3_4").click(function (e) {
            swal("Confirmé!", {
              icon: "info",
              buttons: {
                confirm: {
                  className: "btn btn-info",
                },
              },
            });
          });

    
         
        };

        return {
          //== Init
          init: function () {
            initDemos();
          },
        };
      })();

      //== Class Initialization
      jQuery(document).ready(function () {
        SweetAlert2Demo.init();
      });
    </script>

    
</body>
</html>
