<?php
// Inclure les fichiers nécessaires pour la connexion à la base de données
require '../../../config/dbConnect.php';

// Vérifier si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification et validation des données envoyées
    $userId = isset($_POST['userId']) ? intval($_POST['userId']) : null;
    $firstName = isset($_POST['userFirstName']) ? trim($_POST['userFirstName']) : null;
    $lastName = isset($_POST['userLastName']) ? trim($_POST['userLastName']) : null;
    $email = isset($_POST['userEmail']) ? trim($_POST['userEmail']) : null;
    $phone = isset($_POST['userPhone']) ? trim($_POST['userPhone']) : null;
    $roleId = isset($_POST['userRole']) ? intval($_POST['userRole']) : null;

    // Vérification des champs obligatoires
    if (empty($userId) || empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($roleId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tous les champs sont obligatoires.'
        ]);
        exit;
    }

    // Préparation de la requête SQL pour mettre à jour l'utilisateur
    $sql = "UPDATE users 
            SET first_name = ?, 
                last_name = ?, 
                email = ?, 
                phone = ?, 
                role_id = ? 
            WHERE id = ?";

    $strm = mysqli_prepare($link, $sql);
    if($strm) {
        $strm->bind_param('ssssii', $firstName, $lastName, $email, $phone, $roleId, $userId);
        $strm->execute();

        if ($strm->affected_rows) {
            echo json_encode([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur.'
            ]);
        }

        $strm->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la préparation de la requête.'
        ]);
    }

    // Fermer la connexion
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée.'
    ]);
}
