<?php
    $errors = [];
    $name = $price = $image = $category = $subcategory = $quantity = "";

    require '../../../config/dbConnect.php';

    // Récupération des commandes
    $query = "SELECT * FROM carts";

    $result = mysqli_query($link, $query);
    $categories = [];

    echo json_encode($categories);
    mysqli_close($link);
?>
