<?php
session_start();

if (file_exists('../../config/dbConnect.php')) {
    require '../../config/dbConnect.php';
} else {
    echo "Fichier de configuration introuvable.";
    die();
}

// Vérifiez que la connexion à la base de données est établie
if (!$link) {
    die("Connexion à la base de données échouée : " . mysqli_connect_error());
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'signup') {
        try {
            // Récupération des champs du formulaire
            $newUsername = trim($_POST['newUsername']);
            $newPassword = $_POST['newPassword'];
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $firstName = trim($_POST['firstName']);
            $lastName = trim($_POST['lastName']);

            // Vérifiez si le nom d'utilisateur ou l'e-mail existe déjà
            $checkQuery = "SELECT * FROM users WHERE identifiant = ? OR email = ?";
            $checkStmt = mysqli_prepare($link, $checkQuery);
            mysqli_stmt_bind_param($checkStmt, 'ss', $newUsername, $email);
            mysqli_stmt_execute($checkStmt);
            $checkResult = mysqli_stmt_get_result($checkStmt);

            if (mysqli_num_rows($checkResult) > 0) {
                $errors['username'] = "Nom d'utilisateur ou adresse e-mail déjà utilisé.";
            } else {
                // Hachage du mot de passe
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Insertion dans la base de données
                $insertQuery = "INSERT INTO users (identifiant, password, email, phone, first_name, last_name, role_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertStmt = mysqli_prepare($link, $insertQuery);
                $roleId = 2;  // Role utilisateur par défaut (à ajuster si nécessaire)
                mysqli_stmt_bind_param($insertStmt, 'ssssssi', $newUsername, $hashedPassword, $email, $phone, $firstName, $lastName, $roleId);

                // Exécution de la requête
                if (mysqli_stmt_execute($insertStmt)) {
                    // Récupérer l'utilisateur créé à partir de la base de données
                    $userId = mysqli_insert_id($link);  // Récupère l'ID de l'utilisateur inséré

                    $userQuery = "SELECT * FROM users WHERE id = ?";
                    $userStmt = mysqli_prepare($link, $userQuery);
                    mysqli_stmt_bind_param($userStmt, 'i', $userId);
                    mysqli_stmt_execute($userStmt);
                    $userResult = mysqli_stmt_get_result($userStmt);

                    if ($userResult && mysqli_num_rows($userResult) > 0) {
                        $user = mysqli_fetch_assoc($userResult);

                        // Authentification réussie
                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'email' => $user['email'],
                            'role' => $user['role_id'],
                            'name' => $user['first_name'] . ' ' . $user['last_name']
                        ];
                        
                        mysqli_stmt_close($userStmt);
                        header('Location: ../index.php');
                        exit();
                    } else {
                        $errors['general'] = "Utilisateur non trouvé après inscription.";
                    }
                } else {
                    $errors['general'] = "Erreur lors de la création du compte.";
                }

                mysqli_stmt_close($insertStmt);
            }

            mysqli_stmt_close($checkStmt);
        } catch (Exception $e) {
            $errors['general'] = "Erreur : " . $e->getMessage();
        }
    }
}

// Fermez la connexion à la base de données
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscription</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <style>
            .is-invalid {
                border-color: red;
            }

            .text-danger {
                color: red;
                font-size: 0.9em;
            }
        </style>
    </head>
    <body>
        
        <img class="img-responsive d-block mx-auto" src="Design_sans_titre__1_-removebg-preview.png" alt="" width="50px"/>
        <nav class="navbar navbar-expand-lg navbar-light bg-light" id="navbar">
            <div class="container">
                <a class="navbar-brand" href="#">Cosmetics Shop</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    <main class="container mt-5">
        <h2>Créer un compte</h2>
        <form id="signupForm" method="POST" action="">
            <input type="hidden" name="action" value="signup">
            <div class="form-group">
                <label for="newUsername">Nom d'utilisateur</label>
                <input type="text" class="form-control <?= isset($errors['username']) ? 'is-invalid' : '' ?>" id="newUsername" name="newUsername" value="<?= htmlspecialchars($_POST['newUsername'] ?? '') ?>">
                <div id="usernameError" class="text-danger"><?= $errors['username'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="newPassword">Mot de passe</label>
                <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="newPassword" name="newPassword">
                <div id="passwordError" class="text-danger"><?= $errors['password'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="text" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <div id="emailError" class="text-danger"><?= $errors['email'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="phone">Numéro de téléphone</label>
                <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                <div id="phoneError" class="text-danger"><?= $errors['phone'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="firstName">Prénom</label>
                <input type="text" class="form-control <?= isset($errors['firstName']) ? 'is-invalid' : '' ?>" id="firstName" name="firstName" value="<?= htmlspecialchars($_POST['firstName'] ?? '') ?>">
                <div id="firstNameError" class="text-danger"><?= $errors['firstName'] ?? '' ?></div>
            </div>
            <div class="form-group">
                <label for="lastName">Nom</label>
                <input type="text" class="form-control <?= isset($errors['lastName']) ? 'is-invalid' : '' ?>" id="lastName" name="lastName" value="<?= htmlspecialchars($_POST['lastName'] ?? '') ?>">
                <div id="lastNameError" class="text-danger"><?= $errors['lastName'] ?? '' ?></div>
            </div>
            <button type="submit" class="btn btn-success">S'inscrire</button>
            <a href="login.php" class="btn btn-link">Se connecter</a>
            <?php if (isset($errors['general'])): ?>
                <div class="text-danger mt-3"><?= $errors['general'] ?></div>
            <?php endif; ?>
        </form>
    </main>
    </body>
    <script src="script.js"></script>
</html>
