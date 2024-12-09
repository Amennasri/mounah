<?php
require 'include/database.php';
require 'include/functions.php';

// Fonction pour vérifier les produits
function verifierProduits($db)
{
    // Date actuelle
    $date_actuelle = date('Y-m-d');

    // Calcul de la date limite (2 jours avant expiration)
    $date_limite = date('Y-m-d', strtotime('+2 days', strtotime($date_actuelle)));

    // Requête pour récupérer les produits proches de leur date de fin
    $query = "SELECT id, nom, date_fin FROM produit WHERE date_fin = :date_limite";
    $stmt = $db->prepare($query);
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
verifierProduits($db);
