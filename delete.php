<?php
require 'include/database.php';
require "include/functions.php";
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["id_user"])) {
    header("Location: Connexion.php");
    exit();
}

$id_user = $_SESSION["id_user"];

// Vérification de l'ID fourni
if (!isset($_GET["id"]) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = checkInput($_GET['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Suppression du don
        $statement = $db->prepare("DELETE FROM donations WHERE id = ? AND user_id = ?");
        $statement->execute([$id, $id_user]);
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo 'Erreur SQL : ' . $e->getMessage();
    }
}

// Récupérer les informations du don pour affichage
$stmt = $db->prepare("SELECT nom, description, photo FROM donations WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $id_user]);
$donation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donation) {
    header("Location: index.php");
    exit();
}
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
    <link rel="stylesheet" href="../css/styles.css">
</head>

<body>
    <h3 class="text-logo text-center">
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-right: 10px;">
        Mouneh
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-left: 10px;">
    </h3>
    <div class="container admin">
        <div class="row">
            <h1><strong>Supprimer un don</strong></h1>
            <br>
            <form class="form" action="delete.php?id=<?php echo $id; ?>" method="post">
                <input type="hidden" name="id" value="<?php echo $id; ?>" />
                <p class="alert alert-warning">Êtes-vous sûr de vouloir supprimer cet article ?</p>
                <div class="form-actions">
                    <button type="submit" class="btn btn-warning">Oui</button>
                    <a class="btn btn-default" href="index.php">Non</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>