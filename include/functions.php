<?php
//require 'database.php';

function checkInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function registerUser($db, $nom_propriétaire, $adresse_restaurant, $téléphone, $nom_restaurant, $email, $password)
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (nom_propriétaire, adresse_restaurant, téléphone, nom_restaurant, email, password) VALUES (:nom_propriétaire, :adresse_restaurant, :téléphone, :nom_restaurant, :email, :password)");
    // Utilise la liaison de paramètres pour se protéger contre les attaques par injection SQL
    $stmt->bindParam(':nom_propriétaire', $nom_propriétaire);
    $stmt->bindParam(':adresse_restaurant', $adresse_restaurant);
    $stmt->bindParam(':téléphone', $téléphone);
    $stmt->bindParam(':nom_restaurant', $nom_restaurant);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hash);
    return $stmt->execute();
}

function authenticateUser($db, $email, $password)
{
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <!-- Dans footer.php -->
    <div id="chat-icon"></div>
    <div id="chatbot">
        <div class="chat-header">
            Chatbot <span id="close-chat">X</span>
        </div>
        <div class="chat-content"></div>
        <div class="user-input">
            <input type="text" id="user-question" placeholder="Posez votre question...">
            <button id="send-btn">Envoyer</button>
        </div>
    </div>
    <link rel="stylesheet" href="chatbot.css">
    <script src="chatbot.js"></script>
</body>

</html>