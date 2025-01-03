<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Inscription</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
        <main class="container mt-5">
            <h2>Créer un compte</h2>
            <form id="signupForm">
                <div class="form-group">
                    <label for="newUsername">Nom d'utilisateur</label>
                    <input type="text" class="form-control" id="newUsername" name="newUsername">
                    <div id="usernameError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="text" class="form-control" id="email" name="email">
                    <div id="emailError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="newPassword">Mot de passe</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="newPassword" name="newPassword">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="newPassword">👁</button>
                        </div>
                    </div>
                    <div id="passwordError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="confirmPassword">Confirmer le mot de passe</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirmPassword">👁</button>
                        </div>
                    </div>
                    <div id="passwordError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="phone">Numéro de téléphone</label>
                    <input type="text" class="form-control" id="phone" name="phone">
                    <div id="phoneError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="firstName">Prénom</label>
                    <input type="text" class="form-control" id="firstName" name="firstName">
                    <div id="firstNameError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="lastName">Nom</label>
                    <input type="text" class="form-control" id="lastName" name="lastName">
                    <div id="lastNameError" class="text-danger"></div>
                </div>
                <button type="button" id="signupBtn" class="btn btn-success">S'inscrire</button>
                <button type="reset" class="btn btn-secondary">Réinitialiser</button>
                <a href="login.php">Déjà inscrit ? Connectez-vous</a>
                <div id="generalError" class="text-danger mt-3"></div>
            </form>
        </main>

        <footer class="container mt-5 text-center">
            <hr>
            <p>&copy; 2021 - Cosmetics Shop</p>
        </footer>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const removeErrorOnInput = (field, errorElement, validationFn) => {
                    field.addEventListener('input', () => {
                        if (validationFn()) {
                            field.classList.remove('is-invalid');
                            errorElement.textContent = '';
                        }
                    });
                }

                // Signup Form Validation
                const signupForm = document.getElementById("signupForm");
                if (signupForm) {
                    const username = document.getElementById("newUsername");
                    const password = document.getElementById("newPassword");
                    const email = document.getElementById("email");
                    const phone = document.getElementById("phone");
                    const firstName = document.getElementById("firstName");
                    const lastName = document.getElementById("lastName");

                    signupForm.addEventListener("submit", (e) => {
                        let isValid = true;

                        if (!username.value.trim()) {
                            document.getElementById("usernameError").textContent = "Le nom d'utilisateur est obligatoire.";
                            username.classList.add("is-invalid");
                            isValid = false;
                        }
                        if (password.value.length < 6 || !/\d/.test(password.value) || !/[^a-zA-Z0-9]/.test(password.value)) {
                            document.getElementById("passwordError").textContent = "Le mot de passe doit contenir au moins 6 caractères, un chiffre et un caractère spécial.";
                            password.classList.add("is-invalid");
                            isValid = false;
                        }
                        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
                            document.getElementById("emailError").textContent = "Adresse e-mail invalide.";
                            email.classList.add("is-invalid");
                            isValid = false;
                        }
                        if (!/^(\+213|0)(5|6|7)\d{8}$/.test(phone.value)) {
                            document.getElementById("phoneError").textContent = "Numéro de téléphone invalide.";
                            phone.classList.add("is-invalid");
                            isValid = false;
                        }
                        if (!firstName.value.trim()) {
                            document.getElementById("firstNameError").textContent = "Le prénom est obligatoire.";
                            firstName.classList.add("is-invalid");
                            isValid = false;
                        }
                        if (!lastName.value.trim()) {
                            document.getElementById("lastNameError").textContent = "Le nom est obligatoire.";
                            lastName.classList.add("is-invalid");
                            isValid = false;
                        }

                        if (!isValid) e.preventDefault();
                    });

                    removeErrorOnInput(username, document.getElementById("usernameError"), () => username.value.trim() !== "");
                    removeErrorOnInput(password, document.getElementById("passwordError"), () =>
                        password.value.length >= 6 && /\d/.test(password.value) && /[^a-zA-Z0-9]/.test(password.value)
                    );
                    removeErrorOnInput(email, document.getElementById("emailError"), () =>
                        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)
                    );
                    removeErrorOnInput(phone, document.getElementById("phoneError"), () =>
                        /^(\+213|0)(5|6|7)\d{8}$/.test(phone.value)
                    );
                    removeErrorOnInput(firstName, document.getElementById("firstNameError"), () => firstName.value.trim() !== "");
                    removeErrorOnInput(lastName, document.getElementById("lastNameError"), () => lastName.value.trim() !== "");
                }
            });
            $(document).ready(function () {
                // Fonctionnalité pour afficher/masquer le mot de passe
                $('.toggle-password').on('click', function () {
                    const targetId = $(this).data('target');
                    const targetInput = $('#' + targetId);
                    const type = targetInput.attr('type') === 'password' ? 'text' : 'password';
                    targetInput.attr('type', type);
                    $(this).text(type === 'password' ? '👁' : '🙈');
                });

                $('#signupBtn').on('click', function () {
                    // Validation des champs avant requête AJAX
                    const username = $('#newUsername').val().trim();
                    const email = $('#email').val().trim();
                    const password = $('#newPassword').val();
                    const confirmPassword = $('#confirmPassword').val();
                    const phone = $('#phone').val().trim();
                    const firstName = $('#firstName').val().trim();
                    const lastName = $('#lastName').val().trim();

                    let isValid = true;

                    if (!username) {
                        $('#usernameError').text("Le nom d'utilisateur est obligatoire.");
                        isValid = false;
                    } else {
                        $('#usernameError').text("");
                    }

                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        $('#emailError').text("Adresse e-mail invalide.");
                        isValid = false;
                    } else {
                        $('#emailError').text("");
                    }

                    if (password.length < 6 || !/\d/.test(password) || !/[^a-zA-Z0-9]/.test(password)) {
                        $('#passwordError').text("Le mot de passe doit contenir au moins 6 caractères, un chiffre et un caractère spécial.");
                        isValid = false;
                    } else {
                        $('#passwordError').text("");
                    }

                    if (password !== confirmPassword) {
                        $('#passwordError').text("Les mots de passe ne correspondent pas.");
                        isValid = false;
                    }

                    if (!/^(\+213|0)(5|6|7)\d{8}$/.test(phone)) {
                        $('#phoneError').text("Numéro de téléphone invalide.");
                        isValid = false;
                    } else {
                        $('#phoneError').text("");
                    }

                    if (!firstName) {
                        $('#firstNameError').text("Le prénom est obligatoire.");
                        isValid = false;
                    } else {
                        $('#firstNameError').text("");
                    }

                    if (!lastName) {
                        $('#lastNameError').text("Le nom est obligatoire.");
                        isValid = false;
                    } else {
                        $('#lastNameError').text("");
                    }

                    if (!isValid) {
                        
                        return; // Arrête l'exécution si les champs ne sont pas valides
                    }

                    // Si les champs sont valides, effectuer la requête AJAX
                    const formData = {
                        action: 'signup',
                        newUsername: username,
                        email: email,
                        newPassword: password,
                        confirmPassword: confirmPassword,
                        phone: phone,
                        firstName: firstName,
                        lastName: lastName
                    };

                    $.post('../api/users/addUser.php', {
                        action: 'checkAvailability',
                        username: formData.newUsername,
                        email: formData.email
                    }).done(function (response) {
                        const data = JSON.parse(response);
                        if (!data.success) {
                            $('#usernameError').text(data.errors?.username || '');
                            $('#emailError').text(data.errors?.email || '');
                        } else {
                            // Effectuer l'inscription
                            $.post('../api/users/addUser.php', formData).done(function (signupResponse) {
                                const signupData = JSON.parse(signupResponse);
                                if (signupData.success) {
                                    // Création de session côté serveur
                                    $.post('../api/session/createSession.php', { userId: signupData.userId }).done((function (sessionResponse) {
                                        window.location.replace('../index.php');
                                    }));
                                } else {
                                    $('#generalError').text(signupData.message || 'Erreur inconnue.');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    </body>
</html>
