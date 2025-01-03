<?php
session_start();
require '../../../config/dbConnect.php';

// Ensure user is logged in
if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté']);
    exit;
}
$userId = $_SESSION['user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $comment = trim($_POST['comment']);
    $rating = filter_var($_POST['rating'], FILTER_VALIDATE_INT);

    // Input validation
    if (!$productId || !$comment || !$rating || $rating < 1 || $rating > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Données invalides']);
        exit;
    }

    // Prepare and execute the insert
    $query = "INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'iiss', $productId,$userId, $rating, $comment);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout du commentaire']);
        }
        
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation de la requête']);
    }
    
    mysqli_close($link);
}
?>