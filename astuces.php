<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/css/styles.css">
    <title>Sensibilisation aux Bonnes Pratiques</title>
    <style>
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 30px;
        }

        .section-title {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
        }

        .tips-list {
            list-style-type: none;
            padding-left: 0;
        }

        .tips-list li {
            background-color: #ffffff;
            margin-bottom: 10px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <h3 class="text-logo text-center">
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-right: 10px;">
        Mouneh
        <img src="logo.png" alt="Mouneh Logo" style="height: 100px; margin-left: 10px;">
    </h3>
    <nav>
        <ul class="nav nav-pills" role="tablist">
            <li class="nav-item" role="presentation">
                <a href="accueil.php">Accueil</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="index.php">Mes dons</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="avis_client.php">Avis client</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="quiz.html">Quiz</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="alertes.php">Alerte</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab11" role="tab">Astuces</a>
            </li>
            <li class="nav-item" role="presentation">
                <a href="index.html">A propos de nous</a>
            </li>
        </ul>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Sensibilisation aux Bonnes Pratiques</h1>

        <!-- Conseils de conservation -->
        <div class="mb-5">
            <h2 class="section-title">Conseils de Conservation</h2>
            <ul class="tips-list">
                <li>
                    <h4>Conserver les Fruits</h4>
                    <p>Évitez de mélanger les fruits qui dégagent de l'éthylène (comme les pommes) avec d'autres fruits pour prolonger leur fraîcheur.</p>
                </li>
                <li>
                    <h4>Congeler les Restes</h4>
                    <p>Emballez hermétiquement vos restes pour éviter les brûlures de congélation. Indiquez une date sur les emballages pour une meilleure gestion.</p>
                </li>
                <li>
                    <h4>Ranger les Légumes</h4>
                    <p>Utilisez des torchons pour absorber l'humidité dans les bacs à légumes afin de conserver vos légumes plus longtemps.</p>
                </li>
                <li>
                    <h4>Stocker les Herbes Fraîches</h4>
                    <p>Placez les herbes fraîches dans un verre d'eau ou enveloppez-les dans un essuie-tout humide pour prolonger leur durée de vie.</p>
                </li>
                <li>
                    <h4>Réutiliser les Bocaux</h4>
                    <p>Utilisez les bocaux en verre pour stocker des aliments secs comme le riz, les pâtes ou les céréales. Cela préserve leur fraîcheur.</p>
                </li>
            </ul>
        </div>

        <!-- Recettes anti-gaspillage -->
        <div>
            <h2 class="section-title">Recettes Anti-Gaspillage</h2>
            <ul class="tips-list">
                <li>
                    <h4>Pain Rassis - Pain Perdu</h4>
                    <p>Trempez les tranches de pain rassis dans un mélange d'œufs, de lait, et de sucre, puis faites-les dorer à la poêle pour un délicieux dessert.</p>
                </li>
                <li>
                    <h4>Légumes Fanés - Soupe</h4>
                    <p>Coupez vos légumes fanés en morceaux et faites-les mijoter avec de l'eau, du bouillon, et des épices pour une soupe maison.</p>
                </li>
                <li>
                    <h4>Écorces d'Agrumes</h4>
                    <p>Utilisez les écorces d'orange ou de citron pour faire des zestes confits ou parfumer vos desserts et boissons.</p>
                </li>
                <li>
                    <h4>Chips de Légumes</h4>
                    <p>Préparez des chips croustillantes avec les épluchures de carottes ou de pommes de terre en les passant au four avec un filet d'huile d'olive.</p>
                </li>
                <li>
                    <h4>Fromages Restants - Gratins</h4>
                    <p>Mélangez vos restes de fromage pour préparer un gratin ou une sauce onctueuse pour accompagner vos plats.</p>
                </li>
            </ul>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <footer class="footer">
        © 2024 Mouneh - Partagez plus. Gaspillez moi!
    </footer>
</body>

</html>