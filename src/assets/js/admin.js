document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire concours
    const concoursForm = document.getElementById('concoursForm');
    if (concoursForm) {
        concoursForm.addEventListener('submit', function(e) {
            const theme = document.getElementById('theme').value.trim();
            const dateDeb = document.getElementById('dateDeb').value;
            const dateFin = document.getElementById('dateFin').value;

            if (theme.length < 3) {
                e.preventDefault();
                showError('Le thème doit contenir au moins 3 caractères');
            }

            if (new Date(dateFin) <= new Date(dateDeb)) {
                e.preventDefault();
                showError('La date de fin doit être postérieure à la date de début');
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
        const form = document.getElementById('concoursForm');
        form.parentNode.insertBefore(alert, form);
        setTimeout(() => alert.remove(), 3000);
    }
});
