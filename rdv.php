<?php
session_start();

include 'admin/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

if(!isset($_GET['id']) && !isset($_GET['id_admin'])){
    header("Location: index.php");
    exit;
}

$propertie_id= isset($_GET['id'])?$_GET['id']:'';
$propertie_admin_id = isset($_GET['id_admin'])? $_GET['id_admin']:'';
$error = null;
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nom = $_POST['name'];
    $email = $_POST['email'];
    $telephone = $_POST['phone'];
    $message = $_POST['message'];

    if(empty($nom) || empty($email) || empty($telephone) || empty($message)){
        $error = 'Vous devez remplir tous les champs';
    }else{
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
            if(filter_var($telephone,FILTER_VALIDATE_INT)){
                $sql = "SELECT user_id FROM properties WHERE id=:id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id'=>$propertie_id]);
                $agent_id = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];

                $sql2 = "SELECT admin_id FROM properties_admin WHERE id=:id";
                $stmt2 = $pdo->prepare($sql2);
                $stmt2->execute([':id'=>$propertie_admin_id]);
                $admin_id = $stmt2->fetch(PDO::FETCH_ASSOC)['admin_id'];

                if($_GET['id']){
                    $sql1 = "INSERT INTO rdvs(nom,email,telephone,message,user_id,agent_id,propertie_id) VALUES(:nom,:email,:telephone,:message,:user_id,:agent_id,:propertie_id)";
                    $stmt1= $pdo-> prepare($sql1);
                    $result = $stmt1->execute([':nom'=>$nom,':email'=>$email,':telephone'=>$telephone,':message'=>$message,':user_id'=>$_SESSION['user_id'],':agent_id'=>$agent_id,':propertie_id'=>$_GET['id']]);
                    if($result){
                        header("Location: index.php");
                        exit;
                    }else{
                        $error = 'Une erreur est survenue';
                    }
                } else{
                    $sql3 = "INSERT INTO rdvs(nom,email,telephone,message,user_id,admin_id,propertie_admin_id) VALUES(:nom,:email,:telephone,:message,:user_id,:admin_id,:propertie_admin_id)";
                    $stmt3= $pdo-> prepare($sql3);
                    $result = $stmt3->execute([':nom'=>$nom,':email'=>$email,':telephone'=>$telephone,':message'=>$message,':user_id'=>$_SESSION['user_id'],':admin_id'=>$admin_id,':propertie_admin_id'=>$_GET['id_admin']]);
                    if($result){
                        header("Location: index.php");
                        exit;
                    }else{
                        $error = 'Une erreur est survenue';
                    }
                }


            }else{
                $error = "Entrer un numero de telephone valide";
            }
        }else{
            $error = 'L\'email n\'est pas valide';
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            width: 100%;
        }
        .contact-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .contact-header img {
            max-width: 200px;
            margin-bottom: 20px;
            border-radius: 10px;
        }
        .contact-header h2 {
            margin-bottom: 10px;
        }
        .contact-section, .text-section {
            padding: 20px;
        }
        .text-section img {
            max-width: 400px;
            max-height: 500px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?=  $error ?></div>
        <?php endif; ?>
        <div class="col-md-6 contact-section">
            <div class="contact-header">
                <img src="images/image2.jpg" alt="Contact Us">
                <h2>Prenez rendez-vous</h2>
            </div>
            <form action="" method="post">
                <div class="mb-3">
                    <label for="name" class="form-label">Nom complet</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Adresse email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Numéro de téléphone</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Envoyer</button>
            </form>
        </div>
        <div class="col-md-6 text-section">
            <img src="images/image1.webp" alt='jolie photo de contrat'>
            <h3>Interesse?</h3>
            <p>Prenez rendez-vous des maintenant avec un de nos agents pour trouver votre maison ideale.</p>
            <p><strong>Vous serez contacter ulterieurement par telephone ou messagerie whatsapp ou encore messagerie email</strong></p>
            <p>Notre équipe est dédiée à fournir un service exceptionnel. Nous sommes impatients de vous entendre et de vous offrir le meilleur support possible. Merci de votre confiance et à bientôt!</p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
