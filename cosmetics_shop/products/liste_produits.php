<?php
    // Connexion à la base de données
    if (file_exists('../../config/dbConnect.php')) {
        require '../../config/dbConnect.php';
    } else {
        echo "File not found";
        die();
    }

    // Récupérer les produits depuis la base de données
    $query = "SELECT * FROM products";
    $result = mysqli_query($link, $query);
    $products = [];

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row; // Stocker les produits dans un tableau
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
    <title>Liste des Produits - Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                    <li class="nav-item"><a class="nav-link" href="../admin.html">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_product/ajout_produit.php">Ajouter un Produit</a></li>
                    <li class="nav-item"><a class="nav-link" href="liste_produits.php">Liste des Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="categories/categoriesList.html">Liste des catégories</a></li>
                        <li class="nav-item"><a class="nav-link" href="order/orderList.html">Liste des Commandes</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container mt-5">
        <h2>Liste des Produits</h2>
        <div id="productCount" class="mb-4"></div>
        <div id="productList" class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4" id="product-<?= $product['id'] ?>">
                    <div class="card">
                        <img src="../../images/<?= $product['image_url'] ?>" class="card-img-top" alt="<?= $product['name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= $product['name'] ?></h5>
                            <p class="card-text">Prix: €<?= $product['price'] ?></p>
                            <p class="card-text">Quantité: <?= $product['stock'] ?></p>
                            <button class="btn btn-danger" onclick="supprimerProduit(<?= $product['id'] ?>)">Supprimer</button>
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
                            <label for="editProductSubcategory">Sous-catégorie (optionnel)</label>
                            <select class="form-control" id="editProductSubcategory">
                                <option value="">Aucune</option>
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
    <script>
        // Récupérer tous les produits
        function getAllProducts() {
            fetch('../api/products/getAllProducts.php', { method: 'GET' })
                .then((response) => response.json())
                .then((products) => {
                    const productList = document.getElementById("productList");
                    productList.innerHTML = products
                        .map(
                            (product) => `
                        <div class="col-md-4 mb-4" id="product-${product.id}">
                            <div class="card">
                                <img src="../../images/${product.image_url}" class="card-img-top" alt="${product.name}">
                                <div class="card-body">
                                    <h5 class="card-title">${product.name}</h5>
                                    <p class="card-text">Prix: €${product.price}</p>
                                    <p class="card-text">Quantité: ${product.stock}</p>
                                    <button class="btn btn-danger" onclick="supprimerProduit(${product.id})">Supprimer</button>
                                    <button class="btn btn-warning" onclick="modifierProduit(${product.id})">Modifier</button>
                                </div>
                            </div>
                        </div>
                    `
                        )
                        .join('');
                    document.getElementById("productCount").innerHTML = `<h5>Total des produits: ${products.length}</h5>`;
                })
                .catch((error) => console.error('Erreur lors de la récupération des produits :', error));
        }

        // Supprimer un produit
        function supprimerProduit(productId) {
            if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
                fetch('../api/products/deleteProduct.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: productId }),
                })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            document.getElementById(`product-${productId}`).remove();
                            document.getElementById("productCount").innerHTML = `<h5>Total des produits: ${document.querySelectorAll("#productList > div").length}</h5>`;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch((error) => console.error('Erreur lors de la suppression du produit :', error));
            }
        }

        // Modifier un produit (affiche une modale avec les données actuelles)
        function modifierProduit(productId) {
            fetch(`../api/products/getProductDetails.php?id=${productId}`)
                .then((response) => response.json())
                .then((productDetails) => {
                    
                    // Préremplir les champs du formulaire
                    document.getElementById("editProductId").value = productId;
                    document.getElementById("editProductName").value = productDetails.product.name;
                    document.getElementById("editProductPrice").value = productDetails.product.price;
                    document.getElementById("editProductImage").value = productDetails.product.image_url;
                    document.getElementById("editProductStock").value = productDetails.product.stock;
                    
                    // Charger les catégories et sous-catégories
                    getAllCategories(() => {
                        document.getElementById("editProductCategory").value = productDetails.product.category_id;
                        updateSubcategories(productDetails.product.category_id, productDetails.product.subcategory_id);
                    });

                    // Afficher la modale
                    $("#editProductModal").modal("show");
                })
                .catch((error) => console.error('Erreur lors de la récupération des détails du produit :', error));
        }

        // Récupérer toutes les catégories et remplir les menus déroulants
        function getAllCategories(callback) {
            fetch('../api/categories/getAllCategories.php')
                .then((response) => response.json())
                .then((categories) => {
                    const categorySelect = document.getElementById("editProductCategory");
                    categorySelect.innerHTML = '<option value="">Sélectionnez une catégorie</option>';
                    for (const [id, category] of Object.entries(categories)) {
                        const option = document.createElement("option");
                        option.value = id;
                        option.textContent = category.name;
                        categorySelect.appendChild(option);
                    }
                    // Exécuter le callback (utile pour mettre à jour les sous-catégories)
                    if (callback) callback();
                })
                .catch((error) => console.error('Erreur lors de la récupération des catégories :', error));
        }

        // Mettre à jour les sous-catégories dynamiquement en fonction de la catégorie sélectionnée
        function updateSubcategories(categoryId, selectedSubcategoryId = null) {
            const subcategorySelect = document.getElementById("editProductSubcategory");
            subcategorySelect.innerHTML = '<option value="">Aucune</option>';

            const categories = JSON.parse(localStorage.getItem("categories")) || {};
            if (categories[categoryId]) {
                categories[categoryId].subcategories.forEach((subcategory) => {
                    const option = document.createElement("option");
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    if (subcategory.id == selectedSubcategoryId) option.selected = true;
                    subcategorySelect.appendChild(option);
                });
            }
        }

        // Soumettre les modifications apportées au produit
        document.getElementById("editProductForm").addEventListener("submit", function (event) {
            event.preventDefault();

            const updatedProduct = {
                id: parseInt(document.getElementById("editProductId").value),
                name: document.getElementById("editProductName").value,
                price: parseFloat(document.getElementById("editProductPrice").value),
                image_url: document.getElementById("editProductImage").value,
                category: parseInt(document.getElementById("editProductCategory").value),
                subcategory_id: document.getElementById("editProductSubcategory").value || null,
                stock: parseInt(document.getElementById("editProductStock").value),
            };

            console.log(updatedProduct);

            if (!updatedProduct.name || isNaN(updatedProduct.price) || isNaN(updatedProduct.category)) {
                alert("Veuillez remplir correctement tous les champs obligatoires.");
                return;
            }

            fetch("../api/products/updateProduct.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ product: updatedProduct }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);
                        $("#editProductModal").modal("hide");
                        getAllProducts(); // Rafraîchir les produits
                    } else {
                        alert(data.message);
                    }
                })
                .catch((error) => console.error('Erreur lors de la modification du produit :', error));
        });

        // Charger les données au chargement de la page
        document.addEventListener("DOMContentLoaded", function () {
            getAllProducts();
            getAllCategories();
        });
    </script>

</body>
</html>