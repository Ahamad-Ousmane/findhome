<?php
// Inclure le fichier de configuration de la base de données
require_once(__DIR__ . '/db.php');

$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier si les champs sont remplis
    $username = htmlentities(trim($_POST['username']));
    $email = htmlentities(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirmpassword = trim($_POST['confirmation']);

    // Vérifier si tous les champs sont remplis
    if (!empty($username) && !empty($email) && !empty($password) && !empty($confirmpassword)) {
        // Valider l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Vous devez entrer un email valide";
        } elseif ($password !== $confirmpassword) {
            $error = "Les mots de passe ne correspondent pas";
        } else {
            // Vérifier si le nom d'utilisateur ou l'email existe déjà
            $sql = " SELECT * FROM user WHERE username = :name OR email = :email";
            $stmt1 = $pdo->prepare($sql);
            $stmt1->bindParam(':name', $username);
            $stmt1->bindParam(':email', $email);
            $stmt1 ->execute();
            $result = $stmt1->fetch();
            $stmt1 = null;
            
            $sql2 = "SELECT * FROM admin WHERE username = :name OR email = :email ";
            $stmt2 = $pdo->prepare($sql2);
            $stmt2->bindParam(':name', $username);
            $stmt2->bindParam(':email', $email);
            $stmt2->execute();
            $admin = $stmt2->fetch();
            $stmt2 = null;
    

            if ($result || $admin) {
                $error = "Le nom d'utilisateur ou l'email existe déjà";
            } else {
                // Hacher le mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $profile = true;
                // Préparer la requête SQL d'insertion
                $sql = "INSERT INTO user (username, email, password, profile) VALUES (:username, :email, :password, :profile)";
                $stmt = $pdo->prepare($sql);

                // Lier les paramètres
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':profile', $profile);

                // Exécuter la requête
                if ($stmt->execute()) {
                    header("Location: dash_user.php?enregistrer=true");
                    exit;
                } else {
                    $error = "Erreur: Votre inscription a échoué.";
                }
            }
        }
    } else {
        $error = "Veuillez remplir tous les champs";
    }
}
?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Enrégistrer un agent</title>
</head>
<style>
   .message {
            position: fixed;
            top:50px;
            right: -300px;
            transition: right .9s ease-in-out;
            padding: 15px;
            border-radius: 13px;
            background: white;
            font-weight: 500;
            text-align: center;
            z-index:1000;
        }
        .show{
            right: 15px;
        }

        .hide{
          right: -100%;
        }
          
</style>
<body>
   <!-- Section: Design Block -->
<section class="background-radial-gradient overflow-hidden">
    <style>
      body{
        font-family: system-ui;
      }
      .background-radial-gradient {
        background-color: hsl(218, 41%, 15%);
        background-image: radial-gradient(650px circle at 0% 0%,
            hsl(218, 41%, 35%) 15%,
            hsl(218, 41%, 30%) 35%,
            hsl(218, 41%, 20%) 75%,
            hsl(218, 41%, 19%) 80%,
            transparent 100%),
          radial-gradient(1250px circle at 100% 100%,
            hsl(218, 41%, 45%) 15%,
            hsl(218, 41%, 30%) 35%,
            hsl(218, 41%, 20%) 75%,
            hsl(218, 41%, 19%) 80%,
            transparent 100%);
            height:100%;
      }
  
      #radius-shape-1 {
        height: 220px;
        width: 220px;
        top: -60px;
        left: -130px;
        background: radial-gradient(#44006b, #ad1fff);
        overflow: hidden;
      }
  
      #radius-shape-2 {
        border-radius: 38% 62% 63% 37% / 70% 33% 67% 30%;
        bottom: -60px;
        right: -110px;
        width: 300px;
        height: 300px;
        background: radial-gradient(#44006b, #ad1fff);
        overflow: hidden;
      }
  
      .bg-glass {
        background-color: hsla(0, 0%, 100%, 0.9) !important;
        backdrop-filter: saturate(200%) blur(25px);
      }
      .card {
        background: transparent !important;
        backdrop-filter: blur(15px);
        color: #d5dcff;
      }
      .card a {
        color: #d5dcff;
      }
    </style>
     <?php if($error): ?>
    <div class="message" id="message" style="color:red"><?= $error ?></div>
    <?php endif; ?>
    <script>
        document.addEventListener('DOMContentLoaded',(event) => {
            const message = document.getElementById('message');
            message.classList.add('show');
            setTimeout(()=>{
                message.classList.remove('show')
                message.classList.add('hide');
            },2000)
        })
    </script>
  
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
      <div class="row gx-lg-5 align-items-center mb-5">
        <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
          <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
            La meilleure offre <br />
            <span style="color: hsl(218, 81%, 75%)">pour vous </span>
          </h1>
          <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
          Rejoignez notre communauté et commencez votre recherche de la maison parfaite dès aujourd'hui ! Remplissez le formulaire ci-dessous pour créer votre compte et accéder à notre sélection exclusive de propriétés. Profitez de fonctionnalités avancées telles que la sauvegarde de vos recherches, la gestion des alertes personnalisées et bien plus encore. Nous sommes là pour vous accompagner à chaque étape de votre parcours immobilier. Inscrivez-vous maintenant et commencez à explorer vos options.
          </p>
        </div>
  
        <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
          <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
          <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>
  
          <div class="card bg-glass">
            <div class="card-body px-4 py-5 px-md-5">
              <form action="" method="post">
                <!-- 2 column grid layout with text inputs for the first and last names -->
                <div class="row">
                  
                    <div data-mdb-input-init class="form-outline mb-4">
                      <label class="form-label" for="form3Example2">Nom utilisateur</label>
                      <input type="text" id="form3Example2" class="form-control" name="username" required placeholder="Entrer un nom d'utilisateur"/>
                    </div>
                </div>
  
                <!-- Email input -->
                <div data-mdb-input-init class="form-outline mb-4">
                  <label class="form-label" for="form3Example3">Email</label>
                  <input type="email" id="form3Example3" class="form-control" name="email"  placeholder="Entrer votre address email "required/>
                </div>
  
                <!-- Password input -->
                <div data-mdb-input-init class="form-outline mb-4">
                  <label class="form-label" for="form3Example4">Mot de passe</label>
                  <input type="password" id="form3Example4" class="form-control" name="password" placeholder="Entrer votre mot de passe" required/>
                </div>
                <!-- confirmation input -->
                <div data-mdb-input-init class="form-outline mb-4">
                  <label class="form-label" for="form3Example5">Confirmer le mot de passe</label>
                  <input type="password" id="form3Example4" class="form-control" name="confirmation" placeholder="Confirmer votre mot de passe" required />
                </div>
                <!-- Submit button -->
                 <div class="d-flex">
                 <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4">
                  Enregistrer
                </button>
                 </div>
              
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Section: Design Block -->
</body>
</html>