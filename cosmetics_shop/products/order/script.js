$(document).ready(function () {
    // Récupérer les commandes via l'API
    $.ajax({
        url: '../../api/orders/getAllOrders.php',
        method: 'GET',
        success: function (data) {
            // Vérifier si des commandes sont retournées
            if (data && data.length > 0) {
                console.log(data);
                if (typeof data === 'string') {
                    data = JSON.parse(data); // Parse la chaîne JSON
                }
                $('#ordersTableBody').empty();
                data.forEach(function (commande) {
                    $('#ordersTableBody').append(`
                        <tr>
                            <td>${commande.order_id}</td>
                            <td>${commande.client_name}</td>
                            <td>${parseFloat(commande.total_price).toFixed(2)}</td>
                            <td>${commande.created_at}</td>
                            <td>
                                <button class="btn btn-info view-details" data-id="${commande.order_id}">Voir les détails</button>
                            </td>
                        </tr>
                    `);
                });
            } else {
                $('#ordersTableBody').empty();
                $('#ordersTableBody').append(`
                    <tr>
                        <td colspan="5" class="text-center">Aucune commande trouvée</td>
                    </tr>
                `);
            }
        },
        error: function () {
            $('#ordersTableBody').empty();
            $('#ordersTableBody').append(`
                <tr>
                    <td colspan="5" class="text-center">Erreur lors du chargement des commandes</td>
                </tr>
            `);
        },
    });

    // Fonction pour afficher les détails de la commande dans le modal
    $(document).on('click', '.view-details', function () {
        var orderId = $(this).data('id');
        
        // Récupérer les détails de la commande via l'API
        $.ajax({
            url: `../../api/orders/getOrderDetails.php`,
            method: 'GET',
            data: { order_id: orderId },
            success: function (data) {
                data = JSON.parse(data);
                console.log(data);
                console.log(data.items);
                console.log(data.order);
                // Vérifier si la réponse contient bien les données nécessaires
                if (data && data.order && data.items) {

                    var order = data.order;
                    var items = data.items;

                    // Remplir le modal avec les données de la commande
                    $('#orderId').text(order.order_id); // Utilisation de `order_id` qui est le nom correct dans la réponse
                    $('#clientName').text(order.client_name);
                    $('#orderDate').text(order.created_at);
                    $('#orderTotal').text(parseFloat(order.total_price).toFixed(2));
                    $('#orderStatus').text(order.status);

                    // Afficher les articles de la commande
                    $('#orderItemsList').empty();
                    items.forEach(function (item) {
                        $('#orderItemsList').append(`
                            <li>${item.quantity} x ${item.product_name} à ${parseFloat(item.price).toFixed(2)}€</li>
                        `);
                    });

                    // Ouvrir le modal avec les détails de la commande
                    $('#orderDetailsModal').modal('show');
                } else {
                    alert("Erreur lors de la récupération des détails de la commande.");
                }
            },
            error: function () {
                alert("Erreur lors de la récupération des détails de la commande.");
            }
        });
    });
});