$(document).ready(function () {
    // Charger les commandes et gérer les statuts
    function loadOrders(status = 'tous') {
        $.ajax({
            url: '../../api/orders/getAllOrders.php',
            method: 'GET',
            success: function (data) {
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }

                // Filtrer les commandes selon le statut sélectionné
                const filteredOrders = status === 'tous'
                    ? data
                    : data.filter(order => order.status.toLowerCase() === status.toLowerCase());

                // Mise à jour de la table des commandes
                $('#ordersTableBody').empty();
                if (filteredOrders.length > 0) {
                    filteredOrders.forEach(function (commande) {
                        $('#ordersTableBody').append(`
                            <tr>
                                <td>${commande.order_id}</td>
                                <td>${commande.client_name}</td>
                                <td>${parseFloat(commande.total_price).toFixed(2)}</td>
                                <td>${commande.created_at}</td>
                                <td>${commande.status}</td>
                                <td>
                                    <button class="btn btn-info view-details" data-id="${commande.order_id}">Voir les détails</button>
                                    <button class="btn btn-primary update-status" data-id="${commande.order_id}" data-status="${commande.status}">Modifier le statut</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#ordersTableBody').append(`
                        <tr>
                            <td colspan="6" class="text-center">Aucune commande trouvée</td>
                        </tr>
                    `);
                }
            },
            error: function () {
                $('#ordersTableBody').empty().append(`
                    <tr>
                        <td colspan="6" class="text-center">Erreur lors du chargement des commandes</td>
                    </tr>
                `);
            }
        });
    }

    // Afficher les détails d'une commande
    function loadOrderDetails(orderId) {
        $.ajax({
            url: '../../api/orders/getOrderDetails.php',
            method: 'GET',
            data: { order_id: orderId },
            success: function (data) {
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }

                if (data && data.order && data.items) {
                    const order = data.order;
                    const items = data.items;

                    // Remplir les informations du modal
                    $('#orderId').text(order.order_id);
                    $('#clientName').text(order.client_name);
                    $('#orderDate').text(order.created_at);
                    $('#orderTotal').text(parseFloat(order.total_price).toFixed(2));
                    $('#orderStatus').text(order.status);

                    // Liste des articles
                    $('#orderItemsList').empty();
                    items.forEach(item => {
                        $('#orderItemsList').append(`
                            <li>${item.quantity} x ${item.product_name} à ${parseFloat(item.price).toFixed(2)}€</li>
                        `);
                    });

                    // Afficher le modal
                    $('#orderDetailsModal').modal('show');
                } else {
                    alert('Erreur lors de la récupération des détails de la commande.');
                }
            },
            error: function () {
                alert('Erreur lors de la récupération des détails de la commande.');
            }
        });
    }

    function updateOrderStatus(orderId, newStatus) {
        console.log("Order ID:", orderId, "New Status:", newStatus);
        
        $.ajax({
            url: '../../api/orders/updateOrder.php', // Chemin de l'API
            method: 'POST',
            contentType: 'application/json', // Spécifie que les données envoyées sont au format JSON
            data: JSON.stringify({ order_id: orderId, status: newStatus }), // Convertir les données en JSON avec des noms de clés correspondants à l'API
            success: function (response) {
                try {
                    console.log("Réponse de l'API :", response);
                    const jsonResponse = JSON.parse(response); // Essayer de parser la réponse si ce n'est pas déjà un objet JSON
                    console.log("Réponse de l'API :", jsonResponse);
    
                    if (jsonResponse.message) {
                        alert(jsonResponse.message);
                    } else {
                        alert('Statut mis à jour avec succès.');
                    }
    
                    // Fermer le modal après succès
                    $('#updateStatusModal').modal('hide');
    
                    // Recharger la liste des commandes
                    loadOrders();
                } catch (e) {
                    console.error("Erreur lors de l'analyse de la réponse JSON :", e);
                    alert('Une erreur inattendue s\'est produite.');
                }
            },
            error: function (xhr, status, error) {
                console.error("Erreur AJAX :", xhr.responseText, status, error);
                let errorMessage = "Erreur lors de la mise à jour du statut.";
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    console.error("Erreur lors de l'analyse de la réponse d'erreur JSON :", e);
                }
    
                alert(errorMessage);
            }
        });
    }
    
    

    // Gestion des événements
    function setupEventHandlers() {
        // Voir les détails d'une commande
        $(document).on('click', '.view-details', function () {
            const orderId = $(this).data('id');
            loadOrderDetails(orderId);
        });

        // Afficher le modal pour modifier le statut
        $(document).on('click', '.update-status', function () {
            const orderId = $(this).data('id');
            const currentStatus = $(this).data('status');
            $('#updateOrderId').text(orderId);
            $('#newStatus').val(currentStatus);
            $('#updateStatusModal').modal('show');
        });

        // Mettre à jour le statut via le bouton de confirmation
        $('#updateStatusBtn').on('click', function () {
            const orderId = $('#updateOrderId').text();
            const newStatus = $('#newStatus').val();
            updateOrderStatus(orderId, newStatus);
        });

        // Filtrer les commandes par statut
        $('#statusFilter').on('change', function () {
            const selectedStatus = $(this).val();
            loadOrders(selectedStatus);
        });
    }

    // Initialisation
    function init() {
        loadOrders(); // Charger toutes les commandes au démarrage
        setupEventHandlers(); // Configurer les événements
    }

    init();
});
