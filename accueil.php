<?php
require 'include/database.php';
session_start();

// Coordonnées de l'utilisateur (exemple: latitude et longitude dynamiques)
$user_lat = 36.8065; // Exemple : latitude de Tunis
$user_lng = 10.1815; // Exemple : longitude de Tunis

// Fonction pour calculer la distance
function calculateDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6371; // Rayon de la Terre en km
    $latDistance = deg2rad($lat2 - $lat1);
    $lngDistance = deg2rad($lng2 - $lng1);
    $a = sin($latDistance / 2) * sin($latDistance / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($lngDistance / 2) * sin($lngDistance / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

// Récupérer les dons depuis la base de données
$stmt = $db->prepare('
    SELECT nom, description, photo, expiry_date, created_at, 
           ST_X(location) AS latitude, ST_Y(location) AS longitude, donor_id,
           (6371 * ACOS(COS(RADIANS(:user_lat)) 
              * COS(RADIANS(ST_X(location))) 
              * COS(RADIANS(ST_Y(location)) - RADIANS(:user_lng)) 
              + SIN(RADIANS(:user_lat)) 
              * SIN(RADIANS(ST_X(location))))) AS distance
    FROM donations
    ORDER BY distance ASC
');
$stmt->execute(['user_lat' => $user_lat, 'user_lng' => $user_lng]);

$donations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/styles.css">
    <title>Accueil - Liste des Dons</title>
    <style>
        /*body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }*/

        .don-card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .don-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .don-card-body {
            padding: 15px;
        }

        .don-card-body h5 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }

        .don-card-body p {
            font-size: 14px;
            margin: 0 0 10px;
            color: #666;
        }

        .distance {
            font-size: 14px;
            color: #999;
        }
    </style>
</head>

<body>
    <h3 class="text-logo text-center">
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-right: 10px;">
        Mouneh
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-left: 10px;">
    </h3>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="accueil.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Mes dons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="avis_client.php">Avis client</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="quiz.html">Quiz</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href=".php">À propos de nous</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!--<nav>
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab33" role="tab">Accueil</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="index.php">Mes dons</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="avis_client.php">Avis client</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href=".php">Quiz</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href=".php">A propos de nous</a>
            </li>

        </ul>
    </nav>-->

    <h1 class="text-center mt-4">Liste des Dons Disponibles</h1>

    <div class="container mt-5">
        <div class="row">
            <?php foreach ($donations as $donation) :
                // Vérification des coordonnées valides
                if (!is_numeric($donation['latitude']) || !is_numeric($donation['longitude'])) {
                    continue; // Ignorer les entrées invalides
                }

                // Calculer la distance entre l'utilisateur et le don
                $distance = calculateDistance(
                    $user_lat,
                    $user_lng,
                    $donation['latitude'],
                    $donation['longitude']
                );
            ?>
                <div class="col-md-4">
                    <div class="col-md-4">
                        <div class="don-card">
                            <?php if (!empty($donation['photo'])) : ?>
                                <img src="uploads/<?php echo htmlspecialchars($donation['photo']); ?>"
                                    onerror="this.src='placeholder.jpg';"
                                    alt="Photo du don" class="don-image">
                            <?php else : ?>
                                <img src="placeholder.jpg" alt="Pas de photo disponible" class="don-image">
                            <?php endif; ?>

                            <div class="don-card-body">
                                <h5><?php echo htmlspecialchars($donation['nom']); ?></h5>
                                <p class="distance"><?php echo round($distance, 2); ?> km</p>
                                <p><?php echo htmlspecialchars($donation['description']); ?></p>
                                <p><strong>Date d'expiration :</strong> <?php echo htmlspecialchars($donation['expiry_date']); ?></p>
                                <a href="discussion.php?receiver_id=<?php echo $donation['donor_id']; ?>"
                                    class="btn btn-primary btn-sm">Discuter avec le donneur</a>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
                </div>
        </div>

</body>

</html>