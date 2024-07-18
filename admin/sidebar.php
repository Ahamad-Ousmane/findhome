<?php 

if (!isset($_SESSION['admin_id'])) {
    header("Location: loginadmin.php");
    exit();
}

//Récupérer les notifications non lues
$sql1 = "SELECT n.id, n.message, n.created_at, u.username 
        FROM notifications n 
        JOIN user u ON n.user_id = u.id 
        WHERE n.is_read = FALSE
        ORDER BY n.created_at DESC";
$stmt1 = $pdo->prepare($sql1);
$stmt1->execute();
$notification = $stmt1->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT * FROM rdvs WHERE admin_id = :id AND is_read = false";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $_SESSION['admin_id'], PDO::PARAM_INT);
$stmt->execute();
$notification2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlConfirmAgent = "SELECT * FROM agent_requests WHERE confirmed = false AND refused = false";
$stmtConfirmAgent = $pdo->prepare($sqlConfirmAgent);
$stmtConfirmAgent->execute();
$notificationConfirmAgent = $stmtConfirmAgent->fetchAll(PDO::FETCH_ASSOC);

$currentPage = basename($_SERVER['PHP_SELF']);

?>
 <style>
    .notif {
        border-radius: 100%;
        padding: 0 9px; 
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        margin-right: 5px; 
        width: 10px;
    }
    .nav-item.d-flex {
        justify-content: space-evenly;
        align-items: center;
    }
    h3 {
        font-family: 'Times New Roman', Times, sans-serif;
        font-size: 35px;
    }
    .itemmenu {
      margin-left: 50px;
      font-size: 1.1rem;
      font-family: system-ui !important;
    }
    .itemmenu::before {
  content: "";
  position: absolute;
  width: 8px;
  height: 8px;
  border-radius: 100%;
  background-color: #6b6e78 !important;
  left: 0;

}

    hr {
      width: 130px;
      margin: auto;
    }
    .imgl{
      margin-top: 25px;
      width: 200px;
      height: 200px;
    }
 </style>

<div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" data-background-color="dark">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" data-background-color="dark">
            <a href="dash_user.php" class="logo">
              <!-- <img src="./assets/img/F.png" alt="" class="imgl"> -->
              <h3 class="text-light">FindHome</h3>
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
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-item <?php if($currentPage == 'dash_user.php'){echo 'active';} ?>">
                <a href="dash_user.php">
                  <i class="fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item <?php if($currentPage == 'add_biens.php' || $currentPage == 'properties_views.php'){echo 'active';} ?>">
                <a  href="#submenuBiens" data-bs-toggle="collapse" aria-controls= "submenuBiens">
                  <i class="fas fa-cart-plus"></i>
                  <p>Gestion des biens</p>
                  <i class="fas fa-chevron-down arrow"></i> <!-- Flèche initiale -->
                  
                  
                </a>
                <ul class="collapse list-unstyled" id="submenuBiens">
                            <li class="nav-item <?php if($currentPage == 'add_biens.php'){echo 'active';} ?>">
                                <a class="itemmenu" href="add_biens.php">Ajouter un bien</a>
                            </li>
                            <li class="nav-item <?php if($currentPage == 'properties_views.php'){echo 'active';} ?>">
                                <a class="itemmenu" href="properties_views.php">
                                  Liste des biens
                                  
                                </a>
                            </li>
                </ul>
              </li>
              <li class="nav-item d-flex <?php if($currentPage == 'admin_confirm_agents.php'){echo 'active';} ?>">
                <a href="admin_confirm_agents.php">
                  <i class="fas fa-user-plus"></i>
                  <p>Gestion des agents</p>&nbsp;&nbsp;&nbsp;
                  <?php if(count($notificationConfirmAgent) > 0): ?>
                   <span class="badge badge-success"><?= count($notificationConfirmAgent) ?></span>
                  <?php endif; ?>
                </a>
              </li>
              <li class="nav-item d-flex <?php if($currentPage == 'gerer_rdv.php'){echo 'active';} ?>">
                <a href="gerer_rdv.php">
                <i class="fas fa-handshake"></i>
                  <p>Gestion rendez-vous</p>
                  <?php if(count($notification2)>0):?>
                  <span class="badge badge-success"><?= count($notification2) ?></span>
                  <?php endif; ?>
                </a>
              </li>
              <li class="nav-item">
                <a href="logout.php" onclick="confirmAction(event)">
                  <i class="fas fa-toggle-off"></i>
                  <p>Se déconnecter</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->


