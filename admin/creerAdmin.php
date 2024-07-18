<?php
require_once(__DIR__ . '/db.php');

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['adminname']));
    $email = htmlspecialchars(trim($_POST['email']));

    if (!empty($name) && !empty($email)) {
        // Vérifier si l'utilisateur existe déjà
        $sql = " SELECT * FROM user WHERE username = :name OR email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();
        
        $sql1 = "SELECT * FROM admin WHERE username = :name OR email = :email ";
        $stmt1 = $pdo->prepare($sql);
        $stmt1->bindParam(':name', $name);
        $stmt1->bindParam(':email', $email);
        $stmt1->execute();
        $admin = $stmt->fetch();


        if ($user || $admin) {
            $error = 'Ce nom d\'utilisateur ou cet email existe déjà';
        } else {
            // Validation de l'email
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $password = $_POST['password'];
                $confirmpassword = $_POST['confirmation'];

                // Vérifier la correspondance des mots de passe
                if ($password === $confirmpassword) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insertion des données dans la table admin
                    $sql = 'INSERT INTO admin (username, email, password) VALUES (:name, :email, :password)';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashed_password);

                    if ($stmt->execute()) {
                        header('Location: loginadmin.php?enregistrer=true');
                        exit;
                    } else {
                        $error = 'Erreur lors de l\'enregistrement';
                    }
                } else {
                    $error = 'Les mots de passe ne correspondent pas';
                }
            } else {
                $error = 'Vous devez entrer un email valide';
            }
        }
    } else {
        $error = "Vous devez remplir tous les champs";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Inscription</title>
    <style>
        .message {
            position: fixed;
            top: 50px;
            right: -300px;
            transition: right .9s ease-in-out;
            padding: 15px;
            border-radius: 13px;
            background: white;
            font-weight: 500;
            text-align: center;
            z-index: 1000;
        }

        .show {
            right: 15px;
        }

        .hide {
            right: -100%;
        }
    </style>
</head>
<body>
<section class="background-radial-gradient overflow-hidden">
    <style>
        body {
            font-family: system-ui;
        }

        .background-radial-gradient {
            background-color: hsl(218, 41%, 15%);
            background-image: radial-gradient(650px circle at 0% 0%, hsl(218, 41%, 35%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%),
                radial-gradient(1250px circle at 100% 100%, hsl(218, 41%, 45%) 15%, hsl(218, 41%, 30%) 35%, hsl(218, 41%, 20%) 75%, hsl(218, 41%, 19%) 80%, transparent 100%);
            height: 100%;
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
            max-width: 500px;
            margin: auto;
            color:#d5dcff;
        }

        .card a {
            color: #d5dcff;
        }
    </style>

    <?php if ($error): ?>
        <div class="message" id="message" style="color:red;"><?= $error ?></div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const message = document.getElementById('message');
            message.classList.add('show');
            setTimeout(() => {
                message.classList.remove('show');
                message.classList.add('hide');
            }, 1900);
        });
    </script>

    <div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
        <div class="row gx-lg-5 align-items-center mb-5">
            <div class="col-lg-6 mb-5 mb-lg-0" style="z-index: 10">
                <h1 class="my-5 display-5 fw-bold ls-tight" style="color: hsl(218, 81%, 95%)">
                    Devenez<br />
                    <span style="color: hsl(218, 81%, 75%)">Administrateur de la page</span>
                </h1>
                <p class="mb-4 opacity-70" style="color: hsl(218, 81%, 85%)">
                    En tant qu'administrateur de notre plateforme immobilière, vous avez un rôle essentiel dans la gestion et l'amélioration continue de notre service. Vos responsabilités incluent la gestion des utilisateurs et des propriétés, la fourniture d'un support client efficace, et la sécurité de la plateforme. Merci pour votre engagement à maintenir notre service à la hauteur des attentes des utilisateurs !
                </p>
            </div>

            <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
                <div id="radius-shape-1" class="position-absolute rounded-circle shadow-5-strong"></div>
                <div id="radius-shape-2" class="position-absolute shadow-5-strong"></div>

                <div class="card bg-glass">
                    <div class="card-body px-4 py-5 px-md-5">
                        <form action="" method="post">
                            <div class="row">
                                <div class="form-outline mb-4">
                                    <label class="form-label" for="adminname">Admin name</label>
                                    <input type="text" id="adminname" class="form-control" name="adminname" required placeholder="Entrer un nom d'utilisateur"/>
                                </div>
                            </div>

                            <div class="form-outline mb-4">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" class="form-control" name="email" placeholder="Entrer votre adresse email" required/>
                            </div>

                            <div class="form-outline mb-4">
                                <label class="form-label" for="password">Mot de passe</label>
                                <input type="password" id="password" class="form-control" name="password" placeholder="Entrer votre mot de passe" required/>
                            </div>

                            <div class="form-outline mb-4">
                                <label class="form-label" for="confirmation">Confirmer le mot de passe</label>
                                <input type="password" id="confirmation" class="form-control" name="confirmation" placeholder="Confirmer votre mot de passe" required />
                            </div>
                            <div class="d-flex mt-1">
                            <button type="submit" class="btn btn-primary btn-block mb-4">S'inscrire</button>
                            <a href="loginadmin.php" class="ms-auto m-2 ">Déjà enregistré ? S'authentifier</a>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>
