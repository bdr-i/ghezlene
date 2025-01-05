<?php
    session_start();
    // Vérifier si l'utilisateur est connecté
    $loggedIn = isset($_SESSION['user']);
    if (!$loggedIn) {
        echo '<div class="alert alert-danger">Vous n\'êtes pas autorisé à accéder à cette page</div>';
        echo '<a href="../../users/login.php" class="btn btn-primary">Se connecter</a>';
        exit;
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Liste des Commandes</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container">
                <!-- Logo et marque -->
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img src="../../../images/logo.png" alt="Logo" width="50" class="mr-2">
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
                            <a class="nav-link" href="../../index.php">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../../faq.php">FAQ</a>
                        </li>
                        <?php if ($loggedIn): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../../profile.php">Profile</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../../panier.php">
                                <i class="fas fa-shopping-cart"></i> Panier
                            </a>
                        </li>
                        <? if ($loggedIn): ?> 
                            <li class="nav-item">
                                <a class="nav-link" href=".">Commande</a>
                            </li>
                        <? endif; ?>
                    </ul>
                    <!-- Barre de recherche -->
                    <form class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher">
                        <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
                    </form>
                    <!-- Connexion/Déconnexion -->
                    <?php if ($loggedIn): ?>
                        <a href="../../users/logout.php" class="btn btn-outline-danger ml-3">Se déconnecter</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        </header>

        <main class="container">
            <h1 class="text-center mt-4">Liste des Commandes</h1>

            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Total (€)</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <tr>
                        <td colspan="6" class="text-center">Chargement...</td>
                    </tr>
                </tbody>
            </table>
        </main>

        
        <!-- Modal pour afficher les détails de la commande -->
        <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderDetailsModalLabel">Détails de la commande</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Date :</strong> <span id="orderDate"></span></p>
                        <p><strong>Total (€) :</strong> <span id="orderTotal"></span></p>
                        <p><strong>Statut :</strong> <span id="orderStatus"></span></p>
                        <h6>Articles :</h6>
                        <ul id="orderItemsList"></ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>

        <footer class="text-center mt-4">
            <div class="container">
                <span>Copyright &copy; Cosmetics Shop</span>
            </div>
        </footer>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="script.js"></script>
        
    </body>
</html>
