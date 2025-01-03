<?php
    session_start();

    // Inclure la connexion à la base de données
    if (file_exists('../../../config/dbConnect.php')) {
        require '../../../config/dbConnect.php';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fichier de configuration introuvable']);
        exit;
    }

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user']['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté']);
        exit;
    }

    $userId = $_SESSION['user']['id'];

    // Supprimer le panier en session
    if (isset($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }

    // Supprimer les produits du panier de l'utilisateur dans la base de données
    $query = "DELETE FROM carts WHERE user_id = ?";

    if ($stmt = mysqli_prepare($link, $query)) {
        // Lier le paramètre
        mysqli_stmt_bind_param($stmt, "i", $userId);

        // Exécuter la requête
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['status' => 'success', 'message' => 'Le panier a été supprimé avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du panier']);
        }

        // Fermer la déclaration
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation de la requête']);
    }

    // Fermer la connexion à la base de données
    mysqli_close($link);
?>
