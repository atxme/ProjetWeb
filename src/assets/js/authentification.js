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
            const csrfToken = document.querySelector('input[name="csrf_token"]');
            
            if (!csrfToken || !csrfToken.value) {
                e.preventDefault();
                alert('Erreur de sécurité : token manquant. Veuillez rafraîchir la page.');
                return false;
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
