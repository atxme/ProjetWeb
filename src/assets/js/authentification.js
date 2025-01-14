document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'affichage du mot de passe
    const togglePassword = document.querySelector('.toggle-password');
    const passwordInput = document.querySelector('#password');

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('show');
        });
    }

    // Validation du formulaire
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const login = document.getElementById('login').value.trim();
            const password = document.getElementById('password').value;

            if (login.length < 3) {
                e.preventDefault();
                showError('L\'identifiant doit contenir au moins 3 caractères');
            }

            if (password.length < 6) {
                e.preventDefault();
                showError('Le mot de passe doit contenir au moins 6 caractères');
            }
        });
    }

    function showError(message) {
        const existingAlert = document.querySelector('.alert-danger');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alert = document.createElement('div');
        alert.className = 'alert alert-danger';
        alert.textContent = message;

        const form = document.getElementById('loginForm');
        form.parentNode.insertBefore(alert, form);

        setTimeout(() => alert.remove(), 3000);
    }
});
