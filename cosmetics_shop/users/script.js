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