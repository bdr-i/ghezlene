<?php
$errors = [];

require '../../../config/dbConnect.php';

// Récupération des produits les plus vendus
$query = "SELECT 
                p.id AS product_id, 
                p.name, 
                p.price, 
                p.image_url, 
                SUM(oi.quantity) AS total_sales
            FROM 
                products p
            LEFT JOIN 
                order_items oi ON p.id = oi.product_id
            GROUP BY 
                p.id
            ORDER BY 
                total_sales DESC
            LIMIT 5"; // Limite à 5 produits les plus vendus

$result = mysqli_query($link, $query);
$topSellingProducts = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $topSellingProducts[] = [
            'id' => $row['product_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'image_url' => $row['image_url'],
            'total_sales' => $row['total_sales']
        ];
    }
}

echo json_encode($topSellingProducts);
mysqli_close($link);
?>
