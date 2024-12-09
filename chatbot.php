<?php
require 'include/database.php';
require 'include/functions.php';

$heure = date('H:i');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot </title>
    <link rel="stylesheet" href="chatbot.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <div id="chatbot">
        <div class="chat-header">
            <h3>Conseils </h3>
            <span id="close-chat" onclick="toggleChat()">X</span>
        </div>

        <div class="chat-content">
            <div id="chat-messages"></div>
            <div class="user-input">
                <input type="text" id="user-question" placeholder="Posez votre question..." />
                <button id="send-btn">Envoyer</button>
            </div>
        </div>
    </div>


    <div id="chat-icon" onclick="toggleChat()">💬</div>

    <script>
        function toggleChat() {
            var chat = document.getElementById('chatbot');
            chat.style.display = (chat.style.display === "none") ? "block" : "none";
        }

        $(document).ready(function() {
            $("#send-btn").click(function() {
                var question = $("#user-question").val().trim();
                if (question === "") {
                    alert("Veuillez poser une question !");
                    return;
                }

                var messageHtml = '<div class="user-message"><p>' + question + '</p></div>';
                $("#chat-messages").append(messageHtml);
                $("#user-question").val(""); // Réinitialiser l'input

                $.ajax({
                    url: "",
                    method: "POST",
                    data: {
                        question: question
                    },
                    success: function(response) {
                        var botHtml = '<div class="bot-message"><p>' + response + '</p></div>';
                        $("#chat-messages").append(botHtml);
                        $("#chat-messages").scrollTop($("#chat-messages")[0].scrollHeight);
                    }
                });
            });
        });
    </script>

    <?php

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['question'])) {
        $question = strtolower(trim($_POST['question'])); // Obtenez la question et convertissez en minuscules

        // Préparer la requête SQL pour trouver la réponse
        $stmt = $conn->prepare("SELECT reponse FROM chatbot WHERE question LIKE ?");
        $stmt->bind_param("s", $question);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifier si une réponse existe pour la question
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '<script>
                    $(document).ready(function(){
                        var botHtml = \'<div class="bot-message"><p>' . $row['reponse'] . '</p></div>\';
                        $("#chat-messages").append(botHtml);
                    });
                  </script>';
        } else {
            echo '<script>
                    $(document).ready(function(){
                        var botHtml = \'<div class="bot-message"><p>Désolé, je n\'ai pas compris votre question. Pouvez-vous reformuler ?</p></div>\';
                        $("#chat-messages").append(botHtml);
                    });
                  </script>';
        }

        $stmt->close();
    }

    $conn->close();
    ?>

</body>

</html>