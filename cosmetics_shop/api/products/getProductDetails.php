<?php
    session_start();
    require '../../../config/dbConnect.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
        // Validation de l'ID du produit
        $productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);

        if (!$productId) {
            echo json_encode(['error' => 'ID produit invalide']);
            exit;
        }

        // Préparation de la requête SQL
        $query = "SELECT 
                p.id, 
                p.name, 
                p.price, 
                p.image_url, 
                p.stock, 
                p.category_id, 
                p.subcategory_id,
                c.category_name AS category_name, 
                s.subcategory_name AS subcategory_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN subcategories s ON p.subcategory_id = s.id
            WHERE p.id = ?;
            ";
        $stmt = mysqli_prepare($link, $query);

        if ($stmt) {
            // Liaison des paramètres et exécution de la requête
            mysqli_stmt_bind_param($stmt, 'i', $productId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $product = mysqli_fetch_assoc($result);

            mysqli_stmt_close($stmt);
            mysqli_close($link);

            if ($product) {
                // Retour des détails du produit sous forme JSON
                echo json_encode([
                    'success' => true,
                    'product' => $product
                ]);
            } else {
                // Produit non trouvé
                echo json_encode(['error' => 'Produit non trouvé']);
            }
        } else {
            // Erreur de préparation de la requête
            echo json_encode(['error' => 'Erreur lors de la préparation de la requête SQL']);
            mysqli_close($link);
        }
    } else {
        // Requête invalide
        echo json_encode(['error' => 'Requête invalide ou ID manquant']);
    }
?>
