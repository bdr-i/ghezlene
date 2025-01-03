<?php
require '../../../config/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!$link) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur de connexion à la base de données'
        ]);
        exit();
    }

    $reviewQuery = "
        SELECT pr.*, u.identifiant 
        FROM product_reviews pr 
        JOIN users u ON pr.user_id = u.id
    ";
    
    $stmt = mysqli_prepare($link, $reviewQuery);

    if (!$stmt) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de la préparation de la requête'
        ]);
        exit();
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result === false) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors de l\'exécution de la requête'
        ]);
        mysqli_stmt_close($stmt);
        exit();
    }

    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);

    echo json_encode([
        'status' => 'success',
        'comments' => $reviews
    ]);
}
