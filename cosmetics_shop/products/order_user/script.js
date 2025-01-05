$(document).ready(function () {
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
    // Charger les informations de l'utilisateur
    $.ajax({
        url: '../../api/session/getSession.php',
        method: 'GET',
        success: function (data) {
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }

            if (data.status === 'success') {
                const user = data.user;
                const id = user.id;
                const role = user.role;
                const name = user.name;
                loadOrders('tous'); // Charger toutes les commandes pour l'utilisateur
            } else {
                alert('Erreur lors du chargement des informations de l\'utilisateur');
            }
        },
        error: function () {
            alert('Erreur lors du chargement des informations de l\'utilisateur');
        }
    });

    // Charger les commandes et gérer les statuts
    function loadOrders(status = 'tous') {
        $.ajax({
            url: '../../api/orders/getAllOrdersForOneUser.php',
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
                                <td>${parseFloat(commande.total_price).toFixed(2)}€</td>
                                <td>${commande.created_at}</td>
                                <td>${commande.status}</td>
                                <td>
                                    <button class="btn btn-info view-details" data-id="${commande.order_id}">Voir les détails</button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#ordersTableBody').append(`
                        <tr>
                            <td colspan="4" class="text-center">Aucune commande trouvée</td>
                        </tr>
                    `);
                }

                // Ajouter les événements pour les boutons
                $('.view-details').off('click').on('click', function () {
                    const orderId = $(this).data('id');
                    loadOrderDetails(orderId);
                });
            },
            error: function () {
                $('#ordersTableBody').empty().append(`
                    <tr>
                        <td colspan="4" class="text-center">Erreur lors du chargement des commandes</td>
                    </tr>
                `);
            }
        });
    }

    // Mettre à jour le statut d'une commande
    function updateOrderStatus(orderId, newStatus) {
        $.ajax({
            url: '../../api/orders/updateOrder.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ order_id: orderId, status: newStatus }),
            success: function (response) {
                try {
                    const jsonResponse = typeof response === 'string' ? JSON.parse(response) : response;

                    if (jsonResponse.message) {
                        alert(jsonResponse.message);
                    } else {
                        alert('Statut mis à jour avec succès.');
                    }

                    $('#updateStatusModal').modal('hide');
                    loadOrders();
                } catch (e) {
                    console.error('Erreur lors de l\'analyse de la réponse JSON:', e);
                    alert('Une erreur inattendue s\'est produite.');
                }
            },
            error: function (xhr) {
                let errorMessage = 'Erreur lors de la mise à jour du statut.';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    console.error('Erreur lors de l\'analyse de la réponse d\'erreur JSON:', e);
                }
                alert(errorMessage);
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

                    $('#orderId').text(order.order_id);
                    $('#clientName').text(order.client_name);
                    $('#orderDate').text(order.created_at);
                    $('#orderTotal').text(parseFloat(order.total_price).toFixed(2));
                    $('#orderStatus').text(order.status);

                    $('#orderItemsList').empty();
                    items.forEach(item => {
                        $('#orderItemsList').append(`
                            <li>${item.quantity} x ${item.product_name} à ${parseFloat(item.price).toFixed(2)}€</li>
                        `);
                    });

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
});
