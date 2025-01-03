<?php
session_start();
require '../../../config/dbConnect.php'; // Assurez-vous que ce fichier contient la bonne connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Requête pour récupérer les détails de la commande
    $query = "SELECT 
                orders.id AS order_id, 
                CONCAT(users.first_name, ' ', users.last_name) AS client_name, 
                orders.total_price, 
                orders.created_at, 
                orders.status
              FROM 
                orders
              JOIN 
                users 
              ON 
                orders.user_id = users.id
              WHERE 
                orders.id = ?";
    
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        // Lier la variable à la requête préparée
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        
        // Exécuter la requête
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        // Récupérer les résultats de la commande
        $order = mysqli_fetch_assoc($result);

        // Fermer la requête préparée
        mysqli_stmt_close($stmt);

        if ($order) {
            // Requête pour récupérer les articles de la commande
            $query_items = "SELECT 
                                order_items.quantity, 
                                products.name AS product_name, 
                                order_items.price
                            FROM 
                                order_items
                            JOIN 
                                products ON order_items.product_id = products.id
                            WHERE 
                                order_items.order_id = ?";
            
            $stmt_items = mysqli_prepare($link, $query_items);
            
            if ($stmt_items) {
                // Lier la variable à la requête préparée
                mysqli_stmt_bind_param($stmt_items, "i", $order_id);
                
                // Exécuter la requête
                mysqli_stmt_execute($stmt_items);
                $result_items = mysqli_stmt_get_result($stmt_items);
                
                // Récupérer les résultats des articles
                $items = mysqli_fetch_all($result_items, MYSQLI_ASSOC);
                
                // Fermer la requête préparée
                mysqli_stmt_close($stmt_items);

                // Retourner les détails de la commande et les articles sous forme de JSON
                echo json_encode(['order' => $order, 'items' => $items]);
            } else {
                echo json_encode(['error' => 'Erreur de récupération des articles']);
            }
        } else {
            echo json_encode(['error' => 'Commande introuvable']);
        }
    } else {
        echo json_encode(['error' => 'Erreur de requête']);
    }

    // Fermer la connexion à la base de données
    mysqli_close($link);
} else {
    echo json_encode(['error' => 'ID de commande manquant ou méthode incorrecte']);
}
?>
