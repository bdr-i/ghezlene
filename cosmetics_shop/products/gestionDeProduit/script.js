// Récupérer tous les produits
function getAllProducts() {
    fetchProducts().then((products) => {
        renderProducts(products, "Total des produits", products.length);
    });
}

// Fonction générique pour afficher les produits
function renderProducts(products, message, count) {
    const productList = document.getElementById("productList");
    productList.innerHTML = products
        .map(
            (product) => `
            <div class="col-md-4 mb-4" id="product-${product.id}">
                <div class="card ${product.stock == 0 ? 'border-danger' : ''}">
                    <img src="../../../images/${product.image_url}" class="card-img-top" alt="${product.name}">
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="card-text">Prix: €${product.price}</p>
                        <p class="card-text">Quantité: ${product.stock}</p>
                        ${
                            product.stock == 0
                                ? '<span class="badge badge-danger">Out of Stock</span>'
                                : ''
                        }
                        <button class="btn btn-danger" onclick="supprimerProduit(${product.id})">Supprimer</button>
                        <button class="btn btn-warning" onclick="modifierProduit(${product.id})">Modifier</button>
                    </div>
                </div>
            </div>`
        )
        .join('');
    document.getElementById("productCount").innerHTML = `<h5>${message} : ${count}</h5>`;
}

// Appel d'API générique pour récupérer les produits
function fetchProducts() {
    return fetch('../../api/products/getAllProducts.php', { method: 'GET' })
        .then((response) => response.json())
        .catch((error) => {
            console.error('Erreur lors de la récupération des produits :', error);
            return [];
        });
}

// Supprimer un produit
function supprimerProduit(productId) {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce produit ?")) {
        fetch('../../api/products/deleteProduct.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: productId }),
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    document.getElementById(`product-${productId}`).remove();
                    const count = document.querySelectorAll("#productList > div").length;
                    document.getElementById("productCount").innerHTML = `<h5>Total des produits : ${count}</h5>`;
                } else {
                    alert(data.message);
                }
            })
            .catch((error) => console.error('Erreur lors de la suppression du produit :', error));
    }
}

// Modifier un produit (affiche une modale avec les données actuelles)
function modifierProduit(productId) {
    fetch(`../../api/products/getProductDetails.php?id=${productId}`)
        .then((response) => response.json())
        .then((productDetails) => {
            const product = productDetails.product;

            // Préremplir les champs du formulaire
            document.getElementById("editProductId").value = productId;
            document.getElementById("editProductName").value = product.name;
            document.getElementById("editProductPrice").value = product.price;
            document.getElementById("editProductImage").value = product.image_url;
            document.getElementById("editProductStock").value = product.stock;

            // Charger les catégories et sous-catégories
            getAllCategories(() => {
                document.getElementById("editProductCategory").value = product.category_id;
                updateSubcategories(product.category_id, product.subcategory_id);
            });

            // Afficher la modale
            $("#editProductModal").modal("show");
        })
        .catch((error) => console.error('Erreur lors de la récupération des détails du produit :', error));
}

// Récupérer toutes les catégories
function getAllCategories(callback) {
    fetch('../../api/categories/getAllCategories.php')
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
            if (callback) callback();
        })
        .catch((error) => console.error('Erreur lors de la récupération des catégories :', error));
}

// Mettre à jour les sous-catégories
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

    if (!updatedProduct.name || isNaN(updatedProduct.price) || isNaN(updatedProduct.category)) {
        alert("Veuillez remplir correctement tous les champs obligatoires.");
        return;
    }

    fetch("../../api/products/updateProduct.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ product: updatedProduct }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert(data.message);
                $("#editProductModal").modal("hide");
                getAllProducts();
            } else {
                alert(data.message);
            }
        })
        .catch((error) => console.error('Erreur lors de la modification du produit :', error));
});

// Filtrer les produits par stock
function filtrerProduits(condition, message) {
    fetchProducts().then((products) => {
        const produitsFiltres = products.filter(condition);
        renderProducts(produitsFiltres, message, produitsFiltres.length);
    });
}

function filtrerProduitsRuptureStock() {
    filtrerProduits((product) => product.stock == 0, "Total des produits en rupture");
}

function rechercherParStock() {
    const stockMin = parseInt(document.getElementById("stockSearch").value, 10);
    if (isNaN(stockMin)) {
        alert("Veuillez entrer une valeur valide pour le stock minimum.");
        return;
    }
    filtrerProduits((product) => product.stock >= stockMin, "Produits trouvés");
}

function rechercherParMaxStock() {
    const stockMax = parseInt(document.getElementById("maxStockSearch").value, 10);
    if (isNaN(stockMax)) {
        alert("Veuillez entrer une valeur valide pour le stock maximum.");
        return;
    }
    filtrerProduits((product) => product.stock <= stockMax, "Produits trouvés");
}

function rechercherParIntervalleStock() {
    const stockMin = parseInt(document.getElementById("minStockSearch").value, 10);
    const stockMax = parseInt(document.getElementById("maxStockSearch").value, 10);
    if (isNaN(stockMin) || isNaN(stockMax)) {
        alert("Veuillez entrer des valeurs valides pour le stock minimum et maximum.");
        return;
    }
    filtrerProduits(
        (product) => product.stock >= stockMin && product.stock <= stockMax,
        "Produits trouvés"
    );
}

// Charger les données au chargement de la page
document.addEventListener("DOMContentLoaded", function () {
    getAllProducts();
    getAllCategories();
});

// Gestion de la barre de recherche
document.addEventListener("DOMContentLoaded", () => {
    // Sélection des éléments HTML
    const searchInput = document.querySelector(".form-control[type='search']");
    const productContainer = document.querySelector("#productList");

    // Vérification des éléments dans le DOM
    if (!searchInput || !productContainer) {
        console.error("La barre de recherche ou le conteneur des produits est introuvable dans le DOM.");
        return;
    }

    // Ajout d'un écouteur d'événements sur la barre de recherche
    searchInput.addEventListener("input", (event) => {
        const searchTerm = event.target.value.toLowerCase().trim(); // Récupère et nettoie la recherche

        // Sélection dynamique des produits après chaque changement dans le DOM
        const products = productContainer.querySelectorAll(".col-md-4"); // Contient chaque produit

        let visibleProductsCount = 0; // Compte les produits visibles

        // Parcourt les produits et filtre ceux qui correspondent au terme de recherche
        products.forEach(product => {
            const productName = product.querySelector(".card-title")?.textContent.toLowerCase() || ""; // Récupère le nom
            if (productName.includes(searchTerm)) {
                product.style.display = "block"; // Affiche le produit
                visibleProductsCount++;
            } else {
                product.style.display = "none"; // Cache le produit
            }
        });

        // Gestion du message "Aucun produit trouvé"
        let noResultsMessage = productContainer.querySelector(".no-results");

        if (visibleProductsCount === 0) {
            if (!noResultsMessage) {
                noResultsMessage = document.createElement("p");
                noResultsMessage.textContent = "Aucun produit trouvé.";
                noResultsMessage.classList.add("no-results", "text-danger");
                noResultsMessage.style.textAlign = "center";
                productContainer.appendChild(noResultsMessage);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove(); // Supprime le message si des produits sont visibles
        }
    });
});
