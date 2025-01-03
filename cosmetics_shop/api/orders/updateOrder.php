<?php
session_start();
require '../../../config/dbConnect.php'; // Assurez-vous que ce fichier contient la bonne connexion à la base de données

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données de la requête
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['order_id'], $data['status'])) {
        http_response_code(400);
        echo json_encode(['message' => 'order_id et status sont requis.']);
        exit();
    }

    $orderId = $data['order_id'];
    $status = $data['status'];

    // Liste des statuts valides (à adapter selon votre base de données)
    $validStatuses = ['Pending', 'Completed', 'Cancelled'];

    // Vérifier si le statut est valide
    if (!in_array($status, $validStatuses)) {
        http_response_code(400);
        echo json_encode(['message' => "Le statut '$status' n'est pas valide."]);
        exit();
    }

    // Requête pour mettre à jour le statut
    $query = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $status, $orderId);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo json_encode(['message' => 'Statut mis à jour avec succès.']);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Commande introuvable.']);
            }
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Erreur lors de la mise à jour de la commande.']);
        }

        mysqli_stmt_close($stmt);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Erreur lors de la préparation de la requête.']);
    }

    mysqli_close($link);
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Méthode non autorisée.']);
}
?>
