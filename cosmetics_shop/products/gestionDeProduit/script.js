// Charger toutes les catégories pour le formulaire d'ajout de produit
function loadCategories(callback = null) {
    fetch('../../api/categories/getAllCategories.php')
        .then(response => response.json())
        .then(categories => {
            const categorySelect = document.getElementById('editProductCategory');
            console.log(categories);

            // Réinitialiser les options du menu déroulant
            categorySelect.innerHTML = '<option value="">Sélectionner une catégorie</option>';

            // Ajouter les catégories au menu déroulant
            Object.values(categories).forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
            
            // Ajouter un écouteur d'événement pour mettre à jour les sous-catégories
            categorySelect.addEventListener('change', updateSubcategories);

            // Exécuter une fonction de rappel si fournie
            if (callback) callback();
        })
        .catch(error => console.error('Erreur lors du chargement des catégories :', error));
}

// Mettre à jour les sous-catégories dynamiquement
function updateSubcategories() {
    const categoryId = document.getElementById('editProductCategory').value;
    const subcategorySelect = document.getElementById('editProductSubcategory');

    // Réinitialiser les options du menu déroulant des sous-catégories
    subcategorySelect.innerHTML = '<option value="">Sélectionner une sous-catégorie</option>';

    if (categoryId) {
        // Charger les sous-catégories via l'API
        fetch(`../../api/categories/getSubcategoriesByCategory.php?category_id=${categoryId}`)
            .then(response => response.json())
            .then(subcategories => {
                subcategories.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.subcategory_name;
                    subcategorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Erreur lors du chargement des sous-catégories :', error));
    }
}

// Charger les produits
function getAllProducts() {
    fetchProducts().then((products) => {
        renderProducts(products, "Total des produits", products.length);
    });
}

// Afficher les produits
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
                        ${product.stock == 0 ? '<span class="badge badge-danger">En rupture de stock</span>' : ''}
                        <button class="btn btn-danger" onclick="supprimerProduit(${product.id})">Supprimer</button>
                        <button class="btn btn-warning" onclick="modifierProduit(${product.id})">Modifier</button>
                    </div>
                </div>
            </div>`
        )
        .join('');
    document.getElementById("productCount").innerHTML = `<h5>${message} : ${count}</h5>`;
}

// Récupérer les produits via l'API
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

// Modifier un produit
function modifierProduit(productId) {
    fetch(`../../api/products/getProductDetails.php?id=${productId}`)
        .then((response) => response.json())
        .then((productDetails) => {
            const product = productDetails.product;

            // Préremplir les champs du formulaire
            document.getElementById("editProductId").value = product.id;
            document.getElementById("editProductName").value = product.name;
            document.getElementById("editProductPrice").value = product.price;
            document.getElementById("editProductImage").value = product.image_url;
            document.getElementById("editProductStock").value = product.stock;

            // Charger les catégories et sous-catégories
            loadCategories(() => {
                document.getElementById("editProductCategory").value = product.category_id;
                updateSubcategories();

                // Sélectionner la sous-catégorie associée
                setTimeout(() => {
                    document.getElementById("editProductSubcategory").value = product.subcategory_id;
                }, 200); // Attendre un peu pour laisser le temps au menu de se remplir
            });

            // Afficher la modale d'édition
            $("#editProductModal").modal("show");
        })
        .catch((error) => console.error('Erreur lors de la récupération des détails du produit :', error));
}

// Charger les données lors du chargement de la page
document.addEventListener("DOMContentLoaded", () => {
    loadCategories();
    getAllProducts();
});

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

function filtrerProduitsRuptureStock(){
    fetchProducts().then((products) => {
        const productsOutOfStock = products.filter(product => product.stock == 0);
        renderProducts(productsOutOfStock, "Produits en rupture de stock", productsOutOfStock.length);
    });
}

function rechercherParStockMinEtMax() {
    const stockMinInput = document.getElementById("stockMin").value;
    const stockMaxInput = document.getElementById("stockMax").value;

    const stockMin = stockMinInput !== "" ? parseInt(stockMinInput) : null;
    const stockMax = stockMaxInput !== "" ? parseInt(stockMaxInput) : null;

    if ((stockMin !== null && isNaN(stockMin)) || (stockMax !== null && isNaN(stockMax))) {
        alert("Veuillez saisir des valeurs numériques valides pour les stocks min et/ou max.");
        return;
    }

    if (stockMin !== null && stockMax !== null && stockMin > stockMax) {
        alert("Le stock minimum ne peut pas être supérieur au stock maximum.");
        return;
    }

    fetchProducts().then((products) => {
        const productsInStockRange = products.filter(product => {
            if (stockMin !== null && stockMax !== null) {
                return product.stock >= stockMin && product.stock <= stockMax;
            } else if (stockMin !== null) {
                return product.stock >= stockMin;
            } else if (stockMax !== null) {
                return product.stock <= stockMax;
            }
            return true; // Aucun filtre spécifié, retourner tous les produits
        });

        renderProducts(
            productsInStockRange,
            "Produits filtrés selon les critères de stock",
            productsInStockRange.length
        );
    });
}
function rechercherParNom() {
    console.log("Recherche par nom");
    const searchInput = document.getElementById("searchProduct").value.trim().toLowerCase();

    fetchProducts().then((products) => {
        const productsByName = products.filter(product =>
            product.name.toLowerCase().includes(searchInput)
        );
        renderProducts(productsByName, "Produits trouvés par nom", productsByName.length);
    });
}

// Ajout de l'événement
document.getElementById("searchProduct").addEventListener("input", rechercherParNom);
