<?php
require 'include/database.php'; // Connexion à la base de données
require 'include/functions.php'; // Fichier contenant des fonctions réutilisables

// Fonction pour récupérer les produits proches de la date d'expiration
function verifierProduits($db)
{
    $date_actuelle = date('Y-m-d');
    $date_limite = date('Y-m-d', strtotime('+2 days'));

    $query = "SELECT id, nom, date_fin FROM produit WHERE date_fin BETWEEN :date_actuelle AND :date_limite";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':date_actuelle' => $date_actuelle,
        ':date_limite' => $date_limite,
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les alertes
$alertes = verifierProduits($db);

// Gestion de l'insertion d'un nouveau produit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $date_fin = htmlspecialchars($_POST['date_fin']);

    if (!empty($nom) && !empty($date_fin)) {
        $stmt = $db->prepare("INSERT INTO produit (nom, date_fin) VALUES (?, ?)");
        $stmt->execute([$nom, $date_fin]);

        echo "<script>
            alert('Produit ajouté avec succès.');
            window.location.href = 'alertes.php';
        </script>";
    } else {
        echo "<script>
            alert('Veuillez remplir tous les champs.');
        </script>";
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
    <title>Alertes Produits</title>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            margin-top: 20px;
        }

        h1,
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #007bff;
        }

        .alert {
            text-align: center;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #007bff;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        .btn {
            margin: 5px;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            color: gray;
        }
    </style>
</head>

<body>
    <h3 class="text-logo text-center">
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-right: 10px;">
        Mouneh
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-left: 10px;">
    </h3>

    <nav>
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="accueil.php">Accueil</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="index.php">Mes dons</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="avis_client.php">Avis client</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="quiz.html">Quiz</a>
            </li>

            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab11" role="tab">Alerte</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="astuces.php">Astuces</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="quiz.html">A propos de nous</a>
            </li>
        </ul>
    </nav>
    <div class="container">
        <h1>Alertes Produits</h1>

        <form method="POST" class="mb-4">
            <div class="form-group">
                <label for="nom">Nom du produit :</label>
                <input type="text" id="nom" name="nom" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="date_fin">Date d'expiration :</label>
                <input type="date" id="date_fin" name="date_fin" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Ajouter un produit</button>
        </form>

        <h2>Produits enregistrés</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date d'expiration</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $db->query("SELECT nom, date_fin FROM produit ORDER BY date_fin ASC");
                $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($produits as $produit) : ?>
                    <tr>
                        <td><?= htmlspecialchars($produit['nom']); ?></td>
                        <td><?= htmlspecialchars($produit['date_fin']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($alertes)) : ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($alertes as $produit) : ?>
                        <li>⚠️ Le produit "<strong><?= htmlspecialchars($produit['nom']); ?></strong>" expire le <strong><?= htmlspecialchars($produit['date_fin']); ?></strong>.</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php else : ?>
            <div class="alert alert-success">
                Aucun produit proche de sa date d'expiration.
            </div>
        <?php endif; ?>
    </div>

    <footer class="footer">
        &copy; 2024 Mouneh - Prolongez la vie de vos produits.
    </footer>
</body>

</html>