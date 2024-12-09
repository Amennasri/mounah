<?php
require 'include/database.php';
require 'include/functions.php';
session_start();

$nom = $gouvernorat = $telephone = $email = $password = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $gouvernorat = $_POST['gouvernorat'];
    $telephone = $_POST['telephone'] ?? "";
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Vérification si l'utilisateur existe déjà
        $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            echo "Cet email est déjà utilisé.";
        } else {
            // Insertion des nouvelles informations dans la base de données
            $stmt = $db->prepare("INSERT INTO users (nom, gouvernorat, telephone, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $gouvernorat, $telephone, $email, $password]);
            echo "Inscription réussie. Vous pouvez maintenant vous connecter.";
            header('Location: Connexion.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>S'inscrire</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<style>
    body {
        background: url(../images/bg.png);
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        font-family: 'Montserrat', sans-serif;
        height: 100vh;
        margin: -20px 0 50px;
    }

    .site {
        font-family: 'Holtwood One SC', sans-serif;
    }

    .text-logo {
        font-family: 'Holtwood One SC', sans-serif;
        color: #e7480f;
        text-shadow: 2px 2px #ffd301;
        font-size: 50px;
        padding: 40px 0px;
        text-align: center;
    }

    .text-logo .glyphicon {
        color: #ffd301;
        text-shadow: 0px 0px #ffd301;
    }

    h1 {
        font-weight: bold;
        margin: 0;
    }
</style>

<body>

    <h1 class="text-logo text-center">
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-right: 10px;">
        Mouneh
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-left: 10px;">
    </h1>
    <!-- <img src="logo.png" alt="Mouneh Logo" style="height: 160px; margin-left: 10px;">-->

    <div class="container">
        <div class="row mt-5">
            <div class="col-lg-4 bg-white m-auto rounded-top">
                <h2 class="text-center">Inscription</h2>

                <form method="POST" action="Inscription.php">
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-user"></i>
                        </span>
                        <input type="text" name="nom" class="form-control" placeholder="Nom complet" required />
                    </div>

                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fas fa-phone"></i>
                        </span>
                        <input type="tel" name="telephone" class="form-control" placeholder="Téléphone" required />
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-map-marker" aria-hidden="true"></i>
                        </span>
                        <input type="text" name="gouvernorat" class="form-control" placeholder="Gouvernorat" required />
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-envelope"></i>
                        </span>
                        <input type="email" name="email" class="form-control" placeholder="Email" required />
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">
                            <i class="fa fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="form-control" placeholder="Mot de passe" required />
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">S'inscrire</button>
                        <p class="text-center">
                            Avez-vous déjà un compte ? <a href="Connexion.php">Connexion</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmO-7-974C4d82ex76MtP_Eh_q5pXG0fA&callback=initMap" async defer></script>

</body>

</html>