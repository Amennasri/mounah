<?php
require 'include/database.php';
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["id_user"])) {
  header("Location: Connexion.php");
  exit();
}

$id_user = $_SESSION["id_user"]; // ID de l'utilisateur connecté

// Gestion des actions (supprimer un don)
$action_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  $donation_id = $_POST['donation_id'] ?? null;

  if ($_POST['action'] === 'delete' && $donation_id) {
    $stmt = $db->prepare('DELETE FROM donations WHERE id = ? AND user_id = ?');
    if ($stmt->execute([$donation_id, $id_user])) {
      $action_message = "Don supprimé avec succès.";
    } else {
      $action_message = "Erreur lors de la suppression du don.";
    }
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
  <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="/css/styles.css">
  <title>Mes Dons</title>
  <style>
    body {
      background: #999;
      font-family: 'Montserrat', sans-serif;
    }

    .text-logo {
      font-size: 32px;
      font-family: 'Holtwood One SC', serif;
      text-align: center;
      color: #e7480f;
      margin-top: 20px;
    }

    .text-logo img {
      height: 50px;
      vertical-align: middle;
    }

    .table img {
      width: 100px;
      /* Ajuste la largeur des images */
      height: 100px;
      /* Assure une taille uniforme */
      object-fit: cover;
      /* Découpe l'image pour s'adapter aux dimensions */
      border-radius: 10px;
      /* Ajoute des bords arrondis */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
      /* Ajoute une ombre légère */
      border: 2px solid #ccc;
      /* Ajoute une bordure pour un meilleur contraste */
      transition: transform 0.3s ease;
      /* Ajoute un effet de zoom au survol */
    }

    .table img:hover {
      transform: scale(1.1);
      /* Effet de zoom lors du survol */
      box-shadow: 0 8px 10px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1);
      /* Renforce l'ombre au survol */
    }


    .btn-danger {
      margin-left: 10px;
    }

    .action-message {
      margin: 20px 0;
    }
  </style>
</head>

<body>
  <div class="container site">
    <div class="text-logo">
      <img src="logo.png" alt="Mouneh Logo"> Mouneh <img src="logo.png" alt="Mouneh Logo">
    </div>
    <nav>
      <ul class="nav nav-pills" role="tablist">
        <li class="nav-item" role="presentation">
          <a href="accueil.php">Accueil</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab11" role="tab">Mes dons</a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="avis_client.php">Avis client</a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="quiz.html">Quiz</a>
        </li>
        <li class="nav-item" role="presentation">
          <a href=".php">A propos de nous</a>
        </li>
      </ul>
    </nav>


    <h1 class="mt-4">Mes Dons</h1>

    <?php if (!empty($action_message)) : ?>
      <div class="alert alert-info action-message"><?php echo htmlspecialchars($action_message); ?></div>
    <?php endif; ?>

    <!-- Bouton pour ajouter un don -->
    <div class="text-end mb-3">
      <a href="ajout.php" class="btn btn-success"><i class="fas fa-plus"></i> Ajouter un Don</a>
    </div>

    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Nom</th>
          <th>Photo</th>
          <th>Description</th>
          <th>Date d'expiration</th>
          <th>Localisation</th>
          <th>Date de publication</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Récupérer les dons de l'utilisateur connecté
        $stmt = $db->prepare('
                    SELECT id, nom, description, photo, expiry_date, location, created_at
                    FROM donations 
                    WHERE user_id = ? 
                    ORDER BY created_at DESC
                ');
        $stmt->execute(array($_SESSION["id_user"] ?? ''));
        $donations = $stmt->fetchAll(); //Cette ligne récupère toutes les lignes renvoyées par la requête $stmt et les affecte à la variable $donations
        $cpt = 0;
        foreach ($donations as $donation) {
          $cpt++;
          if ($cpt == 1);

          echo '<tr>';

          echo '<td>' . $donation['nom'] . '</td>';
          echo '<td>';
          if (!empty($donation['photo'])) {
            // Afficher la photo si elle existe
            echo '<img src="uploads/' . htmlspecialchars($donation['photo']) . '" alt="Photo" style="width: 100px; height: auto; object-fit: cover; border-radius: 5px;">';
          } else {
            echo 'Pas de photo';
          }
          echo '</td>';

          echo '<td>' . $donation['description'] . '</td>';
          echo '<td>' . $donation['expiry_date'] . '</td>';
          echo '<td>' . $donation['location'] . '</td>';
          echo '<td>' . $donation['created_at'] . '</td>';

          echo '<td width=300>';
          echo '<a class="btn btn-danger" href="delete.php?id=' . $donation['id'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer</a>';
          echo ' ';
          echo '<a class="btn btn-primary" href="update.php?id=' . $donation['id'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>';
          echo '</td>';
          echo '</tr>';
        }
        ?>
      </tbody>
    </table>

    <a href="deconnexion.php" class="btn btn-warning mt-4"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>