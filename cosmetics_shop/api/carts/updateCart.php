<?php
    session_start();
    require_once '../../../config/dbConnect.php';

    if (isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];

        // Vérifiez si l'utilisateur est connecté
        $userId = isset($_SESSION['user']) ? $_SESSION['user']['id'] : null;

        if (!$userId) {
            echo json_encode(['status' => 'error', 'message' => 'You must be logged in to modify the cart.']);
            exit();
        }

        // Changement de la quantité
        if (isset($_POST['change'])) {
            $change = (int)$_POST['change'];

            // Récupérer la quantité actuelle dans le panier et le stock du produit
            $query = "
                SELECT c.quantity AS cart_quantity, p.stock AS product_stock 
                FROM carts c 
                LEFT JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ? AND c.product_id = ?";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $userId, $productId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if ($row = mysqli_fetch_assoc($result)) {
                $cartQuantity = (int)$row['cart_quantity'];
                $productStock = (int)$row['product_stock'];
                $desiredQuantity = $cartQuantity + $change;

                // Vérifiez si la quantité désirée dépasse le stock disponible
                if ($desiredQuantity > $productStock) {
                    echo json_encode(['status' => 'error', 'message' => 'Not enough stock available.']);
                } elseif ($desiredQuantity > 0) {
                    // Mettre à jour la quantité si elle est supérieure à 0
                    $queryUpdate = "UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?";
                    $stmtUpdate = mysqli_prepare($link, $queryUpdate);
                    mysqli_stmt_bind_param($stmtUpdate, 'iii', $desiredQuantity, $userId, $productId);
                    mysqli_stmt_execute($stmtUpdate);
                    mysqli_stmt_close($stmtUpdate);

                    echo json_encode(['status' => 'success', 'newQuantity' => $desiredQuantity]);
                } else {
                    // Supprimer le produit si la quantité tombe à 0
                    $queryDelete = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
                    $stmtDelete = mysqli_prepare($link, $queryDelete);
                    mysqli_stmt_bind_param($stmtDelete, 'ii', $userId, $productId);
                    mysqli_stmt_execute($stmtDelete);
                    mysqli_stmt_close($stmtDelete);

                    echo json_encode(['status' => 'success', 'message' => 'Product removed from cart.']);
                }
            } else {
                // Si le produit n'est pas trouvé dans le panier
                echo json_encode(['status' => 'error', 'message' => 'Product not found in cart.']);
            }
            mysqli_stmt_close($stmt);
        }

        // Suppression d'un produit
        if (isset($_POST['remove']) && $_POST['remove'] === 'true') {
            $query = "DELETE FROM carts WHERE user_id = ? AND product_id = ?";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $userId, $productId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            echo json_encode(['status' => 'success', 'message' => 'Product removed from cart.']);
        }
    }

    mysqli_close($link);
?>
