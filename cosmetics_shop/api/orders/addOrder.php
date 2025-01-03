<?php
    session_start();
    header('Content-Type: application/json');
    require '../../../config/dbConnect.php';

    $data = json_decode(file_get_contents("php://input"), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['user_id'], $data['total_price'], $data['items'])) {
        $user_id = $data['user_id'];
        $total_price = $data['total_price'];
        $items = $data['items'];

        mysqli_begin_transaction($link);
        try {
            // Insérer la commande
            $stmt = mysqli_prepare($link, "INSERT INTO orders (user_id, total_price, status, created_at) VALUES (?, ?, 'pending', NOW())");
            mysqli_stmt_bind_param($stmt, "id", $user_id, $total_price);
            mysqli_stmt_execute($stmt);
            $order_id = mysqli_insert_id($link);

            // Insérer les articles
            $stmt_items = mysqli_prepare($link, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                mysqli_stmt_bind_param($stmt_items, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                mysqli_stmt_execute($stmt_items);
            }

            mysqli_commit($link);
            echo json_encode(['status' => 'success', 'order_id' => $order_id]);
        } catch (Exception $e) {
            mysqli_rollback($link);
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la validation de la commande.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Données manquantes ou invalides.']);
    }
    mysqli_close($link);
?>
