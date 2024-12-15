<?php
session_start();

// Vérifier si l'utilisateur est connecté
$loggedIn = isset($_SESSION['user']);
$userId = $loggedIn ? $_SESSION['user']['id'] : null;

// Vérifier si le panier existe dans la session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Charger les produits depuis la base de données
if (file_exists('../config/dbConnect.php')) {
    require '../config/dbConnect.php';
} else {
    echo "Fichier de configuration introuvable";
    die();
}

// Récupérer les produits dans le panier de l'utilisateur connecté
$cart = [];
if ($loggedIn) {
    $query = "SELECT p.id, p.name, p.price, c.quantity, p.image_url
              FROM carts c 
              JOIN products p ON c.product_id = p.id 
              WHERE c.user_id = ?";
    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($result)) {
            $cart[] = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
// Fermer la connexion
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Panier - Cosmetics Shop</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="index.php">Cosmetics Shop</a>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                        <li class="nav-item"><a class="nav-link" href="panier.php"><i class="fas fa-shopping-cart"></i> Panier</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main class="container mt-5">
        <h2>Mon Panier</h2>
        <?php if (!$loggedIn): ?>
            <div class="alert alert-warning" role="alert">
                Vous devez être connecté pour voir et modifier votre panier. <a href="users/login.php" class="alert-link">Se connecter</a>.
            </div>
        <?php else: ?>
            <?php if (empty($cart)): ?>
                <p>Votre panier est vide.</p>
            <?php else: ?>
                <div id="productList" class="row">
                    <?php foreach ($cart as $product): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <img src="<?php echo '../images/' . htmlspecialchars($product['image_url']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text">En stock: </p>
                                    <p class="card-text">Prix: €<?= number_format($product['price'], 2) ?></p>
                                    <p class="card-text">Quantité: <span id="quantity-<?= $product['id'] ?>"><?= $product['quantity'] ?></span></p>
                                    <button class="btn btn-primary" onclick="updateQuantity(<?= $product['id'] ?>, 1)">+</button>
                                    <button class="btn btn-secondary" onclick="updateQuantity(<?= $product['id'] ?>, -1)">-</button>
                                    <button class="btn btn-danger" onclick="removeFromCart(<?= $product['id'] ?>)">Supprimer</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-right">
                    <button class="btn btn-success" onclick="validerCommande()">Valider la commande</button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
    </main>

    <footer>
        <div class="container text-center mt-5">
            <span>Copyright &copy; <span>Cosmoshop</span></span>
        </div>
    </footer>

    <script>
        // Fonction pour mettre à jour la quantité d'un produit dans le panier
        function updateQuantity(productId, change) {
            $.ajax({
                url: 'api/carts/updateCart.php',
                method: 'POST',
                data: { product_id: productId, change: change },
                success: function(response) {
                    console.log(response);
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#quantity-' + productId).text(res.newQuantity);
                        if (res.newQuantity === 0) {
                            location.reload();
                        }
                    } else {
                        alert(res.message);
                    }
                }
            });
        }

        // Fonction pour supprimer un produit du panier
        function removeFromCart(productId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
                $.ajax({
                    url: 'update_cart.php',
                    method: 'POST',
                    data: { product_id: productId, remove: true },
                    success: function(response) {
                        location.reload();
                    }
                });
            }
        }

        // Fonction pour valider la commande
        function validerCommande() {
            $.ajax({
                url: 'valider_commande.php',
                method: 'POST',
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert('Commande validée avec succès !');
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                }
            });
        }
        
    </script>
</body>
</html>
