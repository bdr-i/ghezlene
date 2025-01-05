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
                        <li class="nav-item"><a class="nav-link" href="../gestionDeProduit/liste_produits.php">Liste des Produits</a></li>
                        <li class="nav-item"><a class="nav-link" href="./categoriesList.php">Liste des catégories</a></li>
                        <li class="nav-item"><a class="nav-link" href="../order/orderList.php">Liste des Commandes</a></li>
                    </ul>
                </div>
            </nav>
        </header>

        <main class="container">
            <h1 class="text-center mt-4">Liste des catégories et sous-catégories</h1>
            <button class="btn btn-primary" onclick="window.location.href = './categories.php'">Ajouter une catégorie</button>
            <table class="table table-striped mt-4">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nom</th>
                        <th>Sous-catégories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="categoriesTableBody">
                    <tr>
                        <td colspan="4" class="text-center">Chargement...</td>
                    </tr>
                </tbody>
            </table>

            <!-- Modal pour modifier les catégories -->
            <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="categoryModalLabel">Modifier la catégorie</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
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
                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center mt-4">
            <div class="container">
                <span>Copyright &copy; Cosmetics Shop</span>
            </div>
        </footer>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const categoriesTableBody = document.getElementById("categoriesTableBody");

                // Charger les catégories et sous-catégories via AJAX
                fetch("../../api/categories/getAllCategories.php")
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then((data) => {
                        categoriesTableBody.innerHTML = ""; // Effacez le message de chargement

                        if (data && Object.keys(data).length > 0) {
                            Object.values(data).forEach((category, index) => {
                                const subcategories =
                                    category.subcategories && category.subcategories.length > 0
                                        ? category.subcategories.map((sub) => sub.name).join(", ")
                                        : "Aucune";

                                const subcategoriesJson = encodeURIComponent(JSON.stringify(category.subcategories || []));

                                const row = `
                                    <tr id="categoryRow-${category.id}">
                                        <td>${index + 1}</td>
                                        <td id="categoryName-${category.id}">${category.name}</td>
                                        <td id="subcategories-${category.id}">${subcategories}</td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" onclick="editCategory(${category.id}, '${category.name}', '${subcategoriesJson}')">Modifier</button>
                                            <button class="btn btn-danger btn-sm" onclick="deleteCategory(${category.id})">Supprimer</button>
                                        </td>
                                    </tr>
                                `;
                                categoriesTableBody.insertAdjacentHTML("beforeend", row);
                            });
                        } else {
                            categoriesTableBody.innerHTML = `
                                <tr>
                                    <td colspan="4" class="text-center">Aucune catégorie trouvée.</td>
                                </tr>
                            `;
                        }
                    })
                    .catch((error) => {
                        console.error("Erreur lors de la récupération des catégories :", error);
                        categoriesTableBody.innerHTML = `
                            <tr>
                                <td colspan="4" class="text-center text-danger">Erreur lors du chargement des catégories.</td>
                            </tr>
                        `;
                    });
            });

            function editCategory(id, name, subcategoriesJson) {
                document.getElementById("categoryName").value = name;

                const subcategories = JSON.parse(decodeURIComponent(subcategoriesJson));

                const subcategoriesContainer = document.getElementById("subcategories");
                subcategoriesContainer.innerHTML = ""; // Nettoyer les anciennes entrées

                subcategories.forEach((sub) => {
                    const subcategoryField = `
                        <div class="form-group d-flex">
                            <input type="text" class="form-control subcategory-input" value="${sub.name}" placeholder="Nom de la sous-catégorie" required>
                            <button type="button" class="btn btn-danger ml-2 remove-subcategory">-</button>
                        </div>
                    `;
                    subcategoriesContainer.insertAdjacentHTML("beforeend", subcategoryField);
                });

                const newSubcategoryField = `
                    <div class="form-group d-flex">
                        <input type="text" class="form-control subcategory-input" placeholder="Nom de la sous-catégorie" required>
                        <button type="button" class="btn btn-success ml-2 add-subcategory">+</button>
                    </div>
                `;
                subcategoriesContainer.insertAdjacentHTML("beforeend", newSubcategoryField);

                document.getElementById("categoryForm").setAttribute("data-category-id", id);

                $("#categoryModal").modal("show");
            }

            document.getElementById("categoryForm").addEventListener("submit", (event) => {
                event.preventDefault();

                const id = event.target.getAttribute("data-category-id");
                const name = document.getElementById("categoryName").value.trim();
                const subcategories = Array.from(document.querySelectorAll(".subcategory-input"))
                    .map((input) => input.value.trim())
                    .filter((value) => value);

                if (!name) {
                    alert("Le nom de la catégorie est requis.");
                    return;
                }

                console.log({ id, name, subcategories });
                $.ajax({
                    url: "../../api/categories/updateCategory.php",
                    type: "PUT",
                    contentType: "application/json",
                    data: JSON.stringify({ id, name, subcategories }),
                    success: (response) => {
                        console.log(id, name, subcategories);
                        console.log(response);
                        const data = JSON.parse(response);
                        if (data.status === "success") {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    },
                    error: (error) => {
                        console.error("Erreur lors de la mise à jour de la catégorie :", error);
                        alert("Une erreur est survenue. Veuillez réessayer.");
                    },
                });
            });

            document.addEventListener("click", (event) => {
                if (event.target.matches(".add-subcategory")) {
                    const subcategoriesContainer = document.getElementById("subcategories");
                    const newSubcategoryField = `
                        <div class="form-group d-flex">
                            <input type="text" class="form-control subcategory-input" placeholder="Nom de la sous-catégorie" required>
                            <button type="button" class="btn btn-danger ml-2 remove-subcategory">-</button>
                        </div>
                    `;
                    subcategoriesContainer.insertAdjacentHTML("beforeend", newSubcategoryField);
                }

                if (event.target.matches(".remove-subcategory")) {
                    event.target.closest(".form-group").remove();
                }
            });

                function deleteCategory(id) {
                    if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
                        fetch(`../../api/categories/deleteCategory.php`, {
                            method: 'DELETE',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id: id }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                location.reload();
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la suppression :', error);
                            alert('Une erreur est survenue. Veuillez réessayer.');
                        });
                    }
                }

                document.addEventListener('click', (event) => {
                    if (event.target.matches('.add-subcategory')) {
                        const subcategoriesContainer = document.getElementById('subcategories');
                        const newSubcategoryField = `
                            <div class="form-group d-flex">
                                <input type="text" class="form-control subcategory-input" placeholder="Nom de la sous-catégorie">
                                <button type="button" class="btn btn-danger ml-2 remove-subcategory">-</button>
                            </div>
                        `;
                        subcategoriesContainer.insertAdjacentHTML('beforeend', newSubcategoryField);
                    }

                    if (event.target.matches('.remove-subcategory')) {
                        event.target.closest('.form-group').remove();
                    }
                });
        </script>
    </body>
</html>
