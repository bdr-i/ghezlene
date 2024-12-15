<?php
session_start();
require '../../../config/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subcategory'])) {
    // Récupérer et nettoyer l'entrée
    $subcategory = trim($_POST['subcategory']);

    if (empty($subcategory)) {
        echo json_encode(['error' => 'La sous-catégorie est vide']);
        exit;
    }

    // Préparation de la requête SQL
    $query = "SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.description AS product_description,
                p.price,
                p.stock,
                p.image_url,
                c.category_name,
                s.subcategory_name
              FROM 
                products p
              JOIN 
                categories c ON p.category_id = c.id
              LEFT JOIN 
                subcategories s ON p.subcategory_id = s.id
              WHERE 
                s.subcategory_name = ?";

    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        // Lier les paramètres et exécuter la requête
        mysqli_stmt_bind_param($stmt, 's', $subcategory);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Récupération des résultats
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }

        // Libération et fermeture
        mysqli_stmt_close($stmt);
        mysqli_close($link);

        // Retourner les produits en JSON
        if (!empty($products)) {
            echo json_encode($products);
        } else {
            echo json_encode(['error' => 'Aucun produit trouvé pour cette sous-catégorie']);
        }
    } else {
        // Erreur de préparation de la requête
        echo json_encode(['error' => 'Erreur de préparation de la requête SQL']);
        mysqli_close($link);
    }
} else {
    echo json_encode(['error' => 'Requête invalide']);
}
?>
