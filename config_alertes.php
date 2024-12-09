<?php
require 'include/database.php';
require 'include/functions.php';

// Fonction pour récupérer les produits proches de la date d'expiration
function verifierProduits($db)
{
    $date_actuelle = date('Y-m-d');
    $date_limite = date('Y-m-d', strtotime('+2 days', strtotime($date_actuelle)));

    $query = "SELECT id, nom, date_fin FROM produit WHERE date_fin BETWEEN :date_actuelle AND :date_limite";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':date_actuelle' => $date_actuelle,
        ':date_limite' => $date_limite,
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour afficher les alertes sous forme HTML
function afficherAlertes($produits)
{
    if (!empty($produits)) {
        foreach ($produits as $produit) {
            echo "<p style='color: red; font-weight: bold;'>
                    Alerte : Le produit '{$produit['nom']}' expire le {$produit['date_fin']}.
                  </p>";
        }
    } else {
        echo "<p>Aucun produit proche de sa date de fin.</p>";
    }
}

// Appel des fonctions
$alertes = verifierProduits($db);
