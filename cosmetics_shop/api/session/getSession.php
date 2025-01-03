<?php
session_start();

// Définir l'en-tête pour une réponse JSON
header('Content-Type: application/json');

if (isset($_SESSION['user'])) {
    // Renvoyer uniquement les informations nécessaires pour éviter de divulguer des données sensibles
    $user = [
        'id' => $_SESSION['user']['id'] ?? null,
        'name' => $_SESSION['user']['name'] ?? null,
        'role' => $_SESSION['user']['role'] ?? null
    ];

    echo json_encode(['status' => 'success', 'user' => $user]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
}
?>
