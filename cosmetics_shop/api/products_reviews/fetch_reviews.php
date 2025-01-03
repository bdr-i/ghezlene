<?php
    require '../../../config/dbConnect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);

        $reviewQuery = "SELECT pr.*, u.identifiant 
                        FROM product_reviews pr 
                        JOIN users u ON pr.user_id = u.id 
                        WHERE pr.product_id = ? 
                        ORDER BY pr.created_at DESC";
        $stmt = mysqli_prepare($link, $reviewQuery);
        mysqli_stmt_bind_param($stmt, 'i', $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);

        echo json_encode([
            'status' => 'success',
            'comments' => $reviews
        ]);
        
    }
?>