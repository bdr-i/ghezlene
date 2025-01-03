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
                            <a class="nav-link" href="./">FAQ</a>
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
        <section id="faq" class="s_faq_collapse pt-5 pb-5">
            <div class="container">
                <h3>Foire aux questions (FAQ)</h3>
                <div class="accordion" id="faqAccordion">
                    <div class="card">
                        <div class="card-header" id="headingOne">
                            <h5 class="mb-0">
                                <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    Q1: Comment puis-je passer une commande ?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                            <div class="card-body">
                                Pour passer une commande, ajoutez les produits souhaités à votre panier, puis procédez à la validation de votre commande en suivant les instructions à l'écran.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingTwo">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Q2: Quels modes de paiement acceptez-vous ?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                            <div class="card-body">
                                Le payement se fait a livraison.
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingThree">
                            <h5 class="mb-0">
                                <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Q3: Puis-je retourner un produit ?
                                </button>
                            </h5>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                            <div class="card-body">
                                Oui, vous pouvez retourner un produit dans un délai de 30 jours suivant la réception, à condition qu'il soit dans son état d'origine.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </body>
</html>
