document.querySelector('form').addEventListener('submit', function(e) {
    const login = document.getElementById('login').value.trim();
    const password = document.getElementById('password').value;
    
    if (login.length < 3) {
        e.preventDefault();
        showError('L\'identifiant doit contenir au moins 3 caractères');
    }
    
    if (password.length < 8) {
        e.preventDefault();
        showError('Le mot de passe doit contenir au moins 8 caractères');
    }
});

function showError(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-danger';
    errorDiv.textContent = message;
    
    const container = document.querySelector('.login-container');
    const form = document.querySelector('form');
    container.insertBefore(errorDiv, form);
    
    setTimeout(() => errorDiv.remove(), 3000);
}
