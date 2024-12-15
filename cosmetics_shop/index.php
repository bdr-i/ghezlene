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
        <link rel="stylesheet" href="index_style.css">
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
                            <a class="nav-link" href="#faq">FAQ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="panier.php">
                                <i class="fas fa-shopping-cart"></i> Panier
                            </a>
                        </li>
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
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
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
                    <?php foreach ($produits as $produit): ?>
                    <div class="col-md-4 product-item"> <!-- Classe corrigée -->
                        <div class="card">
                            <img src="<?php echo '../images/' . htmlspecialchars($produit['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produit['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title product-name"><?php echo htmlspecialchars($produit['name']); ?></h5> <!-- Ajout de product-name -->
                                <p class="card-text"><?php echo htmlspecialchars($produit['description']); ?></p>
                                <p class="card-text"><?php echo '€ ' . number_format($produit['price'], 2); ?></p>
                                <button 
                                    class="add-to-cart btn btn-primary" 
                                    data-product-id="<?php echo $produit['id']; ?>" 
                                    data-quantity="<?php echo $produit['stock']; ?>">
                                    Ajouter au panier
                                </button>
                                <form action="products/details.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">
                                    <button type="submit" class="btn btn-link">Voir plus</button>
                                </form> 
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <script>
        $(document).ready(function () {
            // Fonction pour gérer les clics sur les boutons "Ajouter au panier"
            function handleAddToCartClick() {
                $('.add-to-cart').off('click').on('click', function () {
                    var productId = $(this).data('product-id');
                    var quantity = $(this).data('quantity');
                    var loggedIn = <?php echo $loggedIn ? 'true' : 'false'; ?>;

                    if (!loggedIn) {
                        window.location.href = 'users/login.php';
                        return;
                    }

                    $.ajax({
                        url: 'api/carts/addToCart.php',
                        type: 'POST',
                        data: {
                            product_id: productId,
                            quantity: quantity
                        },
                        success: function (response) {
                            try {
                                const res = JSON.parse(response);
                                if (res.status === 'success') {
                                    alert('Produit ajouté au panier');
                                } else {
                                    alert('Erreur : ' + res.message);
                                }
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

            // Initialiser les événements sur les boutons "Ajouter au panier"
            handleAddToCartClick();

            // Fonction pour mettre à jour les produits affichés dynamiquement
            function updateProducts(products) {
                // Supprimer les produits existants dans la section contenant les produits
                $('.row').empty();

                // Ajouter les nouveaux produits
                products.forEach(product => {
                    const productCard = `
                        <div class="col-md-4 product-card" data-category="${product.subcategory}">
                            <div class="card">
                                <img src="../images/${product.image_url}" class="card-img-top" alt="${product.product_name}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.product_name}</h5>
                                    <p class="card-text">${product.product_description}</p>
                                    <p class="card-text">€ ${product.price}</p>
                                    <button 
                                        class="add-to-cart btn btn-primary" 
                                        data-product-id="${product.product_id}" 
                                        data-quantity="${product.stock}">
                                        Ajouter au panier
                                    </button>
                                    <form action="products/details.php" method="POST">
                                        <input type="hidden" name="id" value="${product.product_id}">
                                        <button type="submit" class="btn btn-link">Voir plus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;
                    $('.row').append(productCard);
                });

                // Réinitialiser les événements pour les nouveaux boutons
                handleAddToCartClick();
            }


            $(document).ready(function () {
            // Afficher les sous-catégories dynamiquement
            $('#productsDropdown').on('click', function () {
                // Vérifiez si le menu est déjà ouvert
                if ($(this).attr('aria-expanded') === 'false') {
                    $('.dropdown-menu').addClass('show');
                }
            });

            // Gestion des clics sur les catégories et sous-catégories
            $(document).on('click', '.dropdown-item.filter', function (e) {
                e.preventDefault();
                const selectedCategory = $(this).data('category');
                $.ajax({
                    url: 'products/filter/fetch_products.php',
                    type: 'POST',
                    data: {
                        subcategory: selectedCategory
                    },
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
            });
        });
        
        });
             
            // JavaScript pour mettre à jour les produits en fonction de la recherche
        document.addEventListener("DOMContentLoaded", () => {
            // Sélection des éléments HTML
            const searchInput = document.querySelector(".form-control[type='search']");
            const productContainer = document.querySelector("#produits"); // Div contenant les produits
            const products = document.querySelectorAll(".product-item"); // Chaque produit a la classe product-item
            // Vérification de la présence des éléments dans le DOM
            if (!searchInput || !productContainer || products.length === 0) {
                console.warn("Les éléments nécessaires pour la recherche ne sont pas présents dans le DOM.");
                return;
            }

            // Ajout d'un écouteur d'événements sur la barre de recherche
            searchInput.addEventListener("input", (event) => {
                const searchTerm = event.target.value.toLowerCase().trim(); // Récupère et nettoie la recherche

                let visibleProductsCount = 0; // Compte les produits visibles

                // Parcourt les produits et filtre ceux qui correspondent au terme de recherche
                products.forEach(product => {
                    const productName = product.querySelector(".product-name")?.textContent.toLowerCase() || ""; // Nom du produit

                    if (productName.includes(searchTerm)) {
                        product.style.display = "block"; // Affiche le produit
                        visibleProductsCount++;
                    } else {
                        product.style.display = "none"; // Cache le produit
                    }
                });

                // Affiche un message si aucun produit n'est trouvé
                const noResultsMessage = productContainer.querySelector(".no-results");

                if (visibleProductsCount === 0) {
                    if (!noResultsMessage) {
                        const message = document.createElement("p");
                        message.textContent = "Aucun produit trouvé.";
                        message.classList.add("no-results");
                        message.style.textAlign = "center";
                        productContainer.appendChild(message);
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove(); // Supprime le message si des produits deviennent visibles
                }
            });
        });


    </script>
    </body>
</html>
