<?php
    require 'api/access/check_admin.php';
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
                    <li class="nav-item"><a class="nav-link" href="./admin.php">Acceuil</a></li>
                        <li class="nav-item"><a class="nav-link" href="products/gestionDeProduit/liste_produits.php">Liste des Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="products/categories/categoriesList.php">Liste des catégories</a></li>
                        <li class="nav-item"><a class="nav-link" href="products/order/orderList.php">Liste des Commandes</a></li>
                        <li class="nav-item"><a class="nav-link" href="users/gestionUsers/userList.php">Liste des Utilisateurs</a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="productsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Produits</a>
                            <div class="dropdown-menu" aria-labelledby="productsDropdown">
                                <h6 class="dropdown-header">Maquillage</h6>
                                <a class="dropdown-item" href="rouge-a-levres.html">Rouge à Lèvres</a>
                                <a class="dropdown-item" href="fonds-de-teint.html">Fonds de Teint</a>
                                <a class="dropdown-item" href="mascara.html">Mascara</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Parfums</h6>
                                <a class="dropdown-item" href="feminin.html">Parfums Féminins</a>
                                <a class="dropdown-item" href="masculin.html">Parfums Masculins</a>
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">Soins</h6>
                                <a class="dropdown-item" href="serum.html">Sérum</a>
                                <a class="dropdown-item" href="creme.html">Crème Hydratante</a>
                            </div>
                        </li>
                        <a href="users/logout.php" class="btn btn-outline-danger ml-3">Se déconnecter</a>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container">
            <h1 class="mt-4">Dashboard Admin</h1>
            <div class="row">
                <div class="col-md-4">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Produits</div>
                        <div class="card-body">
                            <h5 class="card-title" id="total-products">0</h5>
                            <p class="card-text">Nombre total de produits dans la base de données.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header">Total Utilisateurs</div>
                        <div class="card-body">
                            <h5 class="card-title" id="total-users">0</h5>
                            <p class="card-text">Nombre total d'utilisateurs inscrits.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-header">Produits en Rupture</div>
                        <div class="card-body">
                            <h5 class="card-title" id="out-of-stock">0</h5>
                            <p class="card-text">Produits actuellement en rupture de stock.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Produits les mieux notés</div>
                        <div class="card-body">
                            <canvas id="top-rated-products-chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">Produits les plus vendus</div>
                        <div class="card-body">
                            <canvas id="top-sold-products-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>

        <footer class="text-center">
            <div class="container">
                <span>Copyright &copy; Cosmetics Shop</span>
            </div>
        </footer>

        <!-- Scripts nécessaires -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                getProductData();
                getUserData();
            });

            function getUserData() {
                // Requête AJAX pour obtenir les données des utilisateurs
                $.ajax({
                    url: "api/users/getAllUsers.php",
                    type: "GET",
                    success: function(response) {
                        const data = JSON.parse(response);
                        const totalUsers = data.length;
                        document.getElementById("total-users").textContent = totalUsers;
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
            
            function getProductData() {
                // Requête AJAX pour obtenir les données des produits
                $.ajax({
                    url: "api/products/getAllProducts.php",
                    type: "GET",
                    success: function(response) {
                        const data = JSON.parse(response);
                        const totalProducts = data.length;
                        let outOfStock = 0;
                        for (let i = 0; i < data.length; i++) {
                            if (data[i].stock === 0) {
                                outOfStock++;
                            }
                        }
                        document.getElementById("total-products").textContent = totalProducts;
                        document.getElementById("out-of-stock").textContent = outOfStock;
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }
            document.addEventListener("DOMContentLoaded", function() {
            getProductData();
            getUserData();
            getTopRatedProducts();
            getTopSoldProducts();
        });

        function getUserData() {
            // Requête AJAX pour obtenir les données des utilisateurs
            $.ajax({
                url: "api/users/getAllUsers.php",
                type: "GET",
                success: function(response) {
                    const data = JSON.parse(response);
                    const totalUsers = data.length;
                    document.getElementById("total-users").textContent = totalUsers;
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function getProductData() {
            // Requête AJAX pour obtenir les données des produits
            $.ajax({
                url: "api/products/getAllProducts.php",
                type: "GET",
                success: function(response) {
                    const data = JSON.parse(response);
                    const totalProducts = data.length;
                    let outOfStock = 0;
                    for (let i = 0; i < data.length; i++) {
                        if (data[i].stock === 0) {
                            outOfStock++;
                        }
                    }
                    document.getElementById("total-products").textContent = totalProducts;
                    document.getElementById("out-of-stock").textContent = outOfStock;
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function getTopRatedProducts() {
            // Requête AJAX pour obtenir les produits les mieux notés
            $.ajax({
                url: "api/products/getTopRatedProducts.php", // Cette URL doit renvoyer les produits avec les notes
                type: "GET",
                success: function(response) {
                    const data = JSON.parse(response);
                    const labels = data.map(item => item.name);
                    const ratings = data.map(item => item.average_rating);

                    const ctx = document.getElementById("top-rated-products-chart").getContext("2d");
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Note moyenne (sur 5)',
                                data: ratings,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 5
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function getTopSoldProducts() {
            // Requête AJAX pour obtenir les produits les plus vendus
            $.ajax({
                url: "api/products/getTopSoldProducts.php", // Cette URL doit renvoyer les produits les plus vendus
                type: "GET",
                success: function(response) {
                    const data = JSON.parse(response);
                    const labels = data.map(item => item.name);
                    const sales = data.map(item => item.total_sales);

                    const ctx = document.getElementById("top-sold-products-chart").getContext("2d");
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Quantité vendue',
                                data: sales,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    </body>
</html>
