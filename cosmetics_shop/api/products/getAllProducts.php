<?php
    session_start();
    require '../../../config/dbConnect.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT * FROM products";
        $stmt = mysqli_prepare($link, $query);
        
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $products = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);
            if (empty($products)) {
                echo json_encode(['status' => 'error', 'message' => 'Aucun produit trouvé.']);
                exit();
            } 
            echo json_encode($products);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de requête']);
        }
        
        mysqli_close($link);
    }
    ?>