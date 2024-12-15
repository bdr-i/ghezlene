<?php
    session_start();
    require '../../config/dbConnect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $productId = filter_var($_POST['id'], FILTER_VALIDATE_INT);
        
        $query = "SELECT * FROM products WHERE id = ?";
        $stmt = mysqli_prepare($link, $query);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $productId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            
            echo json_encode($product);
        } else {
            echo json_encode(['error' => 'Erreur de requête']);
        }
        
        mysqli_close($link);
    }
?>