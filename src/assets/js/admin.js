document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire concours
    const concoursForm = document.getElementById('concoursForm');
    const userForm = document.getElementById('userForm');

    if (concoursForm) {
        concoursForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const theme = document.getElementById('theme').value.trim();
            const dateDeb = document.getElementById('dateDeb').value;
            const dateFin = document.getElementById('dateFin').value;

            if (theme.length < 3) {
                showError('Le thème doit contenir au moins 3 caractères');
                return;
            }

            if (new Date(dateFin) <= new Date(dateDeb)) {
                showError('La date de fin doit être postérieure à la date de début');
                return;
            }

            // Si tout est valide, soumettre le formulaire
            this.submit();
        });
    }

    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();
            const age = document.getElementById('age').value;

            if (nom.length < 2 || prenom.length < 2) {
                showError('Le nom et le prénom doivent contenir au moins 2 caractères');
                return;
            }

            if (age < 0 || age > 120) {
                showError('Veuillez entrer un âge valide');
                return;
            }

            // Si tout est valide, soumettre le formulaire
            this.submit();
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
        
        const activeForm = document.activeElement.closest('form');
        if (activeForm) {
            activeForm.insertAdjacentElement('beforebegin', alert);
        }

        setTimeout(() => alert.remove(), 3000);
    }
});
