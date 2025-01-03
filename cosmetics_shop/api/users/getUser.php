<?php
    session_start();
    require '../../../config/dbConnect.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET["id"])){
            $userId = filter_var($_GET["id"], FILTER_VALIDATE_INT);
            $query = "SELECT 
                        users.*, 
                        roles.role_name AS role_name
                        FROM 
                            users
                        JOIN 
                            roles
                        ON 
                            users.role_id = roles.id
                        WHERE 
                            users.id = ?;
                        ";
            $stmt = mysqli_prepare($link, $query);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'i', $userId);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                
                echo json_encode($user);
            } else {
                echo json_encode(['error' => 'Erreur de requête']);
            }
            
            mysqli_close($link);
        }else{
            echo json_encode(['error' => 'ID utilisateur non fourni']);
        }
    }else{
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
?>