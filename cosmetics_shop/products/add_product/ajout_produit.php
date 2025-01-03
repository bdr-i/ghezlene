<?php
    require '../../api/access/check_admin.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Produit - Cosmetics Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .error { color: red; font-size: 0.9em; margin-top: 5px; }
        .is-invalid { border-color: red; }
    </style>
</head>
<body>
    <img class="img-responsive d-block mx-auto" src="Design_sans_titre__1_-removebg-preview.png" alt="" width="50px" />
    <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">Cosmetics Shop</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="../../admin.php">Acceuil</a></li>
                    <li class="nav-item"><a class="nav-link" href="../gestionDeProduit/liste_produits.php">Liste des Produits</a></li>
                    <li class="nav-item"><a class="nav-link" href="../categories/categoriesList.php">Liste des catégories</a></li>
                    <li class="nav-item"><a class="nav-link" href="./orderList.php">Liste des Commandes</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2>Ajouter un Produit</h2>

        <form id="productForm" action="../../api/products/addProduct.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productName">Nom du Produit</label>
                <input type="text" class="form-control" id="productName" name="productName">
                <div class="error" id="productNameError"></div>
            </div>

            <div class="form-group">
                <label for="productDescription">Description</label>
                <textarea class="form-control" id="productDescription" name="productDescription" rows="3"></textarea>
                <div class="error" id="productDescriptionError"></div>
            </div>
            
            <div class="form-group">
                <label for="productPrice">Prix</label>
                <input type="number" class="form-control" id="productPrice" name="productPrice">
                <div class="error" id="productPriceError"></div>
            </div>

            <div class="form-group">
                <label for="productImage">Image</label>
                <input type="file" class="form-control" id="productImage" name="productImage">
                <div class="error" id="productImageError"></div>
            </div>

            <div class="form-group">
                <label for="productCategory">Catégorie</label>
                <select class="form-control" id="productCategory" name="productCategory" onchange="updateSubcategories()">
                    <option value="">Sélectionner une catégorie</option>
                    <option value="1">Maquillage</option>
                    <option value="2">Parfum</option>
                    <option value="3">Soins</option>
                </select>
                <div class="error" id="productCategoryError"></div>
            </div>

            <div class="form-group">
                <label for="productSubcategory">Sous-catégorie</label>
                <select class="form-control" id="productSubcategory" name="productSubcategory">
                    <option value="">Sélectionner une sous-catégorie</option>
                </select>
                <div class="error" id="productSubcategoryError"></div>
            </div>

            <div class="form-group">
                <label for="productQuantity">Quantité</label>
                <input type="number" class="form-control" id="productQuantity" name="productQuantity" min="1">
                <div class="error" id="productQuantityError"></div>
            </div>

            <button type="submit" class="btn btn-primary">Ajouter le Produit</button>
        </form>
        <a href="../liste_produits.php" class="btn btn-secondary mt-3">Voir la Liste des Produits</a>
    </div>
    <footer> 
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <p class="text-center">Cosmetics Shop &copy; 2021</p>
                </div>
            </div>
        </div>
    </footer>
    
    <script>
        const categories = {
            "1": {"name": "Maquillage", "subcategories": [{"id": "1", "name": "Rouge à lèvres"}, {"id": "2", "name": "Palette de maquillage"}, {"id": "3", "name": "Mascara"}]},
            "2": {"name": "Parfum", "subcategories": [{"id": "4", "name": "Brume corporelle"}]},
            "3": {"name": "Soins", "subcategories": [{"id": "5", "name": "Sérum anti-âge"}]}
        };

        function updateSubcategories() {
            const categoryId = document.getElementById('productCategory').value;
            const subcategorySelect = document.getElementById('productSubcategory');
            subcategorySelect.innerHTML = '<option value="">Sélectionner une sous-catégorie</option>';

            if (categories[categoryId]) {
                categories[categoryId].subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
            }
        }
    </script>
</body>
</html>
