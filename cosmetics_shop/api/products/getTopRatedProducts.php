<?php
    $errors = [];

    require '../../../config/dbConnect.php';

    // Récupération des produits les mieux notés
    $query = "SELECT 
                    p.id AS product_id, 
                    p.name, 
                    p.price, 
                    p.image_url, 
                    AVG(r.rating) AS average_rating
                FROM 
                    products p
                LEFT JOIN 
                    product_reviews r ON p.id = r.product_id
                GROUP BY 
                    p.id
                HAVING 
                    AVG(r.rating) IS NOT NULL
                ORDER BY 
                    average_rating DESC
                LIMIT 5"; // Limite à 5 produits les mieux notés

    $result = mysqli_query($link, $query);
    $bestRatedProducts = [];

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $bestRatedProducts[] = [
                'id' => $row['product_id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image_url' => $row['image_url'],
                'average_rating' => $row['average_rating']
            ];
        }
    }

    echo json_encode($bestRatedProducts);
    mysqli_close($link);
?>
