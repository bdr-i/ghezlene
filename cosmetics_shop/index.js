    // Initialiser les événements sur les boutons "Ajouter au panier"
    handleAddToCartClick();

    $(document).ready(function () {
        // Fonction pour afficher les étoiles en fonction de la note
        function displayStars(rating, container) {
            const maxStars = 5;
            let starsHTML = "";

            for (let i = 1; i <= maxStars; i++) {
                if (i <= Math.floor(rating)) {
                    starsHTML += '<span class="star filled">&#9733;</span>'; // Étoile pleine
                } else if (i - rating <= 0.5) {
                    starsHTML += '<span class="star half-filled">&#9734;</span>'; // Étoile demi-pleine
                } else {
                    starsHTML += '<span class="star">&#9734;</span>'; // Étoile vide
                }
            }

            container.innerHTML = starsHTML;
        }

        // Requête pour récupérer les avis des produits
        $.ajax({
            url: "api/products_reviews/getAllReviews.php",
            method: "GET",
            dataType: "json", // Attendez une réponse JSON du serveur
            success: function (response) {
                if (response.status === "success") {
                    // Liste des avis par produit
                    const reviews = response.comments; // Assurez-vous que cette structure contient les avis.

                    // Calculer la note moyenne pour chaque produit
                    const productRatings = {}; // Stocker les moyennes des produits
                    reviews.forEach(review => {
                        const productId = review.product_id;
                        const rating = parseFloat(review.rating);

                        if (!productRatings[productId]) {
                            productRatings[productId] = {
                                total: 0,
                                count: 0
                            };
                        }

                        productRatings[productId].total += rating;
                        productRatings[productId].count += 1;
                    });

                    // Ajouter les étoiles aux produits affichés
                    $('.product-item').each(function () {
                        const productId = $(this).find('.add-to-cart').data('product-id');
                        if (productRatings[productId]) {
                            const averageRating =
                                productRatings[productId].total / productRatings[productId].count;
                            const starContainer = $(this).find('#starRating')[0];

                            if (starContainer) {
                                displayStars(averageRating, starContainer);
                            }
                        }
                    });
                } else {
                    alert(response.message || "Une erreur s'est produite.");
                }
            },
            error: function () {
                alert("Une erreur est survenue lors de la récupération des avis.");
            },
        });
    });

    // Fonction pour mettre à jour les produits affichés dynamiquement
    function updateProducts(products) {
        // Supprimer les produits existants dans la section contenant les produits
        $('.row').empty();

        // Ajouter les nouveaux produits
        products.forEach(product => {
            const productCard = `
                <div class="col-md-4 product-card" data-category="${product.subcategory}">
                    <div class="card">
                        <img src="../images/${product.image_url}" class="card-img-top" alt="${product.product_name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.product_name}</h5>
                            <p class="card-text">${product.product_description}</p>
                            <p class="card-text">€ ${product.price}</p>
                            <p class="card-text">Stock: ${product.stock}</p>
                            <button 
                                class="add-to-cart btn btn-primary" 
                                data-product-id="${product.product_id}" 
                                data-quantity="${product.stock}">
                                Ajouter au panier
                            </button>
                            <form action="products/details.php" method="POST">
                                <input type="hidden" name="id" value="${product.product_id}">
                                <button type="submit" class="btn btn-link">Voir plus</button>
                            </form>
                        </div>
                    </div>
                </div>
            `;
            $('.row').append(productCard);
        });

        // Réinitialiser les événements pour les nouveaux boutons
        handleAddToCartClick();
    }


    $(document).ready(function () {
    // Afficher les sous-catégories dynamiquement
    $('#productsDropdown').on('click', function () {
        // Vérifiez si le menu est déjà ouvert
        if ($(this).attr('aria-expanded') === 'false') {
            $('.dropdown-menu').addClass('show');
        }
    });

    // Gestion des clics sur les catégories et sous-catégories
    $(document).on('click', '.dropdown-item.filter', function (e) {
        e.preventDefault();
        const selectedCategory = $(this).data('category');
        if(selectedCategory === 'all') {
            //Rafraichir la page
            window.location.reload();
            return;
        }
        $.ajax({
            url: 'products/filter/fetch_products.php',
            type: 'POST',
            data: {
                subcategory: selectedCategory
            },
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    updateProducts(res);
                } catch (error) {
                    alert('Erreur lors de la récupération des produits.');
                    console.error(error);
                }
            },
            error: function () {
                alert('Erreur lors de la requête au serveur.');
            }
        });
    });
});

     
    // JavaScript pour mettre à jour les produits en fonction de la recherche
document.addEventListener("DOMContentLoaded", () => {
    // Sélection des éléments HTML
    const searchInput = document.querySelector(".form-control[type='search']");
    const productContainer = document.querySelector("#produits"); // Div contenant les produits
    const products = document.querySelectorAll(".product-item"); // Chaque produit a la classe product-item
    // Vérification de la présence des éléments dans le DOM
    if (!searchInput || !productContainer || products.length === 0) {
        console.warn("Les éléments nécessaires pour la recherche ne sont pas présents dans le DOM.");
        return;
    }

    // Ajout d'un écouteur d'événements sur la barre de recherche
    searchInput.addEventListener("input", (event) => {
        const searchTerm = event.target.value.toLowerCase().trim(); // Récupère et nettoie la recherche

        let visibleProductsCount = 0; // Compte les produits visibles

        // Parcourt les produits et filtre ceux qui correspondent au terme de recherche
        products.forEach(product => {
            const productName = product.querySelector(".product-name")?.textContent.toLowerCase() || ""; // Nom du produit

            if (productName.includes(searchTerm)) {
                product.style.display = "block"; // Affiche le produit
                visibleProductsCount++;
            } else {
                product.style.display = "none"; // Cache le produit
            }
        });

        // Affiche un message si aucun produit n'est trouvé
        const noResultsMessage = productContainer.querySelector(".no-results");

        if (visibleProductsCount === 0) {
            if (!noResultsMessage) {
                const message = document.createElement("p");
                message.textContent = "Aucun produit trouvé.";
                message.classList.add("no-results");
                message.style.textAlign = "center";
                productContainer.appendChild(message);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove(); // Supprime le message si des produits deviennent visibles
        }
    });
});