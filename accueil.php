<?php
require 'include/database.php';
require 'include/functions.php';
session_start();

// Coordonnées de l'utilisateur (exemple: latitude et longitude dynamiques)
$user_lat = 36.8065; // Exemple : latitude de Tunis
$user_lng = 10.1815; // Exemple : longitude de Tunis

// Fonction pour calculer la distance
function calculateDistance($lat1, $lng1, $lat2, $lng2)
{
    $earthRadius = 6371; // Rayon de la Terre en km
    $latDistance = deg2rad($lat2 - $lat1);
    $lngDistance = deg2rad($lng2 - $lng1);
    $a = sin($latDistance / 2) * sin($latDistance / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($lngDistance / 2) * sin($lngDistance / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

// Récupérer les dons depuis la base de données
$stmt = $db->prepare('
    SELECT nom, description, image, expiry_date, created_at, 
           ST_X(location) AS latitude, ST_Y(location) AS longitude, donor_id,
           (6371 * ACOS(COS(RADIANS(:user_lat)) 
              * COS(RADIANS(ST_X(location))) 
              * COS(RADIANS(ST_Y(location)) - RADIANS(:user_lng)) 
              + SIN(RADIANS(:user_lat)) 
              * SIN(RADIANS(ST_X(location))))) AS distance
    FROM donations
    ORDER BY distance ASC
');
$stmt->execute(['user_lat' => $user_lat, 'user_lng' => $user_lng]);
$donations = $stmt->fetchAll();
?>

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
    <title>Accueil - Liste des Dons</title>
    <style>
        .donation-item {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .donation-item:hover {
            transform: scale(1.05);
        }

        .donation-item img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .don-card-body h5 {
            font-size: 18px;
            color: #333;
            margin-top: 10px;
        }

        .don-card-body p {
            font-size: 14px;
            color: #666;
        }

        /*.navbar {
            margin-bottom: 20px;
        }

        .navbar-nav>li>a {
            color: #333 !important;
            font-size: 16px;
        }

        .navbar-nav>.active>a {
            font-weight: bold;
            color: #e7480f !important;
        }*/

        .thumbnail {
            position: relative;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .thumbnail img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
        }

        .caption {
            padding: 15px;
            text-align: center;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .col-sm-6,
        .col-md-4 {
            flex: 1 1 calc(33.33% - 15px);
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
        <ul class="nav nav-pills">
            <li class="nav-item">
                <a class="nav-link active" href="accueil.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="index.php">Mes dons</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="avis_client.php">Avis client</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="quiz.html">Quiz</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="alertes.php">Alerte</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="astuces.php">Astuces</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="apropos.html">À propos de nous</a>
            </li>
        </ul>
    </nav>


    <div class="row">
        <?php foreach ($donations as $donation) : ?>
            <div class="col-sm-6 col-md-4">
                <div class="thumbnail">
                    <?php if (!empty($donation['image'])) : ?>
                        <img src="uploads/<?php echo htmlspecialchars($donation['image']); ?>" alt="Image du don" onerror="this.onerror=null; this.src='placeholder.jpg';">
                    <?php else : ?>
                        <img src="placeholder.jpg" alt="Image non disponible">
                    <?php endif; ?>
                    <div class="caption">
                        <h4><?php echo htmlspecialchars($donation['nom']); ?></h4>
                        <p><?php echo htmlspecialchars($donation['description']); ?></p>
                        <p><strong>Date d'expiration :</strong> <?php echo htmlspecialchars($donation['expiry_date']); ?></p>
                        <p><strong>Publiée le :</strong> <?php echo htmlspecialchars($donation['created_at']); ?></p>
                        <a href="discussion.php?receiver_id=<?php echo $donation['donor_id']; ?>" class="btn btn-primary btn-sm">Discuter avec le donneur</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!--feedback -->
    <div class="container">
        <h1 class="mt-5 mb-5"> </h1>
        <div class="card">
            <div class="card-header">feedback client</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-4 text-center">
                        <h1 class="text-warning mt-4 mb-4">
                            <b><span id="average_rating">0.0</span> / 5</b>
                        </h1>
                        <div class="mb-3">
                            <i class="fas fa-star star-light mr-1 main_star"></i>
                            <i class="fas fa-star star-light mr-1 main_star"></i>
                            <i class="fas fa-star star-light mr-1 main_star"></i>
                            <i class="fas fa-star star-light mr-1 main_star"></i>
                            <i class="fas fa-star star-light mr-1 main_star"></i>
                        </div>
                        <h3><span id="total_review">0</span> Note</h3>
                    </div>
                    <div class="col-sm-4">
                        <p>
                        <div class="progress-label-left"><b>5</b> <i class="fas fa-star text-warning"></i></div>

                        <div class="progress-label-right">(<span id="total_five_star_review">0</span>)</div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="five_star_progress"></div>
                        </div>
                        </p>
                        <p>
                        <div class="progress-label-left"><b>4</b> <i class="fas fa-star text-warning"></i></div>

                        <div class="progress-label-right">(<span id="total_four_star_review">0</span>)</div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="four_star_progress"></div>
                        </div>
                        </p>
                        <p>
                        <div class="progress-label-left"><b>3</b> <i class="fas fa-star text-warning"></i></div>

                        <div class="progress-label-right">(<span id="total_three_star_review">0</span>)</div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="three_star_progress"></div>
                        </div>
                        </p>
                        <p>
                        <div class="progress-label-left"><b>2</b> <i class="fas fa-star text-warning"></i></div>

                        <div class="progress-label-right">(<span id="total_two_star_review">0</span>)</div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="two_star_progress"></div>
                        </div>
                        </p>
                        <p>
                        <div class="progress-label-left"><b>1</b> <i class="fas fa-star text-warning"></i></div>

                        <div class="progress-label-right">(<span id="total_one_star_review">0</span>)</div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="one_star_progress"></div>
                        </div>
                        </p>
                    </div>
                    <div class="col-sm-4 text-center">
                        <h3 class="mt-4 mb-3">Écrivez votre avis ici</h3>
                        <button type="button" name="add_review" id="add_review" class="btn btn-primary">Avis</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-5" id="review_content"></div>
    </div>


    <div id="review_modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Envoyer le commentaire</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 class="text-center mt-2 mb-4">
                        <i class="fas fa-star star-light submit_star mr-1" id="submit_star_1" data-rating="1"></i>
                        <i class="fas fa-star star-light submit_star mr-1" id="submit_star_2" data-rating="2"></i>
                        <i class="fas fa-star star-light submit_star mr-1" id="submit_star_3" data-rating="3"></i>
                        <i class="fas fa-star star-light submit_star mr-1" id="submit_star_4" data-rating="4"></i>
                        <i class="fas fa-star star-light submit_star mr-1" id="submit_star_5" data-rating="5"></i>
                    </h4>
                    <div class="form-group">
                        <input type="text" name="user_name" id="user_name" class="form-control" placeholder="Entrez votre nom" />
                    </div>
                    <div class="form-group">
                        <textarea name="user_review" id="user_review" class="form-control" placeholder="Tapez commentaire ici"></textarea>
                    </div>
                    <div class="form-group text-center mt-4">
                        <button type="button" class="btn btn-primary" id="save_review">Envoyer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="deconnexion.php" class="btn btn-warning mt-4"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a>

    <footer class="footer">
        © 2024 Mouneh - Partagez plus. Gaspillez moi!
    </footer>
</body>
<script>
    $(document).ready(function() {

        var rating_data = 0;

        $('#add_review').click(function() {

            $('#review_modal').modal('show');

        });

        $(document).on('mouseenter', '.submit_star', function() {

            var rating = $(this).data('rating');

            reset_background();

            for (var count = 1; count <= rating; count++) {

                $('#submit_star_' + count).addClass('text-warning');

            }

        });

        function reset_background() {
            for (var count = 1; count <= 5; count++) {

                $('#submit_star_' + count).addClass('star-light');

                $('#submit_star_' + count).removeClass('text-warning');

            }
        }

        $(document).on('mouseleave', '.submit_star', function() {

            reset_background();

            for (var count = 1; count <= rating_data; count++) {

                $('#submit_star_' + count).removeClass('star-light');

                $('#submit_star_' + count).addClass('text-warning');
            }

        });

        $(document).on('click', '.submit_star', function() {

            rating_data = $(this).data('rating');

        });

        $('#save_review').click(function() {

            var user_name = $('#user_name').val();

            var user_review = $('#user_review').val();

            if (user_name == '' || user_review == '') {
                alert("Please Fill Both Field");
                return false;
            } else {
                $.ajax({
                    url: "submit_rating.php",
                    method: "POST",
                    data: {
                        rating_data: rating_data,
                        user_name: user_name,
                        user_review: user_review
                    },
                    success: function(data) {
                        $('#review_modal').modal('hide');

                        load_rating_data();

                        alert(data);
                    }
                })
            }

        });

        load_rating_data();

        function load_rating_data() {
            $.ajax({
                url: "submit_rating.php",
                method: "POST",
                data: {
                    action: 'load_data'
                },
                dataType: "JSON",
                success: function(data) {
                    $('#average_rating').text(data.average_rating);
                    $('#total_review').text(data.total_review);

                    var count_star = 0;

                    $('.main_star').each(function() {
                        count_star++;
                        if (Math.ceil(data.average_rating) >= count_star) {
                            $(this).addClass('text-warning');
                            $(this).addClass('star-light');
                        }
                    });

                    $('#total_five_star_review').text(data.five_star_review);

                    $('#total_four_star_review').text(data.four_star_review);

                    $('#total_three_star_review').text(data.three_star_review);

                    $('#total_two_star_review').text(data.two_star_review);

                    $('#total_one_star_review').text(data.one_star_review);

                    $('#five_star_progress').css('width', (data.five_star_review / data.total_review) * 100 + '%');

                    $('#four_star_progress').css('width', (data.four_star_review / data.total_review) * 100 + '%');

                    $('#three_star_progress').css('width', (data.three_star_review / data.total_review) * 100 + '%');

                    $('#two_star_progress').css('width', (data.two_star_review / data.total_review) * 100 + '%');

                    $('#one_star_progress').css('width', (data.one_star_review / data.total_review) * 100 + '%');

                    if (data.review_data.length > 0) {
                        var html = '';

                        for (var count = 0; count < data.review_data.length; count++) {
                            html += '<div class="row mb-3">';

                            html += '<div class="col-sm-1"><div class="rounded-circle bg-danger text-white pt-2 pb-2"><h3 class="text-center">' + data.review_data[count].user_name.charAt(0) + '</h3></div></div>';

                            html += '<div class="col-sm-11">';

                            html += '<div class="card">';

                            html += '<div class="card-header"><b>' + data.review_data[count].user_name + '</b></div>';

                            html += '<div class="card-body">';

                            for (var star = 1; star <= 5; star++) {
                                var class_name = '';

                                if (data.review_data[count].rating >= star) {
                                    class_name = 'text-warning';
                                } else {
                                    class_name = 'star-light';
                                }

                                html += '<i class="fas fa-star ' + class_name + ' mr-1"></i>';
                            }

                            html += '<br />';

                            html += data.review_data[count].user_review;

                            html += '</div>';

                            html += '<div class="card-footer text-right">On ' + data.review_data[count].datetime + '</div>';

                            html += '</div>';

                            html += '</div>';

                            html += '</div>';
                        }

                        $('#review_content').html(html);
                    }
                }
            })
        }

    });
</script>


</html>