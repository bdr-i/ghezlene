<?php
    $errors = [];
    $name = $price = $image = $category = $subcategory = $quantity = "";

    require '../../../config/dbConnect.php';
    // Récupération des catégories et sous-catégories
    $query = "SELECT c.id AS category_id, c.category_name, 
    s.id AS subcategory_id, s.subcategory_name 
    FROM categories c 
    LEFT JOIN subcategories s ON c.id = s.category_id";

    $result = mysqli_query($link, $query);
    $categories = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
        $category_id = $row['category_id'];
        if (!isset($categories[$category_id])) {
            $categories[$category_id] = [
            'name' => $row['category_name'],
            'subcategories' => []
            ];
        }
        if (!empty($row['subcategory_id'])) {
            $categories[$category_id]['subcategories'][] = [
            'id' => $row['subcategory_id'],
            'name' => $row['subcategory_name']
            ];
        }
    }
    }


    // Traitement uniquement côté serveur pour l'insertion
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['productName']);
        $price = trim($_POST['productPrice']);
        $category = trim($_POST['productCategory']);
        $subcategory = trim($_POST['productSubcategory']);
        $quantity = trim($_POST['productQuantity']);
        $description = "Aucune description disponible";

        // Gestion de l'image
        if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === 0) {
            $image = basename($_FILES['productImage']['name']);
            $uploadDir = '../../../images/';
            $uploadFile = $uploadDir . $image;

            if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $uploadFile)) {
                $errors['productImage'] = "Erreur lors du téléchargement de l'image.";
            }
        }

        // Si aucune erreur liée au téléchargement d'image, insertion dans la base
        if (empty($errors)) {
            require '../../../config/dbConnect.php';
            
            $sql = "INSERT INTO products (name, description, price, stock, image_url, category_id, subcategory_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($link, $sql)) {
                mysqli_stmt_bind_param($stmt, 'ssdisss', $name, $description, $price, $quantity, $image, $category, $subcategory);
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: ../../products/gestionDeProduit/liste_produits.php');
                    exit();
                } else {
                    $errors['general'] = "Erreur lors de l'ajout du produit. Veuillez réessayer.";
                }
                mysqli_stmt_close($stmt);
            }
            mysqli_close($link);
        }
    }
?>