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

// Fonction pour récupérer les produits qui expirent dans 2 jours
function verifierProduits($pdo)
{
    $date_actuelle = date('Y-m-d');
    $date_limite = date('Y-m-d', strtotime('+2 days'));

    $query = "SELECT * FROM produit WHERE date_fin BETWEEN :date_actuelle AND :date_limite";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':date_actuelle' => $date_actuelle,
        ':date_limite' => $date_limite
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupérer les produits avec alertes
$alertes = verifierProduits($pdo);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alertes Produits</title>
    <!-- Importation de SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>


    <?php if (!empty($alertes)): ?>
        <script>
            // Parcourir les produits et afficher une alerte SweetAlert pour chacun
            <?php foreach ($alertes as $produit): ?>
                Swal.fire({
                    icon: 'warning',
                    title: 'Alerte Expiration!',
                    text: "Le produit '<?= htmlspecialchars($produit['nom']); ?>' expire le <?= htmlspecialchars($produit['date_fin']); ?>.",
                    timer: 5000, // L'alerte disparaît après 5 secondes
                    timerProgressBar: true,
                    showConfirmButton: false
                });
            <?php endforeach; ?>
        </script>
    <?php else: ?>
        <p>Aucun produit n'approche de sa date d'expiration.</p>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>