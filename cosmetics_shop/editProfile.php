<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "Veuillez vous connecter pour modifier votre profil.";
    die();
}

if (file_exists('../config/dbConnect.php')) {
    require '../config/dbConnect.php';
} else {
    echo "Fichier de connexion introuvable";
    die();
}

$userId = $_SESSION['user']['id'];

// Récupérer les informations de l'utilisateur actuel
$query = "SELECT * FROM users WHERE id = $userId";
$result = mysqli_query($link, $query);
$user = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mise à jour des informations du profil
    $identifiant = $_POST['identifiant'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];

    $updateQuery = "UPDATE users SET identifiant = '$identifiant', email = '$email', first_name = '$first_name', last_name = '$last_name', phone = '$phone' WHERE id = $userId";
    mysqli_query($link, $updateQuery);
    header("Location: profile.php");
    exit();
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier mon profil</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <header id="top">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <a class="navbar-brand" href="#">Cosmetics Shop</a>
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item"><a class="nav-link" href="user.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Se déconnecter</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="container mt-5">
            <h2>Modifier mes informations</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="name">Nom</label>
                    <input type="text" class="form-control" id="identifiant" name="identifiant" value="<?php echo $user['identifiant']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Nom</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Prenom</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </main>

    <footer class="mt-5">
        <div class="container">
            <p class="text-center">Cosmetics Shop &copy; 2021</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
