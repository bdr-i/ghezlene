<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .is-invalid {
            border-color: red;
        }
        .text-danger {
            color: red;
            font-size: 0.9em;
        }
        .password-toggle {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }
        .password-container {
            position: relative;
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
                    class="form-control" 
                    id="username" 
                    name="username" 
                    value=""
                >
                <div id="usernameError" class="text-danger"></div>
            </div>
            <div class="form-group password-container">
                <label for="password">Mot de passe</label>
                <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    name="password"
                >
                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                <div id="passwordError" class="text-danger"></div>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
            <button type="reset" class="btn btn-secondary">Annuler</button>
            <a href="creation_de_compte.php" class="btn btn-link">Créer un compte</a>
        </form>
    </main>
    <footer>
        <div class="container text-center mt-5">
            <span>Copyright &copy; <span>Cosmoshop</span></span>
        </div>
    </footer>
    <script src="script.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function (event) {
            event.preventDefault();
            connexion();
        });
        function connexion() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            $.ajax({
                url: '../api/auth/auth.php',
                method: 'POST',
                data: {
                    action: 'login',
                    username: username,
                    password: password
                },
                success: function (response) {
                    if (response.role === 'admin') {
                        window.location.href = '../admin.php';
                    } else if (response.role === 'user') {
                        window.location.href = '../index.php';
                    } 
                    console.log(response);
                    if (response.status === 'error') {
                        document.getElementById('usernameError').innerText = response.message;
                    }
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }
        document.getElementById('togglePassword').addEventListener('click', function () {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            // Changer l'icône
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
