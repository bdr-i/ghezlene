<?php 
session_start();

if (file_exists('../../../config/dbConnect.php')) {
    require '../../../config/dbConnect.php';
} else {
    echo json_encode(['status' => 'error', 'message' => 'Fichier de configuration non trouvé.']);
    exit();
}

header('Content-Type: application/json');

$errorMessages = [];
$values = ['username' => '', 'password' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données de la requête
    $values['username'] = trim($_POST['username'] ?? '');
    $values['password'] = trim($_POST['password'] ?? '');

    // Validation des champs
    if (empty($values['username']) || empty($values['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs.']);
        exit();
    }

    try {
        // Préparation de la requête SQL
        $query = "SELECT u.*, r.role_name FROM users u 
                  INNER JOIN roles r ON u.role_id = r.id 
                  WHERE u.email = ? OR u.identifiant = ?";
        $stmt = mysqli_prepare($link, $query);
        if (!$stmt) {
            throw new Exception("Erreur de préparation de la requête.");
        }

        mysqli_stmt_bind_param($stmt, 'ss', $values['username'], $values['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $user = mysqli_fetch_assoc($result);

        // Vérification des résultats
        if (!$user) {
            echo json_encode(['status' => 'error', 'message' => "Nom d'utilisateur ou email introuvable."]);
        } elseif (!password_verify($values['password'], $user['password'])) {
            echo json_encode(['status' => 'error', 'message' => 'Mot de passe incorrect.']);
        } else {
            // Authentification réussie
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role_name'],
                'name' => $user['first_name'] . ' ' . $user['last_name']
            ];

            $response = [
                'status' => 'success',
                'message' => 'Connexion réussie.',
                'role' => $user['role_name']
            ];

            echo json_encode($response);
        }
        mysqli_stmt_close($stmt);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Erreur d\'exécution: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
}
?>
