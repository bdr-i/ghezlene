<?php
    session_start();

    // Fonction pour vérifier le rôle admin
    function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    // Vérifier si l'utilisateur est admin
    if (!isAdmin()) {
        // Rediriger en utilisant une URL relative depuis le root
        header("Location: " . str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__) . '/index.php');
        exit;
    }
?>
