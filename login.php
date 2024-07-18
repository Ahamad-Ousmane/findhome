<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Connexion</title>
    <style>
        .message {
            position: fixed;
            top:50px;
            right: -300px;
            transition: right .9s ease-in-out;
            padding: 15px;
            border-radius: 13px;
            color: #4CAF50;
            background: white;
            font-weight: 500;
            text-align: center;
            z-index:1000;
        }
        .show{
            right: 15px;
        }

        .hide{
            right:-100%;
        }
    </style>
</head>
<body>
   <!-- Section: Design Block -->
    
<section class="background-radial-gradient overflow-hidden">
    <style>

      body ,html{
        height:100%;
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
  
      
      .card{
        max-width:500px;
        margin: auto;
        background: transparent;
        backdrop-filter: blur(20px);
        color:#d5dcff;
      }

      .card a{
        color: #d5dcff;
      }
    <?php
    $showMessage = false;
    $messageText = "";

    if (isset($_GET['enregistrer']) && $_GET['enregistrer'] == 'true') {
        $showMessage = true;
        $messageText = "Vous avez été enregistré avec succès";
    }

    if (isset($_GET['success']) && $_GET['success'] == 'true') {
        $showMessage = true;
        $messageText = "Mot de passe modifié avec succès";
    }
    ?>
     
    </style>
    <?php if($showMessage): ?>
        <div class="message" id="message"style="color: #4CAF50;"><?= $messageText ?></div>

        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                const message = document.getElementById('message');
                message.classList.add('show');
                setTimeout(() => {
                    message.classList.remove('show');
                    message.classList.add('hide');
                }, 1900); 
                const url = new URL(window.location);
                url.searchParams.delete('enregistrer');
                url.searchParams.delete('success');
                window.history.replaceState({}, document.title, url.toString());// Le message disparaîtra après 3 secondes
            });
        </script>
    <?php endif; ?>
    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
      <div class="row gx-lg-5 align-items-center mb-5">
        <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
          <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
            Connectez <br />
            <span style="color: hsl(218, 81%, 75%)">vous!</span>
          </h1>
          <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
          Bienvenue sur notre plateforme de gestion immobilière. Connectez-vous pour accéder à votre compte et découvrir nos dernières offres de maisons à louer, à vendre et d'autres propriétés exceptionnelles. Profitez de fonctionnalités personnalisées, consultez vos favoris et gérez vos visites. Pour toute question ou assistance, notre équipe est à votre écoute. Connectez-vous maintenant pour commencer votre parcours vers votre nouvelle maison de rêve.
          </p>
        </div>
  
        <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
          <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
          <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>
  
          <div class="card ">
            <div class="card-body px-4 py-5 px-md-5">
              <form action="authentificate.php" method="post">
                <!-- 2 column grid layout with text inputs for the first and last names -->
                <div class="row">
            
                    <div data-mdb-input-init class="form-outline">
                      <label class="form-label m-3" for="username" id="username">username</label>
                      <input type="text" class="form-control mb-3" name="username" placeholder="Entrer votre nom d'utilisateur" required />
                    
                  </div>
                </div>
                <!-- Password input -->
                <div data-mdb-input-init class="form-outline mb-4">
                  <label class="form-label m-3" for="passsword">Password</label>
                  <input type="password" class="form-control mb-4" name="password" placeholder="Entrer votre mot de passe" required />
                </div> 
                <div class="d-flex mt-4"><!-- Submit button -->
                <button type="submit" data-mdb-button-init data-mdb-ripple-init class="btn btn-primary btn-block mb-4 mt-3 me-auto">
                  Se connecter
                </button>
                
                    <a href="newpassword.php" class="btn btn-link ms-auto mt-3">Mot de passe oublie?</a>
                </div>

                  <a href="signup.php" class="d-flex btn btn-link justify-content-center">Pas encore enregistrer? Inscrivez-vous</a>
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