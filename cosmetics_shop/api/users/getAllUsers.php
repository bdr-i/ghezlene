<?php
    session_start();
    require '../../../config/dbConnect.php';
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $query = "SELECT 
                    users.*, 
                    roles.role_name AS role_name
                    FROM 
                        users
                    JOIN 
                        roles
                    ON 
                        users.role_id = roles.id;
                    ";
        $stmt = mysqli_prepare($link, $query);
        
        if ($stmt) {
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt);
            
            echo json_encode($users);
        } else {
            echo json_encode(['error' => 'Erreur de requête']);
        }
        
        mysqli_close($link);
    }
    ?>