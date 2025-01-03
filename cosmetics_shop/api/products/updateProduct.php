<?php
// Connexion à la base de données
if (file_exists('../../../config/dbConnect.php')) {
    require '../../../config/dbConnect.php';
} else {
    echo json_encode(["success" => false, "message" => "Fichier de configuration introuvable."]);
    die();
}

// Lire les données envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!empty($data['product'])) {
    $product = $data['product'];

    // Validation des champs
    if (isset($product['id'], $product['name'], $product['price'], $product['image_url'], $product['category'], $product['stock'])) {
        $id = intval($product['id']);
        $name = mysqli_real_escape_string($link, $product['name']);
        $price = floatval($product['price']);
        $image_url = mysqli_real_escape_string($link, $product['image_url']);
        $category_id = intval($product['category']); // On suppose que `category` est un ID
        $stock = intval($product['stock']);
        $subcategory_id = isset($product['subcategory_id']) ? intval($product['subcategory_id']) : null;

        // Préparer la requête SQL
        $query = "
            UPDATE products
            SET name = '$name', 
                price = $price, 
                image_url = '$image_url', 
                category_id = $category_id, 
                stock = $stock,
                subcategory_id = " . ($subcategory_id !== null ? $subcategory_id : "NULL") . "
            WHERE id = $id
        ";

        // Exécuter la requête
        if (mysqli_query($link, $query)) {
            echo json_encode(["success" => true, "message" => "Produit mis à jour avec succès."]);
        } else {
            echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour : " . mysqli_error($link)]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Données manquantes ou invalides."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Aucune donnée reçue."]);
}

// Fermer la connexion
mysqli_close($link);
?>
