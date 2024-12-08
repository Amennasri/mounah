<?php
require '../include/base_de_donnée.php';
require "../include/functions.php";
session_start();
if (!isset($_SESSION["id_user"])) {
  header("Location: Connexion.php");
  exit();
} else {
  $id_user = $_SESSION["id_user"]; // Récupère l'ID utilisateur de la session
}

// Requête pour récupérer le restaurant associé à l'utilisateur connecté
$query = "SELECT * FROM utilisateur WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_user]);
$restaurant = $stmt->fetch();

// Vérifie si un restaurant a été trouvé
if ($restaurant) {
  $restaurant_name = $restaurant['nom_restaurant'];
} else {
  // Gère le cas où aucun restaurant n'est trouvé pour l'utilisateur
  echo "Aucun restaurant trouvé pour l'utilisateur actuel.";
  exit();
}

// Récupérer les informations du restaurateur connecté
$query = "SELECT nom_restaurant, subscription_start_date, subscription_end_date, is_active FROM utilisateur WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id_user);  // Utilisation de la variable $id_user définie
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Si l'utilisateur n'est pas trouvé (ce qui ne devrait pas arriver)
if (!$user) {
  echo "Erreur : utilisateur non trouvé.";
  exit();
}

if (!$id_user) {
  die("Veuillez vous connecter.");
} else {
  // Récupérer les détails du compte du restaurateur
  $requete = $db->prepare('
    SELECT is_active
    FROM utilisateur
    WHERE id = ?');
  $requete->execute(array($id_user));

  // Vérifier si le restaurateur existe et récupérer son statut 'is_active'
  $restaurant = $requete->fetch();

  if (!$restaurant) {
    die("Restaurant non trouvé.");
  }

  // Vérifier si le compte est actif ou inactif
  if ($restaurant['is_active'] == 0) {
    echo "Votre abonnement n'est pas actif. Veuillez contacter Amen pour renouveler votre abonnement.<br> Contact: +21690191637 / +21650150311";
    exit();
  }
}

if (!empty($_GET['id'])) {
  $id = checkInput($_GET['id']);
}

$statement = $db->prepare("SELECT articles.id, articles.nom, articles.description, articles.prix, articles.image, catégorie.nom AS category FROM articles LEFT JOIN catégorie ON articles.catégorie = catégorie.id WHERE articles.id = ?");
$statement->execute(array($id));
$articles = $statement->fetch();


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
  <h1 class="text-logo"><span class="glyphicon glyphicon-cutlery"></span> <?php echo $restaurant_name; ?> <span class="glyphicon glyphicon-cutlery"></span></h1>
  <div class="container admin">
    <div class="row">
      <div class="col-sm-6">
        <h1><strong>Voir un article </strong></h1>
        <br>
        <form>
          <div class="form-group">
            <label>Nom:</label><?php echo '  ' . $articles['nom']; ?>
          </div>
          <div class="form-group">
            <label>Description:</label><?php echo '  ' . $articles['description']; ?>
          </div>
          <div class="form-group">
            <label>Prix:</label><?php echo '  ' . number_format((float)$articles['prix'], 2, '.', '') . ' DT'; ?>
          </div>
          <div class="form-group">
            <label>Catégorie:</label><?php echo '  ' . $articles['category']; ?>
          </div>
          <div class="form-group">
            <label>Image:</label><?php echo '  ' . $articles['image']; ?>
          </div>
        </form>
        <br>
        <div class="form-actions">
          <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
        </div>
      </div>
      <div class="col-sm-6 site">
        <div class="thumbnail">
          <img src="<?php echo '../images/' . $articles['image']; ?>" alt="...">
          <div class="prix"><?php echo number_format((float)$articles['prix'], 2, '.', '') . ' DT'; ?></div>
          <div class="caption">
            <h4><?php echo $articles['nom']; ?></h4>
            <p><?php echo $articles['description']; ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>