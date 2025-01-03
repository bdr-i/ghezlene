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

        //supprimer les commentaires associés au produit
        $query = "DELETE FROM product_reviews WHERE product_id = ?";
        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $data['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        //supprimer les produits associés au panier
        $query = "DELETE FROM carts WHERE product_id = ?";
        if ($stmt = mysqli_prepare($link, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $data['id']);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

        // supprimer le produit de la base de données

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