let points = 0;

function submitQuiz() {
    // Réponses correctes
    const answers = {
        q1: "c",
        q2: "b",
        q3: "b",
        q4: "d",
        q5: "b",
        q6: "b",
        q7: "c",
        q8: "d",
        q9: "b",
        q10: "c",
        q11: "d",
        q12: "d",
        q13: "c",
        q14: "c",
        q15: "b",
        q16: "b",
        q17: "b",
        q18: "b",
        q19: "a",
        q20: "b"
    };

    // Initialiser le score
    let score = 0;

    // Récupérer le formulaire
    const form = document.getElementById("quiz-form");
    const resultDiv = document.getElementById("result");

    // Vérifier si toutes les questions ont été répondues
    let allAnswered = true;
    Object.keys(answers).forEach(question => {
        if (!form[question]?.value) {
            allAnswered = false; // Une question n'a pas été répondue
        }
    });

    // Si toutes les questions ne sont pas répondues, afficher une alerte
    if (!allAnswered) {
        alert("Veuillez répondre à toutes les questions avant de soumettre le formulaire.");
        return; // Arrêter l'exécution de la fonction
    }

    // Parcourir les réponses pour vérifier les réponses utilisateur
    Object.keys(answers).forEach(question => {
        const userAnswer = form[question]?.value;
        if (userAnswer === answers[question]) {
            score++; // Incrémenter le score pour chaque bonne réponse
            points += 10; // Ajouter 10 points par bonne réponse
        }
    });

    // Condition pour vérifier le score final
    if (score >= 12) {
        // Alerte en cas de réussite
        alert(`Félicitations ! Votre score : ${score}/20. Vous avez gagné ${points} points.`);
    } else {
        // Alerte en cas d'échec
        alert(`Votre score : ${score}/20. Malheureusement, vous n'avez pas atteint le score requis. Cependant, vous êtes inscrit au tirage au sort !`);
    }

    // Afficher le score dans la page
    resultDiv.textContent = `Votre score : ${score} / 20`;
}
