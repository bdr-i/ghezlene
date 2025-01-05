<?php
session_start();

// Inclure la connexion à la base de données
if (file_exists('../../config/dbConnect.php')) {
    require '../../config/dbConnect.php';
} else {
    echo json_encode(['status' => 'error', 'message' => 'Fichier de configuration introuvable']);
    exit;
}

$product = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

    if ($productId) {
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = mysqli_prepare($link, $query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $productId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la préparation de la requête']);
            exit;
        }
    } else {
        echo "<p>ID invalide</p>";
        exit;
    }
}


mysqli_close($link);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produit - Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .star {
            cursor: pointer;
            font-size: 24px;
            color: gray;
        }
        .star.selected {
            color: gold;
        }
        .profile-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .comment-wrapper {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .edit-comment, .delete-comment {
            margin-left: 15px;
            cursor: pointer;
            color: blue;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Cosmetics Shop</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../panier.php">Panier</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main class="container mt-5">
    <h2>Détails du produit</h2>
    <div id="productList" class="row">
        <?php if ($product): ?>
            <div class="col-md-4">
                <div class="card">
                    <img src="../../images/<?= htmlspecialchars($product['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="card-text">Prix: €<?= number_format($product['price'], 2) ?></p>
                        <p class="card-text">Quantité: <?= htmlspecialchars($product['stock']) ?></p>
                        <button class="btn btn-info" onclick='openProductModal(
                            <?= json_encode($product['name'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                            <?= json_encode($product['image_url'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>,
                            <?= json_encode($product['price']) ?>
                        )'>Voir Détails</button>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>Aucun produit trouvé.</p>
        <?php endif; ?>
    </div>
</main>

<footer class="bg-light text-center mt-5 py-3">
    Cosmetics Shop &copy; 2020
</footer>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body">
                <img id="modalProductImage" class="img-fluid" alt="">
                <p>
                    <span id="modalProductPrice"></span>
                    <span id="modalProductStock"></span>
                </p>
                <h6>Commentaires :</h6>
                <div id="commentsSection"></div>
                <textarea id="commentInput" class="form-control mb-2" rows="3" placeholder="Ajoutez un commentaire..."></textarea>
                <div id="starRating" class="mb-2">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <button class="btn btn-primary" id="addCommentButton" onclick=>Ajouter un commentaire</button>
                <p id="averageRating" class="mt-3"></p>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function fetchReviews(){
        const data = {
            product_id: <?= $product['id'] ?>, // Assurez-vous que 'product' est défini
        }; 
        
        // Envoi de la requête AJAX pour récupérer les commentaires
        $.ajax({
            url: "../api/products_reviews/fetch_reviews.php",
            method: "POST",
            data: data,
            dataType: "json", // Attendez une réponse JSON du serveur
            success: function (response) {
                if (response.status === "success") {

                    // Rechargez les commentaires depuis la réponse
                    const comments = response.comments || []; // Assurez-vous que 'comments' contient la liste des commentaires
                    const commentsSection = document.getElementById("commentsSection");

                    // Générer le contenu HTML à partir des commentaires récupérés
                    commentsSection.innerHTML = comments.map(comment => 
                        `<p>${comment.identifiant} : ${comment.comment} (${comment.rating} étoiles)</p>`
                    ).join('');
                    const averageRating = comments.length > 0 ? 
                                        (comments.reduce((sum, c) => sum + c.rating, 0) / comments.length).toFixed(1) : 
                                        'Aucune note';
                    document.getElementById("averageRating").innerText = `Note moyenne : ${averageRating} étoiles`;

                } else {
                    alert(response.message || "Une erreur s'est produite.");
                }
                    
            },
            error: function () {
                alert("Une erreur est survenue lors de la récuperation des commentaires.");
            },
        });

    }
document.addEventListener("DOMContentLoaded", function() {
    window.openProductModal = function(name, image, price) {
        document.getElementById("productModalLabel").innerText = name;
        document.getElementById("modalProductImage").src = `../../images/${image}`;
        document.getElementById("modalProductPrice").innerText = `Prix: €${parseFloat(price).toFixed(2)}`;
        document.getElementById("modalProductStock").innerText = `Quantité: <?= $product['stock'] ?>`;
        
        const data = {
            product_id: <?= $product['id'] ?>, // Assurez-vous que 'product' est défini
        }; 
        fetchReviews();
        $('#productModal').modal('show');
        document.getElementById("addCommentButton").onclick = function () {
            const commentText = document.getElementById("commentInput").value.trim();
            const selectedRating = Array.from(
                document.querySelectorAll("#starRating .star")
            ).filter((star) => star.classList.contains("selected")).length;

            if (!commentText) {
                alert("Veuillez saisir un commentaire");
                return;
            }

            if (!selectedRating) {
                alert("Veuillez sélectionner une note");
                return;
            }
            productId = <?= $product['id'] ?>;
            // Préparez les données à envoyer
            const data = {
                product_id: productId, // Assurez-vous que `productId` est défini
                comment: commentText,
                rating: selectedRating,
            };

            // Envoi de la requête AJAX
            $.ajax({
                url: "../api/products_reviews/add_comment.php",
                method: "POST",
                data: data,
                dataType: "json", // Attendez une réponse JSON du serveur
                success: function (response) {
                    if (response.status === "success") {
                        alert("Votre commentaire a été ajouté avec succès");
                        // Rechargez les commentaires
                        fetchReviews();
                        // Réinitialisez le formulaire
                        document.getElementById("commentInput").value = "";
                        document
                            .querySelectorAll("#starRating .star")
                            .forEach((star) => star.classList.remove("selected"));
                    } else {
                        alert(response.message || "Une erreur s'est produite.");
                    }
                },
                error: function () {
                    alert("Une erreur est survenue lors de l'envoi du commentaire.");
                },
            });
        };


    };

    document.querySelectorAll("#starRating .star").forEach(star => {
        star.addEventListener("click", function() {
            const ratingValue = this.getAttribute('data-value');
            document.querySelectorAll("#starRating .star").forEach((s, i) => {
                s.classList.toggle('selected', i < ratingValue);
            });
        });
    });
});

</script>
</body>
</html>
