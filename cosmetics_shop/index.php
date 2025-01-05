<?php
    session_start();

    // Connexion à la base de données
    if (file_exists('../config/dbConnect.php')) {
        require '../config/dbConnect.php';
    } else {
        die("Fichier de configuration introuvable");
    }

    // Vérifier si le panier existe dans la session
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Charger les produits et les catégories depuis la base de données
    $produits = [];
    $categories = [];

    // Requête pour récupérer les produits
    $queryProduits = "SELECT * FROM products";
    if ($resultProduits = mysqli_query($link, $queryProduits)) {
        while ($row = mysqli_fetch_assoc($resultProduits)) {
            $produits[] = $row;
        }
    } else {
        die("Erreur lors de la récupération des produits.");
    }

    // Requête pour récupérer les catégories avec leurs sous-catégories
    $queryCategories = "
        SELECT c.id AS category_id, c.category_name, s.id AS subcategory_id, s.subcategory_name
        FROM categories c
        LEFT JOIN subcategories s ON c.id = s.category_id
        ORDER BY c.category_name, s.subcategory_name
    ";

    if ($resultCategories = mysqli_query($link, $queryCategories)) {
        while ($row = mysqli_fetch_assoc($resultCategories)) {
            $categoryName = $row['category_name'];
            $subcategoryName = $row['subcategory_name'];
            
            // Ajouter la catégorie au tableau si elle n'existe pas encore
            if (!isset($categories[$categoryName])) {
                $categories[$categoryName] = [];
            }

            // Ajouter la sous-catégorie si elle existe
            if ($subcategoryName) {
                $categories[$categoryName][] = $subcategoryName;
            }
        }
    } else {
        die("Erreur lors de la récupération des catégories.");
    }
    
    // Fermer la connexion
    mysqli_close($link);

    // Vérifier si l'utilisateur est connecté
    $loggedIn = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cosmetics Shop</title>
        
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
        <link rel="stylesheet" href="index_style.css?v=1.0">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
    <header id="top">
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container">
                <!-- Logo et marque -->
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="../images/logo.png" alt="Logo" width="50" class="mr-2">
                    Cosmetics Shop
                </a>
                <!-- Bouton pour mobile -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Basculer la navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <!-- Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <!-- Liens principaux -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="faq.php">FAQ</a>
                        </li>
                        <?php if ($loggedIn): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="profile.php">Profile</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="panier.php">
                                <i class="fas fa-shopping-cart"></i> Panier
                            </a>
                        </li>
                        <? if ($loggedIn): ?> 
                            <li class="nav-item">
                                <a class="nav-link" href="products/order_user/order.php">Commande</a>
                            </li>
                        <? endif; ?>
                        <!-- Menu déroulant Produits -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Produits
                            </a>
                            <div class="dropdown-menu" aria-labelledby="productsDropdown">
                                <!-- Toutes les catégories -->
                                <a class="dropdown-item filter" href="#" data-category="all">Toutes les catégories</a>
                                <div class="dropdown-divider"></div>
                                <!-- Catégories dynamiques -->
                                <?php foreach ($categories as $categoryName => $subcategories): ?>
                                    <h6 class="dropdown-header">
                                        <?= htmlspecialchars($categoryName) ?>
                                    </h6>
                                    <?php foreach ($subcategories as $subcategoryName): ?>
                                        <a class="dropdown-item filter" href="#" data-category="<?= htmlspecialchars($subcategoryName) ?>">
                                            <?= htmlspecialchars($subcategoryName) ?>
                                        </a>
                                    <?php endforeach; ?>
                                    <div class="dropdown-divider"></div>
                                <?php endforeach; ?>
                            </div>
                        </li>
                    </ul>
                    <!-- Barre de recherche -->
                    <form class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher">
                    </form>
                    <!-- Connexion/Déconnexion -->
                    <?php if ($loggedIn): ?>
                        <a href="users/logout.php" class="btn btn-outline-danger ml-3">Se déconnecter</a>
                    <?php else: ?>
                        <a href="users/login.php" class="btn btn-outline-primary ml-3">Se connecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>

    </header>

    <main>
        <section class="s_cover text-center">
            <h1>Beauté sans frontières</h1>
            <p>Exprimez votre éclat unique</p>
            <a href="#produits" class="btn btn-primary btn-lg">Découvrir plus</a>
        </section>
        <section id="produits" class="s_dynamic_snippet_products pt-5 pb-5">
            <div class="container">
                <h4>Nos produits phares</h4>
                <div id="product-container" class="row">
                </div>
            </div>
        </section>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Initialisation
            fetchProducts();
            handleAddToCartClick();
            initDropdownMenu();

            // Fonction pour récupérer tous les produits
            function fetchProducts() {
                $.ajax({
                    url: 'api/products/getAllProducts.php',
                    type: 'GET',
                    success: function (response) {
                        try {
                            const res = JSON.parse(response);
                            updateProducts(res);
                        } catch (error) {
                            alert('Erreur lors de la récupération des produits.');
                            console.error(error);
                        }
                    },
                    error: function () {
                        alert('Erreur lors de la requête au serveur.');
                    }
                });
            }

            // Fonction pour gérer les clics sur les boutons "Ajouter au panier"
            function handleAddToCartClick() {
                $('.add-to-cart').off('click').on('click', function () {
                    const productId = $(this).data('product-id');
                    const quantity = $(this).data('quantity');
                    const loggedIn = <?php echo $loggedIn ? 'true' : 'false'; ?>;

                    if (!loggedIn) {
                        window.location.href = 'users/login.php';
                        return;
                    }

                    $.ajax({
                        url: 'api/carts/addToCart.php',
                        type: 'POST',
                        data: { product_id: productId, quantity: quantity },
                        success: function (response) {
                            try {
                                const res = JSON.parse(response);
                                alert(res.status === 'success' ? 'Produit ajouté au panier' : 'Erreur : ' + res.message);
                            } catch (error) {
                                alert('Erreur lors de la mise à jour du panier.');
                                console.error(error);
                            }
                        },
                        error: function () {
                            alert('Erreur lors de la requête au serveur.');
                        }
                    });
                });
            }

            function updateProducts(products) {
                const productContainer = $('.row');
                productContainer.empty();

                products.forEach(product => {
                    const productCard = `
                        <div class="col-md-4 product-item" data-category="${product.subcategory}">
                            <div class="card">
                                <img src="../images/${product.image_url}" class="card-img-top" alt="${product.name}">
                                <div class="card-body">
                                    <h5 class="card-title product-name">${product.name}</h5>
                                    <p class="card-text">${product.description}</p>
                                    <p class="card-text">Prix : ${product.price} €</p>
                                    <p class="card-text">Stock : ${product.stock}</p>
                                    <div id="starRating-${product.id}" class="mb-2"></div>
                                    <button 
                                        class="add-to-cart btn btn-primary" 
                                        data-product-id="${product.id}" 
                                        data-quantity="${product.stock}">
                                        Ajouter au panier
                                    </button>
                                    <a href="products/details.php?id=${product.id}" class="btn btn-link">Voir plus</a>

                                </div>
                            </div>
                        </div>`;
                    productContainer.append(productCard);
                });

                // Réinitialiser les événements pour la recherche et les boutons
                initSearchFunctionality();
                handleAddToCartClick();
                updateProductStars(); // Appeler la mise à jour des étoiles après le rendu des produits
            }

            // Fonction pour mettre à jour les étoiles des produits
            function updateProductStars() {
                $.ajax({
                    url: 'api/products_reviews/getAllReviews.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            const reviews = response.comments;
                            const productRatings = {};

                            reviews.forEach(review => {
                                const productId = review.product_id;
                                const rating = parseFloat(review.rating);

                                if (!productRatings[productId]) {
                                    productRatings[productId] = { total: 0, count: 0 };
                                }

                                productRatings[productId].total += rating;
                                productRatings[productId].count++;
                            });

                            $('.product-item').each(function () {
                                const productId = $(this).find('.add-to-cart').data('product-id');
                                const starContainer = $(`#starRating-${productId}`);
                                if (productRatings[productId]) {
                                    const averageRating = productRatings[productId].total / productRatings[productId].count;
                                    const stars = generateStarsHTML(averageRating);
                                    starContainer.html(stars);
                                } else {
                                    starContainer.html(generateStarsHTML(0));
                                }
                            });
                        }
                    },
                    error: function () {
                        console.error('Erreur lors de la récupération des avis.');
                    }
                });
            }

            // Fonction pour générer le HTML des étoiles
            function generateStarsHTML(rating) {
                const maxStars = 5;
                let starsHTML = '';

                for (let i = 1; i <= maxStars; i++) {
                    if (i <= Math.floor(rating)) {
                        starsHTML += '<span class="star filled">&#9733;</span>';
                    } else if (i - rating <= 0.5) {
                        starsHTML += '<span class="star half-filled">&#9734;</span>';
                    } else {
                        starsHTML += '<span class="star">&#9734;</span>';
                    }
                }
                return starsHTML;
            }

            // Fonction pour initialiser le menu déroulant
            function initDropdownMenu() {
                $('#productsDropdown').on('click', function () {
                    const isExpanded = $(this).attr('aria-expanded') === 'true';
                    $('.dropdown-menu').toggleClass('show', !isExpanded);
                });

                $(document).on('click', '.dropdown-item.filter', function (e) {
                    e.preventDefault();
                    const selectedCategory = $(this).data('category');
                    if (selectedCategory === 'all') {
                        window.location.reload();
                        return;
                    }
                    $.ajax({
                        url: 'products/filter/fetch_products.php',
                        type: 'POST',
                        data: { subcategory: selectedCategory },
                        success: function (response) {
                            try {
                                const res = JSON.parse(response);
                                if (res.lenght === 0) {
                                    console.log("gbh");
                                    alert('Aucun produit trouvé pour cette catégorie.');
                                    return;
                                }
                                updateProducts(res);
                            } catch (error) {
                                alert('Erreur lors de la récupération des produits.');
                                console.error(error);
                            }
                        },
                        error: function () {
                            alert('Erreur lors de la requête au serveur.');
                        }
                    });
                });
            }

            // Fonction de recherche de produits
            function initSearchFunctionality() {
                const searchInput = $(".form-control[type='search']");
                const productContainer = $("#produits");
                const products = $(".product-item");
                if (!searchInput.length || !productContainer.length || !products.length) {
                    console.warn("Éléments nécessaires pour la recherche non trouvés.");
                    return;
                }

                searchInput.on("input", function () {
                    const searchTerm = $(this).val().toLowerCase().trim();
                    let visibleProductsCount = 0;

                    products.each(function () {
                        const productName = $(this).find(".product-name").text().toLowerCase();
                        const isVisible = productName.includes(searchTerm);
                        $(this).toggle(isVisible);
                        if (isVisible) visibleProductsCount++;
                    });

                    const noResultsMessage = productContainer.find(".no-results");
                    if (visibleProductsCount === 0) {
                        if (!noResultsMessage.length) {
                            productContainer.append("<p class='no-results' style='text-align: center;'>Aucun produit trouvé.</p>");
                        }
                    } else {
                        noResultsMessage.remove();
                    }
                });
            }
        });
    </script>
    </body>
</html>
