<?php
    session_start();

    // Vérifier si l'ID utilisateur est fourni
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userId'])) {
        $userId = intval($_POST['userId']);

        require_once '../../../config/dbConnect.php'; // Inclure le fichier de configuration de la base de données

        $stmt = mysqli_prepare($link, 'SELECT 
                        users.*, 
                        roles.role_name AS role_name
                        FROM 
                            users
                        JOIN 
                            roles
                        ON 
                            users.role_id = roles.id
                        WHERE 
                            users.id = ?;');
        mysqli_stmt_bind_param($stmt, 'i', $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['identifiant'],
                'email' => $user['email'],
                'role' => $user['role_name']
            ];
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Utilisateur introuvable.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Données invalides.']);
    }
?>
