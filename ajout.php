<?php
require 'include/database.php';
require 'include/functions.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["id_user"])) {
    header("Location: Connexion.php");
    exit();
}

$id_user = $_SESSION["id_user"];
$donation_id = $_GET['id'] ?? null;

// Initialisation des variables
$nomError = $descriptionError = $imageError = $locationError = $expiryDateError = "";
$nom = $description = $image = $expiry_date = $latitude = $longitude = "";
$isUpdate = false;

// Si un ID est passé, récupérer les données existantes pour mise à jour
if ($donation_id) {
    $isUpdate = true;
    $stmt = $db->prepare("SELECT nom, description, image, expiry_date, ST_X(location) AS latitude, ST_Y(location) AS longitude 
                          FROM donations WHERE id = ? AND user_id = ?");
    $stmt->execute([$donation_id, $id_user]);
    $donation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$donation) {
        header("Location: index.php");
        exit();
    }

    $nom = $donation['nom'];
    $description = $donation['description'];
    $image = $donation['image'];
    $expiry_date = $donation['expiry_date'];
    $latitude = $donation['latitude'];
    $longitude = $donation['longitude'];
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $expiry_date = htmlspecialchars($_POST['expiry_date']);
    $latitude = htmlspecialchars($_POST['latitude']);
    $longitude = htmlspecialchars($_POST['longitude']);
    $newimage = $_FILES["image"]["name"] ?? "";
    $imagePath = $newimage ? 'uploads/' . basename($newimage) : $image;
    $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);
    $isSuccess = true;
    $isUploadSuccess = false;

    // Validation des champs
    if (empty($nom)) {
        $nomError = 'Ce champ ne peut pas être vide.';
        $isSuccess = false;
    }
    if (empty($description)) {
        $descriptionError = 'Ce champ ne peut pas être vide.';
        $isSuccess = false;
    }
    if (empty($image) && !$newimage) {
        $imageError = 'Veuillez sélectionner une image.';
        $isSuccess = false;
    } else if ($newimage) {
        $isUploadSuccess = true;
        if (!in_array($imageExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $imageError = "Les fichiers autorisés sont : .jpg, .jpeg, .png, .gif.";
            $isUploadSuccess = false;
        }
        if ($_FILES["image"]["size"] > 500000) {
            $imageError = "La taille du fichier ne doit pas dépasser 500KB.";
            $isUploadSuccess = false;
        }
        if ($isUploadSuccess && !move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) {
            $imageError = "Erreur lors du téléversement de l'image.";
            $isUploadSuccess = false;
        }
    }

    if (empty($latitude) || empty($longitude) || !is_numeric($latitude) || !is_numeric($longitude)) {
        $locationError = 'Les coordonnées de localisation sont invalides ou absentes.';
        $isSuccess = false;
    }

    if ($isSuccess) {
        $location = "POINT($latitude $longitude)";
        if ($isUpdate) {
            $stmt = $db->prepare("UPDATE donations 
                                  SET nom = ?, description = ?, image = ?, expiry_date = ?, location = ST_GeomFromText(?) 
                                  WHERE id = ? AND user_id = ?");
            $stmt->execute([$nom, $description, $imagePath, $expiry_date, $location, $donation_id, $id_user]);
        } else {
            $stmt = $db->prepare("INSERT INTO donations (user_id, nom, description, image, expiry_date, location) 
                                  VALUES (?, ?, ?, ?, ?, ST_GeomFromText(?))");
            $stmt->execute([$id_user, $nom, $description, $imagePath, $expiry_date, $location]);
        }
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="/css/styles.css">

</head>

<body>
    <h3 class="text-logo text-center">
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-right: 10px;">
        Mouneh
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-left: 10px;">
    </h3>
    <div class="container mt-5">
        <h1><?= $isUpdate ? "Modifier le Don" : "Ajouter un Don" ?></h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nom" class="form-label">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>">
                <span class="text-danger"><?= $nomError ?></span>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description:</label>
                <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
                <span class="text-danger"><?= $descriptionError ?></span>
            </div>
            <div class="mb-3">
                <label for="expiry_date" class="form-label">Date d'expiration:</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" value="<?= htmlspecialchars($expiry_date) ?>">
                <span class="text-danger"><?= $expiryDateError ?></span>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">image:</label>
                <input type="file" class="form-control" id="image" name="image">
                <span class="text-danger"><?= $imageError ?></span>
            </div>
            <div class="mb-3">
                <button type="button" class="btn btn-info" id="getLocation">Obtenir ma localisation</button>
                <input type="hidden" id="latitude" name="latitude" value="<?= htmlspecialchars($latitude) ?>">
                <input type="hidden" id="longitude" name="longitude" value="<?= htmlspecialchars($longitude) ?>">
                <div id="coords-display" style="margin-top: 10px; display: none;">
                    <strong>Latitude :</strong> <span id="lat-display"></span><br>
                    <strong>Longitude :</strong> <span id="lng-display"></span>
                </div>
                <span class="text-danger"><?= $locationError ?></span>
            </div>
            <button type="submit" class="btn btn-primary"><?= $isUpdate ? "Modifier" : "Ajouter" ?></button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>

    <script>
        document.getElementById('getLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    document.getElementById('lat-display').textContent = position.coords.latitude;
                    document.getElementById('lng-display').textContent = position.coords.longitude;
                    document.getElementById('coords-display').style.display = 'block';
                    alert("Localisation obtenue !");
                }, function() {
                    alert("Impossible d'obtenir la localisation.");
                });
            } else {
                alert("La géolocalisation n'est pas prise en charge par votre navigateur.");
            }
        });
    </script>
    <footer class="footer">
        © 2024 Mouneh - Partagez plus. Gaspillez moi!
    </footer>
</body>

</html>