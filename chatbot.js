// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', function () {
    // Ouvrir le chatbot lorsque le logo est cliqué
    const chatIcon = document.getElementById('chat-icon');
    const chatbot = document.getElementById('chatbot');
    const closeChat = document.getElementById('close-chat');

    if (chatIcon) {
        chatIcon.addEventListener('click', function () {
            console.log("Logo cliqué"); // Débogage
            chatbot.style.display = 'block';
        });
    } else {
        console.error("L'élément #chat-icon est introuvable.");
    }

    // Fermer le chatbot lorsque le bouton de fermeture est cliqué
    if (closeChat) {
        closeChat.addEventListener('click', function () {
            console.log("Fermeture du chatbot"); // Débogage
            chatbot.style.display = 'none';
        });
    } else {
        console.error("L'élément #close-chat est introuvable.");
    }

    // Envoyer un message
    const sendBtn = document.getElementById('send-btn');
    if (sendBtn) {
        sendBtn.addEventListener('click', function () {
            const userQuestion = document.getElementById('user-question').value;
            const chatContent = document.querySelector('.chat-content');

            if (userQuestion.trim()) {
                // Ajouter le message de l'utilisateur
                const userMessage = document.createElement('div');
                userMessage.classList.add('user-message');
                userMessage.innerHTML = `<p><strong>Vous:</strong> ${userQuestion}</p>`;
                chatContent.appendChild(userMessage);

                // Ajouter une réponse fictive du bot
                const botMessage = document.createElement('div');
                botMessage.classList.add('bot-message');
                botMessage.innerHTML = `<p><strong>Bot:</strong> Merci pour votre question ! Je suis là pour vous aider.</p>`;
                chatContent.appendChild(botMessage);

                // Effacer l'entrée utilisateur
                document.getElementById('user-question').value = '';
                chatContent.scrollTop = chatContent.scrollHeight; // Faire défiler vers le bas
            } else {
                alert("Veuillez entrer une question.");
            }
        });
    } else {
        console.error("L'élément #send-btn est introuvable.");
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const chatIcon = document.getElementById('chat-icon');
    const chatbot = document.getElementById('chatbot');
    const closeChat = document.getElementById('close-chat');
    const sendBtn = document.getElementById('send-btn');
    const chatContent = document.querySelector('.chat-content');
    const userQuestion = document.getElementById('user-question');

    // Ouvrir le chatbot
    chatIcon.addEventListener('click', function () {
        chatbot.style.display = 'block';
    });

    // Fermer le chatbot
    closeChat.addEventListener('click', function () {
        chatbot.style.display = 'none';
    });

    // Envoyer un message
    sendBtn.addEventListener('click', function () {
        const question = userQuestion.value.trim();

        if (question) {
            // Ajouter le message de l'utilisateur
            const userMessage = document.createElement('div');
            userMessage.classList.add('user-message');
            userMessage.innerHTML = `<p><strong>Vous:</strong> ${question}</p>`;
            chatContent.appendChild(userMessage);

            // Ajouter une réponse fictive du bot
            const botMessage = document.createElement('div');
            botMessage.classList.add('bot-message');
            botMessage.innerHTML = `<p><strong>Bot:</strong> Merci pour votre message !</p>`;
            chatContent.appendChild(botMessage);

            // Effacer l'entrée utilisateur
            userQuestion.value = '';
            chatContent.scrollTop = chatContent.scrollHeight;
        }
    });
});
