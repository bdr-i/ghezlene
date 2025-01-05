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
if (isset($data['id'], $data['name'], $data['subcategories'])) {
    $categoryId = intval($data['id']);
    $categoryName = mysqli_real_escape_string($link, $data['name']);
    $subcategories = $data['subcategories']; // Liste des nouvelles sous-catégories

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

    // Supprimer les sous-catégories existantes pour cette catégorie
    $queryDeleteSubcategories = "DELETE FROM subcategories WHERE category_id = ?";
    $stmtDeleteSubcategories = mysqli_prepare($link, $queryDeleteSubcategories);

    if ($stmtDeleteSubcategories) {
        mysqli_stmt_bind_param($stmtDeleteSubcategories, 'i', $categoryId);

        if (!mysqli_stmt_execute($stmtDeleteSubcategories)) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression des sous-catégories existantes']);
            mysqli_stmt_close($stmtDeleteSubcategories);
            exit;
        }

        mysqli_stmt_close($stmtDeleteSubcategories);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation pour la suppression des sous-catégories']);
        exit;
    }

    // Ajouter les nouvelles sous-catégories
    $queryInsertSubcategory = "INSERT INTO subcategories (subcategory_name, category_id) VALUES (?, ?)";
    $stmtInsertSubcategory = mysqli_prepare($link, $queryInsertSubcategory);

    if ($stmtInsertSubcategory) {
        foreach ($subcategories as $subcategoryName) {
            $subcategoryNameEscaped = mysqli_real_escape_string($link, $subcategoryName);

            mysqli_stmt_bind_param($stmtInsertSubcategory, 'si', $subcategoryNameEscaped, $categoryId);

            if (!mysqli_stmt_execute($stmtInsertSubcategory)) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout des sous-catégories']);
                mysqli_stmt_close($stmtInsertSubcategory);
                exit;
            }
        }

        mysqli_stmt_close($stmtInsertSubcategory);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation pour l\'ajout des sous-catégories']);
        exit;
    }

    // Réponse de succès
    echo json_encode(['status' => 'success', 'message' => 'La catégorie et les sous-catégories ont été mises à jour avec succès']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Données nécessaires manquantes']);
}

// Fermer la connexion à la base de données
mysqli_close($link);
?>
