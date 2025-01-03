<?php
require '../../api/access/check_admin.php';
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Gestion des Utilisateurs</title>
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
                        <li class="nav-item"><a class="nav-link" href="../../products/gestionDeProduit/liste_produits.php">Liste des Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../products/categories/categoriesList.php">Liste des catégories</a></li>
                        <li class="nav-item"><a class="nav-link" href="../../products/order/orderList.php">Liste des Commandes</a></li>
                        <li class="nav-item"><a class="nav-link" href="./userList.php">Liste des Utilisateurs</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container">
            <h1 class="text-center mt-4">Gestion des Utilisateurs</h1>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom complet</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="6" class="text-center">Chargement...</td>
                    </tr>
                </tbody>
            </table>
        </main>

        <!-- Modal pour Ajouter/Modifier un utilisateur -->
        <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="userModalLabel">Ajouter/Modifier un Utilisateur</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="userForm">
                            <input type="hidden" id="userId" name="userId">
                            <div class="form-group">
                                <label for="userFirstName">Prénom</label>
                                <input type="text" class="form-control" id="userFirstName" name="userFirstName" required>
                            </div>
                            <div class="form-group">
                                <label for="userLastName">Nom</label>
                                <input type="text" class="form-control" id="userLastName" name="userLastName" required>
                            </div>
                            <div class="form-group">
                                <label for="userEmail">Email</label>
                                <input type="email" class="form-control" id="userEmail" name="userEmail" required>
                            </div>
                            <div class="form-group">
                                <label for="userPhone">Téléphone</label>
                                <input type="text" class="form-control" id="userPhone" name="userPhone" required>
                            </div>
                            <div class="form-group">
                                <label for="userRole">Rôle</label>
                                <select class="form-control" id="userRole" name="userRole" required>
                                    <!-- Les options de rôle seront ajoutées dynamiquement -->
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </form>
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
        <script>
            // Charger les rôles dans le formulaire
            function loadRoles() {
                $.get('../../api/roles/getRoles.php', function(data) {
                    const roles = JSON.parse(data);
                    const userRoleSelect = $('#userRole');
                    userRoleSelect.empty();
                    console.log(roles);
                    roles.forEach(role => {
                        userRoleSelect.append(`<option value="${role.id}">${role.role_name}</option>`);
                    });
                });
            }

            // Charger la liste des utilisateurs
            function loadUsers() {
                $.get('../../api/users/getAllUsers.php', function(data) {
                    const users = JSON.parse(data);
                    const usersTableBody = $('#usersTableBody');
                    usersTableBody.empty();
                    if (users.length) {
                        users.forEach(user => {
                            usersTableBody.append(`
                                <tr>
                                    <td>${user.id}</td>
                                    <td>${user.first_name} ${user.last_name}</td>
                                    <td>${user.email}</td>
                                    <td>${user.phone}</td>
                                    <td>${user.role_name}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" onclick="editUser(${user.id})">Modifier</button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">Supprimer</button>
                                    </td>
                                </tr>
                            `);
                        });
                    } else {
                        usersTableBody.append('<tr><td colspan="6" class="text-center">Aucun utilisateur trouvé.</td></tr>');
                    }
                });
            }

            // Modifier un utilisateur
            function editUser(userId) {
                $.get(`../../api/users/getUser.php?id=${userId}`, function(data) {
                    const user = JSON.parse(data);
                    $('#userId').val(user.id);
                    $('#userFirstName').val(user.first_name);
                    $('#userLastName').val(user.last_name);
                    $('#userEmail').val(user.email);
                    $('#userPhone').val(user.phone);
                    $('#userRole').val(user.role_id);
                    $('#userModal').modal('show');
                });
            }

            // Supprimer un utilisateur
            function deleteUser(userId) {
                if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                    $.post('../../api/users/deleteUser.php', { id: userId }, function(response) {
                        alert(response.message);
                        loadUsers();
                    });
                }
            }

            // Ajouter ou modifier un utilisateur
            $('#userForm').submit(function(e) {
                e.preventDefault();
                const formData = $(this).serialize();
                $.post('../../api/users/updateUser.php', formData, function(response) {
                    $('#userModal').modal('hide');
                    loadUsers();
                });
            });

            $(document).ready(function() {
                loadRoles();
                loadUsers();
            });
        </script>
    </body>
</html>
