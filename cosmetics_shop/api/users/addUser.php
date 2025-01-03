<?php
session_start();

if (file_exists('../../../config/dbConnect.php')) {
    require '../../../config/dbConnect.php';
} else {
    echo json_encode(['success' => false, 'message' => 'Fichier de configuration introuvable.']);
    exit;
}

// Vérifiez que la connexion à la base de données est établie
if (!$link) {
    echo json_encode(['success' => false, 'message' => 'Connexion à la base de données échouée.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'checkAvailability') {
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');

        $errors = [];
        if ($email) {
            $query = "SELECT id FROM users WHERE email = ?";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $errors['email'] = "L'adresse e-mail est déjà utilisée.";
            }
            mysqli_stmt_close($stmt);
        }

        if ($username) {
            $query = "SELECT id FROM users WHERE identifiant = ?";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if (mysqli_num_rows($result) > 0) {
                $errors['username'] = "Le nom d'utilisateur est déjà utilisé.";
            }
            mysqli_stmt_close($stmt);
        }

        echo json_encode(['success' => empty($errors), 'errors' => $errors]);
        exit;
    }

    if ($action === 'signup') {
        $newUsername = trim($_POST['newUsername'] ?? '');
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $firstName = trim($_POST['firstName'] ?? '');
        $lastName = trim($_POST['lastName'] ?? '');

        $errors = [];

        if ($newPassword !== $confirmPassword) {
            $errors['password'] = "Les mots de passe ne correspondent pas.";
        }

        if (empty($errors)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $roleId = 2; // Role utilisateur par défaut

            $query = "INSERT INTO users (identifiant, password, email, phone, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ssssssi', $newUsername, $hashedPassword, $email, $phone, $firstName, $lastName, $roleId);

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'message' => 'Compte créé avec succès.', 'userId' => mysqli_insert_id($link)]);
            } else {
                echo json_encode(['success' => false, 'message' => "Erreur lors de la création du compte.".mysqli_error($link)]);
            }
            mysqli_stmt_close($stmt);
        } else {
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        exit;
    }
}

mysqli_close($link);
?>