<?php
require "include/database.php";
require "include/functions.php";
session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: Connexion.php");
    exit();
}

// Traitement du formulaire de déconnexion
unset($_SESSION["id_user"]);
header("Location: Connexion.php"); // Redirection vers la page de connexion

?>
<!DOCTYPE html>
<html>

<head>
    <title>Menu</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <h1 class="text-logo"><span class="glyphicon glyphicon-cutlery"></span> <?php echo $restaurant_name; ?> <span class="glyphicon glyphicon-cutlery"></span></h1>
    <!--<div class="container admin">
        <div class="row">
            <h1><strong>Déconnexion</strong></h1>
            <br>
            <form class="form" action="deconnexion.php" role="form" method="">
                <p class="alert alert-warning">Etes vous sur de déconnecter ?</p>
                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">Oui</button>
                    <a class="btn btn-default" href="index.php">Non</a>
                </div>
            </form>
        </div>
    </div>-->
</body>

</html>