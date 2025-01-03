<?php
session_start();
require '../../../config/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Vérifier si l'utilisateur est connecté et son ID est dans la session
    if (!isset( $_SESSION['user']['id'])) {
        echo json_encode(['error' => 'Utilisateur non connecté']);
        exit;
    }

    $userId =  $_SESSION['user']['id'];

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
                users.id = ?
            ORDER BY 
                orders.created_at DESC;
            ";

    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'i', $userId); // Associer l'ID utilisateur au paramètre
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        echo json_encode($orders);
    } else {
        echo json_encode(['error' => 'Erreur de requête']);
    }

    mysqli_close($link);
}
?>
