<?php
    require '../../api/access/check_admin.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits - Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style href="style.css"> </style>

</head>
<body>

    <img class="img-responsive d-block mx-auto" src="Design_sans_titre__1_-removebg-preview.png" alt="" width="50px"/>
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Cosmetics Shop</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="../../admin.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="./liste_produits.php">Liste des Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="../categories/categoriesList.php">Liste des catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="../order/orderList.php">Liste des Commandes</a></li>
                    <!-- Barre de recherche -->
                    <form class="form-inline my-2 my-lg-0">
                        <input id="searchProduct" class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher">
                    </form>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button class="btn btn-secondary" onclick="filtrerProduitsRuptureStock()">Voir les produits en rupture de stock</button>
            <a href="../add_product/ajout_produit.php" class="btn btn-success">Ajouter un Produit</a>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Rechercher par stock :</label>
                <div class="input-group">
                    <input type="number" id="stockMin" class="form-control" placeholder="Stock minimum" aria-label="Stock minimum">
                    <input type="number" id="stockMax" class="form-control" placeholder="Stock maximum" aria-label="Stock maximum">
                    <button class="btn btn-primary" onclick="rechercherParStockMinEtMax()">Rechercher</button>
                </div>
            </div>
        </div>



        <h2>Liste des Produits</h2>
        <div id="productCount" class="mb-4"></div>
        <div id="productList" class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4 product-item" id="product-<?= $product['id'] ?>">
                    <div class="card <?= ($product['stock'] == 0) ? 'border-danger' : '' ?>">
                        <img src="../../../images/<?= $product['image_url'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text">Prix: €<?= $product['price'] ?></p>
                            <p class="card-text">Quantité: <?= $product['stock'] ?></p>
                            <?php if ($product['stock'] == 0): ?>
                                <span class="badge badge-danger">Out of Stock</span>
                            <?php endif; ?>
                            <button class="btn btn-danger" onclick="supprimerProduit(<?= $product['id'] ?>)">Supprimer</button>
                            <button class="btn btn-warning" onclick="modifierProduit(<?= $product['id'] ?>)">Modifier</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
<footer class="bg-light text-center text-lg-start mt-5" id="footer">
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            &copy; 2021 Cosmetics Shop:
            <a class="text-dark" href="https://mdbootstrap.com/">CosmeticsShop.com</a>
        </div>
    </footer>

    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Modifier le produit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editProductForm">
                        <input type="hidden" id="editProductId">
                        <div class="form-group">
                            <label for="editProductName">Nom du produit</label>
                            <input type="text" class="form-control" id="editProductName" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductPrice">Prix</label>
                            <input type="number" step="0.01" class="form-control" id="editProductPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductImage">URL de l'image</label>
                            <input type="text" class="form-control" id="editProductImage" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductStock">Quantité</label>
                            <input type="number" class="form-control" id="editProductStock" required>
                        </div>
                        <div class="form-group">
                            <label for="editProductCategory">Catégorie</label>
                            <select class="form-control" id="editProductCategory" required>
                                <!-- Les catégories seront injectées ici dynamiquement -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="editProductSubcategory">Sous-catégorie</label>
                            <select class="form-control" id="editProductSubcategory">
                                <!-- Les sous-catégories seront injectées ici dynamiquement -->
                            </select>
                        </div>


                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="script.js">
        
    </script>

</body>
</html>