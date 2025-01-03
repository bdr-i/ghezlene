<?php

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['id'])) {
        $CategoryId = $data['id'];
        require '../../../config/dbConnect.php';

        // Commencer une transaction
        mysqli_begin_transaction($link);

        try {
            // Supprimer les sous-catégories associées à la catégorie
            $deleteSubcategoriesQuery = "DELETE FROM subcategories WHERE category_id = ?";
            if ($stmtSub = mysqli_prepare($link, $deleteSubcategoriesQuery)) {
                mysqli_stmt_bind_param($stmtSub, 'i', $CategoryId);
                if (!mysqli_stmt_execute($stmtSub)) {
                    throw new Exception('Erreur lors de la suppression des sous-catégories.');
                }
                mysqli_stmt_close($stmtSub);
            } else {
                throw new Exception('Erreur de préparation de la requête pour les sous-catégories.');
            }

            // Supprimer la catégorie
            $deleteCategoryQuery = "DELETE FROM categories WHERE id = ?";
            if ($stmtCat = mysqli_prepare($link, $deleteCategoryQuery)) {
                mysqli_stmt_bind_param($stmtCat, 'i', $CategoryId);
                if (!mysqli_stmt_execute($stmtCat)) {
                    throw new Exception('Erreur lors de la suppression de la catégorie.');
                }
                mysqli_stmt_close($stmtCat);
            } else {
                throw new Exception('Erreur de préparation de la requête pour la catégorie.');
            }

            // Valider la transaction
            mysqli_commit($link);

            // Réponse en cas de succès
            echo json_encode(['success' => true, 'message' => 'Catégorie et sous-catégories supprimées avec succès.']);
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            mysqli_rollback($link);

            // Réponse en cas d'erreur
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }

        // Fermer la connexion à la base de données
        mysqli_close($link);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID de la catégorie manquant.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Méthode de requête non valide.']);
}
?>
