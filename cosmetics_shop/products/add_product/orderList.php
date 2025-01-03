<?php
    require '../../api/access/check_admin.php';
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
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <a class="navbar-brand" href="#">Admin Cosmetics Shop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="../../admin.php">Acceuil</a></li>
                        <li class="nav-item"><a class="nav-link" href="../liste_produits.php">Liste des Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="../categories/categoriesList.php">Liste des catégories</a></li>
                        <li class="nav-item"><a class="nav-link" href="./orderList.php">Liste des Commandes</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container">
            <h1 class="text-center mt-4">Liste des Commandes</h1>
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Client</th>
                        <th>Total (€)</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="ordersTableBody">
                    <tr>
                        <td colspan="5" class="text-center">Chargement...</td>
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
                        <p><strong>ID de la commande :</strong> <span id="orderId"></span></p>
                        <p><strong>Client :</strong> <span id="clientName"></span></p>
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
        <script src="script.js">
            

        </script>
    </body>
</html>
