<?php
require 'include/database.php';
require 'include/functions.php';
session_start();

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION["id_user"])) {
    header("Location: Connexion.php");
    exit();
}

// Récupération des IDs utilisateur et don
$id_user = $_SESSION["id_user"];
$receiver_id = $_GET['receiver_id'] ?? null;
$donation_id = $_GET['donation_id'] ?? null;

/*if (!$receiver_id || !$donation_id) {
    die("Utilisateur ou don non spécifié.");
}*/

// Récupération des messages entre les deux utilisateurs pour un don spécifique
$stmt = $db->prepare("
    SELECT * FROM messages 
    WHERE donation_id = :donation_id 
      AND ((sender_id = :id_user AND receiver_id = :receiver_id) 
        OR (sender_id = :receiver_id AND receiver_id = :id_user))
    ORDER BY created_at ASC
");
$stmt->execute([
    'donation_id' => $donation_id,
    'id_user' => $id_user,
    'receiver_id' => $receiver_id
]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Envoi d'un message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $message = htmlspecialchars($_POST['message']);
    $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, donation_id, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$id_user, $receiver_id, $donation_id, $message]);

    // Redirection pour éviter le ré-envoi du formulaire
    header("Location: discussion.php?receiver_id=$receiver_id&donation_id=$donation_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-box {
            height: 400px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .message {
            margin-bottom: 15px;
        }

        .message.me {
            text-align: right;
        }

        .message .content {
            display: inline-block;
            max-width: 70%;
            padding: 10px;
            border-radius: 12px;
        }

        .message.me .content {
            background: #007bff;
            color: white;
        }

        .message.other .content {
            background: #e9ecef;
            color: black;
        }

        .chat-footer textarea {
            resize: none;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center">Discussion</h1>
        <div class="chat-box mb-4">
            <?php foreach ($messages as $msg): ?>
                <div class="message <?= $msg['sender_id'] == $id_user ? 'me' : 'other' ?>">
                    <div class="content">
                        <?= htmlspecialchars($msg['message']) ?>
                    </div>
                    <small class="text-muted d-block mt-1">
                        <?= date('d/m/Y H:i', strtotime($msg['created_at'])) ?>
                    </small>
                </div>
            <?php endforeach; ?>
        </div>
        <form method="POST" action="discussion.php?receiver_id=<?= $receiver_id ?>&donation_id=<?= $donation_id ?>">
            <div class="chat-footer">
                <div class="mb-3">
                    <textarea class="form-control" name="message" rows="3" placeholder="Écrire un message..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer</button>
                <a href="accueil.php" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </div>
    <footer class="footer">
        © 2024 Mouneh - Partagez plus. Gaspillez moi!
    </footer>
</body>

</html>