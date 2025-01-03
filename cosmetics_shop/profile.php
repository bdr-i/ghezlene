<?php
session_start();

// Vérifier si l'utilisateur est connecté (par exemple, si l'ID de l'utilisateur est dans la session)
if (!isset($_SESSION['user'])) {
    echo "Veuillez vous connecter pour accéder à votre profil.";
    die();
}

// Connexion à la base de données
if (file_exists('../config/dbConnect.php')) {
    require '../config/dbConnect.php';
} else {
    echo "Fichier de connexion introuvable";
    die();
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
// Récupérer les informations de l'utilisateur à partir de la base de données
$userId = $_SESSION['user']['id'];
$query = "SELECT * FROM users WHERE id = $userId";
$result = mysqli_query($link, $query);
$user = mysqli_fetch_assoc($result);

// Fermer la connexion
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header id="top">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">Cosmetics Shop</a>
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
            </div>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <h2>Bienvenue, <?php echo $user['first_name']." ".$user['last_name'] ?> !</h2>
            <div class="card">
                <div class="card-header">Informations de votre profil</div>
                <div class="card-body">
                    <p><strong>Nom :</strong> <?php echo $user['first_name']; ?></p>
                    <p><strong>Prenom :</strong> <?php echo $user['last_name']; ?></p>
                    <p><strong>Email :</strong> <?php echo $user['email']; ?></p>
                    <p><strong>Téléphone :</strong> <?php echo $user['phone']; ?></p>
                    <!-- Optionnel : Formulaire pour modifier le profil -->
                    <a href="editProfile.php" class="btn btn-primary">Modifier mon profil</a>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <span>Copyright &copy; <span>Cosmoshop</span></span>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
