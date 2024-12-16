<?php
    $errors = [];
    $name = $price = $image = $category = $subcategory = $quantity = "";

    require '../../../config/dbConnect.php';

    // Récupération des catégories et sous-catégories
    $query = "SELECT 
                    c.id AS category_id, 
                    c.category_name, 
                    s.id AS subcategory_id, 
                    s.subcategory_name
                FROM 
                    categories c
                LEFT JOIN 
                    subcategories s 
                ON 
                    c.id = s.category_id;";


    $result = mysqli_query($link, $query);
    $categories = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $category_id = $row['category_id'];
            if (!isset($categories[$category_id])) {
                $categories[$category_id] = [
                    'id' => $category_id,
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

    echo json_encode($categories);
    mysqli_close($link);
?>
