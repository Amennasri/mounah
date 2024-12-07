<?php
require 'include/database.php';
require 'include/functions.php';

session_start();
if (!isset($_SESSION["id_user"])) {
    header("Location: Connexion.php");
    exit();
}

$id_user = $_SESSION["id_user"];
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit();
}

$nomError = $descriptionError = $photoError = "";
$nom = $description = $photo = "";

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars($_POST["nom"]);
    $description = htmlspecialchars($_POST["description"]);
    $newPhoto = $_FILES["photo"]["name"] ?? "";
    $photoPath = $newPhoto ? 'uploads/' . basename($newPhoto) : $photo;
    $photoExtension = pathinfo($photoPath, PATHINFO_EXTENSION);
    $isSuccess = true;

    if (empty($nom)) {
        $nomError = "Ce champ ne peut pas être vide.";
        $isSuccess = false;
    }
    if (empty($description)) {
        $descriptionError = "Ce champ ne peut pas être vide.";
        $isSuccess = false;
    }
    if ($newPhoto && !in_array($photoExtension, ["jpg", "jpeg", "png", "gif"])) {
        $photoError = "Les fichiers autorisés sont : .jpg, .jpeg, .png, .gif.";
        $isSuccess = false;
    }

    if ($isSuccess) {
        if ($newPhoto && move_uploaded_file($_FILES["photo"]["tmp_name"], $photoPath)) {
            $stmt = $db->prepare("UPDATE donations SET nom = ?, description = ?, photo = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$nom, $description, $photoPath, $id, $id_user]);
        } else {
            $stmt = $db->prepare("UPDATE donations SET nom = ?, description = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$nom, $description, $id, $id_user]);
        }
        header("Location: index.php");
        exit();
    }
} else {
    $stmt = $db->prepare("SELECT * FROM donations WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $id_user]);
    $donation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$donation) {
        header("Location: index.php");
        exit();
    }

    $nom = $donation["nom"];
    $description = $donation["description"];
    $photo = $donation["photo"];
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
    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/styles.css">
    <title>Modifier</title>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Modifier un don</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($nom) ?>">
                <span class="text-danger"><?= $nomError ?></span>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description"><?= htmlspecialchars($description) ?></textarea>
                <span class="text-danger"><?= $descriptionError ?></span>
            </div>

            <div class="form-group">
                <label for="photo">photo:</label>
                <input type="file" class="form-control" id="photo" name="photo">
                <?php if ($photo): ?>
                    <img src="<?= $photo ?>" alt="photo de l'donations" style="max-width: 150px; margin-top: 10px;">
                <?php endif; ?>
                <span class="text-danger"><?= $photoError ?></span>
            </div>
            <button type="submit" class="btn btn-success">Modifier</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</body>

</html>