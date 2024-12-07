<?php
require 'include/database.php';
require "include/functions.php";

session_start();
$error_message = "";

if (isset($_POST["email"])) {
    $resultat = authenticateUser($db, $_POST["email"], $_POST["password"]);
    if ($resultat == true) {
        $_SESSION["id_user"] = $resultat["id"];
        header("Location: index.php");
        exit;
    } else {
        $error_message = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: url(../images/bg.png) no-repeat center center fixed;
            background-size: cover;
            font-family: 'Montserrat', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .text-logo {
            font-family: 'Holtwood One SC', sans-serif;
            color: #e7480f;
            text-shadow: 2px 2px #ffd301;
            font-size: 50px;
            text-align: center;
            margin-bottom: 20px;
        }

        .text-logo img {
            height: 70px;
            vertical-align: middle;
            margin: 0 10px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 100%;
        }

        .form-control {
            border-radius: 5px;
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="text-logo">
        <img src="logo.png" alt="Mouneh Logo"> Mouneh <img src="logo.png" alt="Mouneh Logo">
    </div>

    <div class="login-container">
        <h2 class="text-center mb-4">Connexion</h2>
        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Se connecter</button>
            <div class="text-center mt-3">
                <p>Vous n'avez pas de compte ? <a href="Inscription.php">Inscription</a></p>
                <a href="#">Mot de passe oublié ?</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>