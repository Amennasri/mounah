<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'mouneh'; // Remplacez par le nom de votre base de données
$username = 'root'; // Remplacez par votre utilisateur MySQL
$password = ''; // Remplacez par votre mot de passe MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction pour vérifier les produits
function verifierProduits($pdo)
{
    // Date actuelle
    $date_actuelle = date('Y-m-d');

    // Calcul de la date limite (2 jours avant expiration)
    $date_limite = date('Y-m-d', strtotime('+2 days', strtotime($date_actuelle)));

    // Requête pour récupérer les produits proches de leur date de fin
    $query = "SELECT id, nom, date_fin FROM produit WHERE date_fin = :date_limite";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':date_limite' => $date_limite]);

    // Vérification des résultats
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($produits)) {
        foreach ($produits as $produit) {
            echo "<p style='color: red; font-weight: bold;'>Alerte : Le produit '{$produit['nom']}' reste 2 jours avant sa date de consommation ({$produit['date_fin']}).</p>";
        }
    } else {
        echo "<p>Aucun produit proche de sa date de fin.</p>";
    }
}

// Appeler la fonction pour vérifier les produits
verifierProduits($pdo);
