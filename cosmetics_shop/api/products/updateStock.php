<?php
require '../../../config/dbConnect.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data)) {
    echo json_encode(['status' => 'error', 'message' => 'Données invalides.']);
    die();
}

foreach ($data as $item) {
    $product_id = intval($item['product_id']);
    $quantity = intval($item['quantity']);

    // Vérifier et mettre à jour le stock
    $query = "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?";
    if ($stmt = mysqli_prepare($link, $query)) {
        mysqli_stmt_bind_param($stmt, 'iii', $quantity, $product_id, $quantity);
        if (!mysqli_stmt_execute($stmt) || mysqli_stmt_affected_rows($stmt) === 0) {
            echo json_encode(['status' => 'error', 'message' => "Stock insuffisant pour le produit ID $product_id."]);
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            die();
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
echo json_encode(['status' => 'success', 'message' => 'Stock mis à jour avec succès.']);
?>
