<?php
// deleteProduct.php

header('Content-Type: application/json');

// Vérifiez la méthode de requête
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérez les données envoyées via AJAX
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérifiez si un ID de produit a été envoyé
    if (isset($data['id'])) {
        require '../../../config/dbConnect.php';

        $productId = $data['id'];
        $query = "DELETE FROM products WHERE id = ?";

        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $productId);

            // Exécutez la requête et retournez le résultat
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Produit supprimé avec succès.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du produit.']);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID du produit manquant.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non valide.']);
}
?>