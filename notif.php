<?php

	//Recuperer les notifications non lues

	$sql = "SELECT * FROM notifs WHERE is_read = false AND user_id=:id";
	$notif_nr = $pdo->prepare($sql);
    $notif_nr->execute(array(':id' => $_SESSION['user_id']));
	$notif_nr->execute();
	$notifs = $notif_nr->fetchAll(PDO::FETCH_ASSOC); 

	$sql = "SELECT * FROM rdvs WHERE is_read=false AND agent_id=:id";
	$rdv_nr = $pdo->prepare($sql);
    $rdv_nr->execute(array(':id' => $_SESSION['user_id']));
	$rdvs = $rdv_nr->fetchAll(PDO::FETCH_ASSOC); 



?>
		<style>
    .notification {
        position: relative; /* Pour positionner le badge par rapport à la notification */
        display: inline-block;
        padding: 1px 10px;
        border-radius: 50%; /* Pour créer une forme circulaire */
        background-color: #f8f9fa; /* Couleur de fond de la notification */
        box-shadow: 0 0 6px rgba(0, 0, 0, 0.1); /* Ombre légère */
    }

    .notification i {
        font-size: 1.4rem; /* Taille de l'icône */
        color: #6c757d; /* Couleur de l'icône */
    }

    .badge {
        position: absolute; /* Position absolue par rapport à la notification */
        top: -5px; /* Décalage vers le haut pour superposer sur la notification */
        right: -5px; /* Décalage vers la droite pour superposer sur la notification */
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px; /* Largeur du badge */
        height: 20px; /* Hauteur du badge */
        border-radius: 50%; /* Forme circulaire du badge */
        background-color: red; /* Couleur de fond du badge */
        color: white; /* Couleur du texte du badge */
        font-size: 0.8rem; /* Taille de police du badge */
        font-weight: bold; /* Police en gras pour le badge */
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.2); /* Légère ombre pour le badge */
    }
</style>
<div class="ml-4 notification">
    <?php if(count($notifs) > 0 || count( $rdvs) > 0): ?>
        <a href="notifications.php"><i class="bi bi-bell"></i></a>
        <span class="badge"><?= count($notifs) + count( $rdvs) ?></span> <!-- Affichage du nombre de notifications -->


    <?php else: ?>
        <a href="notifications.php"><i class="bi bi-bell"></i></a>
        <span class="badge badge-light text-dark" style="background-color:#CCCCCC">0</span>
    <?php endif; ?>
</div>

	