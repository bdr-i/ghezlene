<?php
    session_start();

    // Inclure la connexion à la base de données
    if (file_exists('../../../config/dbConnect.php')) {
        require '../../../config/dbConnect.php';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fichier de configuration introuvable']);
        exit;
    }

    // Lire et décoder les données JSON envoyées
    $data = json_decode(file_get_contents('php://input'), true);

    // Vérifier que les données nécessaires sont fournies
    if (isset($data['id'], $data['name'])) {
        $categoryId = intval($data['id']);
        $categoryName = mysqli_real_escape_string($link, $data['name']);

        // Vérifier si la catégorie est associée à des produits
        $queryCheckProducts = "SELECT COUNT(*) FROM products WHERE category_id = ?";
        $stmtCheckProducts = mysqli_prepare($link, $queryCheckProducts);

        if ($stmtCheckProducts) {
            mysqli_stmt_bind_param($stmtCheckProducts, 'i', $categoryId);
            mysqli_stmt_execute($stmtCheckProducts);
            mysqli_stmt_bind_result($stmtCheckProducts, $productCount);
            mysqli_stmt_fetch($stmtCheckProducts);

            if ($productCount > 0) {
                echo json_encode(['status' => 'error', 'message' => 'Impossible de mettre à jour cette catégorie : elle est associée à des produits']);
                mysqli_stmt_close($stmtCheckProducts);
                exit;
            }

            mysqli_stmt_close($stmtCheckProducts);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation pour la vérification des produits']);
            exit;
        }

        // Mettre à jour le nom de la catégorie
        $queryUpdateCategory = "UPDATE categories SET category_name = ? WHERE id = ?";
        $stmtUpdateCategory = mysqli_prepare($link, $queryUpdateCategory);

        if ($stmtUpdateCategory) {
            mysqli_stmt_bind_param($stmtUpdateCategory, 'si', $categoryName, $categoryId);

            if (!mysqli_stmt_execute($stmtUpdateCategory)) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour de la catégorie']);
                mysqli_stmt_close($stmtUpdateCategory);
                exit;
            }

            mysqli_stmt_close($stmtUpdateCategory);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation pour la mise à jour de la catégorie']);
            exit;
        }

        // Réponse de succès
        echo json_encode(['status' => 'success', 'message' => 'Le nom de la catégorie a été mis à jour avec succès']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Données nécessaires manquantes']);
    }

    // Fermer la connexion à la base de données
    mysqli_close($link);
?>
