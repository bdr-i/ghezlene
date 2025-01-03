<?php
    session_start();

    // Inclure la connexion à la base de données
    if (file_exists('../../../config/dbConnect.php')) {
        require '../../../config/dbConnect.php';
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Fichier de configuration introuvable']);
        exit;
    }

    if (isset($_POST['product_id'])) {
        $productId = (int) $_POST['product_id'];
        $quantity = 1; // La quantité ajoutée est toujours 1 à chaque fois

        // Vérifier si le panier existe en session
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Mettre à jour le panier en session
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity; // Ajouter 1 à la quantité existante
        } else {
            $_SESSION['cart'][$productId] = $quantity; // Ajouter un nouveau produit avec une quantité de 1
        }

        // Vérifier si l'utilisateur est connecté pour associer le panier à un utilisateur
        $userId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

        // Préparer la requête pour insérer ou mettre à jour le panier
        $query = "INSERT INTO carts (user_id, product_id, quantity) 
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + 1";

        if ($stmt = mysqli_prepare($link, $query)) {
            // Lier les paramètres
            mysqli_stmt_bind_param($stmt, "iii", $userId, $productId, $quantity);

            // Exécuter la requête
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['status' => 'success', 'message' => 'Produit ajouté au panier et enregistré dans la base de données']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'exécution de la requête']);
            }

            // Fermer la déclaration
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur de préparation de la requête']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID du produit manquant']);
    }

    // Fermer la connexion à la base de données
    mysqli_close($link);
?>
