<?php 
session_start();

if (file_exists('../../config/dbConnect.php')) {
    require '../../config/dbConnect.php';
} else {
    echo "Fichier de configuration non trouvé.";
    die();
}

$errorMessages = [];
$values = ['username' => '', 'password' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs saisies par l'utilisateur
    $values['username'] = trim($_POST['username'] ?? '');
    $values['password'] = trim($_POST['password'] ?? '');

    if (empty($values['username']) || empty($values['password'])) {
        $errorMessages['general'] = "Veuillez remplir tous les champs.";
    } else {
        try {
            // Rechercher l'utilisateur par email ou identifiant
            $query = "SELECT u.*, r.role_name FROM users u 
                      INNER JOIN roles r ON u.role_id = r.id 
                      WHERE u.email = ? OR u.identifiant = ?";
            $stmt = mysqli_prepare($link, $query);
            mysqli_stmt_bind_param($stmt, 'ss', $values['username'], $values['username']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);

            if (!$user) {
                $errorMessages['username'] = "Nom d'utilisateur ou email introuvable.";
            } elseif (!password_verify($values['password'], $user['password'])) {
                $errorMessages['password'] = "Mot de passe incorrect.";
            } else {
                // Authentification réussie
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role_name'],
                    'name' => $user['first_name'] . ' ' . $user['last_name']
                ];

                // Redirection basée sur le rôle
                if ($user['role_name'] === 'admin') {
                    header('Location: ../admin.html'); // Rediriger vers une page admin
                } else {
                    header('Location: ../index.php'); // Rediriger vers la page principale
                }
                exit();
            }
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            $errorMessages['general'] = "Erreur d'exécution: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Connexion</title>
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
        <header>
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container">
                    <a class="navbar-brand" href="../index.php">Cosmetics Shop</a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
                            <li class="nav-item"><a class="nav-link" href="#faq">FAQ</a></li>
                            <li class="nav-item"><a class="nav-link" href="../panier.html"><i class="fas fa-shopping-cart"></i> Panier</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </header>
        <main class="container mt-5">
            <h2>Connexion</h2>
            <form id="loginForm" method="POST">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input 
                        type="text" 
                        class="form-control <?= isset($errorMessages['username']) ? 'is-invalid' : '' ?>" 
                        id="username" 
                        name="username" 
                        value="<?= htmlspecialchars($values['username']) ?>"
                    >
                    <div id="usernameError" class="text-danger"><?= $errorMessages['username'] ?? '' ?></div>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input 
                        type="password" 
                        class="form-control <?= isset($errorMessages['password']) ? 'is-invalid' : '' ?>" 
                        id="password" 
                        name="password"
                    >
                    <div id="passwordError" class="text-danger"><?= $errorMessages['password'] ?? '' ?></div>
                </div>
                <?php if (isset($errorMessages['general'])): ?>
                    <div class="text-danger mb-3"><?= $errorMessages['general'] ?></div>
                <?php endif; ?>
                <button type="submit" class="btn btn-primary">Se connecter</button>
                <button type="reset" class="btn btn-secondary">Annuler</button>
                <a href="creation_de_compte.php" class="btn btn-link">Créer un compte</a>
            </form>
        </main>
    <script src="script.js"></script>
    </body>
</html>
