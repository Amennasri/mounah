<?php
session_start();
require 'include/database.php'; 
// Vérifiez si un utilisateur ou un client anonyme est connecté
if (!isset($_SESSION["id_user"])) {
	if (!isset($_SESSION["id_client"])) {
		$_SESSION["id_client"] = generateUniqueClientId(); // Générer un ID unique pour le client
	}
} else {
	// Si l'utilisateur est connecté, s'assurer que l'ID client est défini
	if (!isset($_SESSION["id_client"])) {
		$_SESSION["id_client"] = generateUniqueClientId();
	}
	$id_client = $_SESSION["id_client"] ?? null;
	
	if ($id_client === null) {
		echo json_encode(["status" => "error", "message" => "ID client non défini."]);
		exit;
	}
	
}

// Récupérez l'ID du restaurant à partir des paramètres GET
$id_restaurant = $_GET["id"] ?? null; // Utilisation de null coalescent pour éviter une erreur si non défini
if ($id_restaurant === null) {
	echo json_encode(["status" => "error", "message" => "ID restaurant manquant."]); // Message d'erreur clair
	exit; // Stopper l'exécution
}

$id_client = $_SESSION["id_client"]; // Récupérer l'ID client de la session

// Configurer le fuseau horaire
date_default_timezone_set('Africa/Tunis');
$current_time = date("Y-m-d H:i:s");

// Vérifiez si une évaluation a été soumise
if (isset($_POST["rating_data"])) {
	// Validation des données soumises
	$user_name = $_POST["user_name"] ?? null;
	$user_rating = $_POST["rating_data"] ?? null;
	$user_review = $_POST["user_review"] ?? null;

	if (empty($user_name) || empty($user_rating) || empty($user_review)) {
		echo json_encode(["status" => "error", "message" => "Tous les champs sont obligatoires."]);
		exit;
	}

	if (!is_numeric($user_rating) || $user_rating < 1 || $user_rating > 5) {
		echo json_encode(["status" => "error", "message" => "La note doit être entre 1 et 5."]);
		exit;
	}

	try {
		// Vérifiez si le client a déjà soumis un commentaire pour ce restaurant
		$stmt = $db->prepare("SELECT COUNT(*) FROM review WHERE id_restaurant = :id_restaurant AND id_client = :id_client");
		$stmt->bindParam(':id_restaurant', $id_restaurant);
		$stmt->bindParam(':id_client', $id_client);
		$stmt->execute();
		$count = $stmt->fetchColumn();

		if ($count > 0) {
			echo json_encode([ "Vous avez deja soumis un avis."]);
		} else {
			// Insérer un nouvel avis
			$stmt = $db->prepare("INSERT INTO review (user_name, user_rating, user_review, datetime, id_restaurant, id_client) VALUES (:user_name, :user_rating, :user_review, :datetime, :id_restaurant, :id_client)");
			$stmt->bindParam(':user_name', $user_name);
			$stmt->bindParam(':user_rating', $user_rating);
			$stmt->bindParam(':user_review', $user_review);
			$stmt->bindParam(':datetime', $current_time);
			$stmt->bindParam(':id_restaurant', $id_restaurant);
			$stmt->bindParam(':id_client', $id_client);
			$stmt->execute();

			echo json_encode(["status" => "success", "message" => "Votre avis a été soumis avec succès."]);
		}
	} catch (PDOException $e) {
		// Gestion des erreurs de base de données
		echo json_encode(["status" => "error", "message" => "Erreur de base de données : " . $e->getMessage()]);
	}
	exit;
}

// Traitement pour afficher les avis et les statistiques
if (isset($_POST["action"])) {
	try {
		// Calculer les statistiques d'avis avec des agrégations SQL pour réduire la charge
		$query = "
            SELECT 
                COUNT(*) as total_review,
                AVG(user_rating) as average_rating,
                SUM(CASE WHEN user_rating = 5 THEN 1 ELSE 0 END) as five_star_review,
                SUM(CASE WHEN user_rating = 4 THEN 1 ELSE 0 END) as four_star_review,
                SUM(CASE WHEN user_rating = 3 THEN 1 ELSE 0 END) as three_star_review,
                SUM(CASE WHEN user_rating = 2 THEN 1 ELSE 0 END) as two_star_review,
                SUM(CASE WHEN user_rating = 1 THEN 1 ELSE 0 END) as one_star_review
            FROM review
            WHERE id_restaurant = :id_restaurant
        ";
		$statement = $db->prepare($query);
		$statement->bindParam(':id_restaurant', $id_restaurant);
		$statement->execute();
		$statistics = $statement->fetch(PDO::FETCH_ASSOC);

		// Récupérer les avis individuels
		$review_query = "SELECT user_name, user_review, user_rating, datetime FROM review WHERE id_restaurant = :id_restaurant ORDER BY review_id DESC";
		$review_stmt = $db->prepare($review_query);
		$review_stmt->bindParam(':id_restaurant', $id_restaurant);
		$review_stmt->execute();
		$reviews = $review_stmt->fetchAll(PDO::FETCH_ASSOC);

		// Formater la date des avis
		foreach ($reviews as &$review) {
			$review["datetime"] = date("d/m/Y", strtotime($review["datetime"]));
		}

		// Construire la réponse JSON
		$output = [
			"status" => "success",
			"average_rating" => number_format($statistics["average_rating"], 1),
			"total_review" => $statistics["total_review"],
			"five_star_review" => $statistics["five_star_review"],
			"four_star_review" => $statistics["four_star_review"],
			"three_star_review" => $statistics["three_star_review"],
			"two_star_review" => $statistics["two_star_review"],
			"one_star_review" => $statistics["one_star_review"],
			"review_data" => $reviews
		];

		echo json_encode($output);
	} catch (PDOException $e) {
		echo json_encode(["status" => "error", "message" => "Erreur lors de la récupération des avis : " . $e->getMessage()]);
	}
	exit;
}

// Fonction pour générer un ID client unique
function generateUniqueClientId()
{
	return bin2hex(random_bytes(16)); // Génère un identifiant unique et sécurisé
}
