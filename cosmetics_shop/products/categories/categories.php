<?php 
    require '../../api/access/check_admin.php';
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Cosmetics Shop</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="#">Admin Cosmetics Shop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="../../admin.php">Acceuil</a></li>
                        <li class="nav-item"><a class="nav-link" href="../add_product/ajout_produit.php">Ajouter un Produit</a></li>
                        <li class="nav-item"><a class="nav-link" href="../liste_produits.php">Liste des Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="../categories/categoriesList.php">Liste des catégories</a></li>
                        <li class="nav-item"><a class="nav-link" href="./orderList.php">Liste des Commandes</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container mt-4">
            <form id="categoryForm">
                <div class="form-group">
                    <label for="categoryName">Nom de la catégorie</label>
                    <input type="text" class="form-control" id="categoryName" name="categoryName">
                </div>
                <div id="subcategories">
                    <label>Sous-catégories</label>
                    <div class="form-group d-flex">
                        <input type="text" class="form-control subcategory-input" placeholder="Nom de la sous-catégorie">
                        <button type="button" class="btn btn-success ml-2 add-subcategory">+</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </main>

        <footer class="text-center mt-4">
            <div class="container">
                <span>Copyright &copy; Cosmetics Shop</span>
            </div>
        </footer>

        <script src="script.js">
            
        </script>
    </body>
</html>
