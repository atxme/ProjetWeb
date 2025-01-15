document.addEventListener('DOMContentLoaded', function() {
    const concoursForm = document.getElementById('concoursForm');
    const userForm = document.getElementById('userForm');

    function showError(form, message) {
        const existingAlert = form.querySelector('.alert-danger');
        if (existingAlert) {
            existingAlert.remove();
        }

        const alert = document.createElement('div');
        alert.className = 'alert alert-danger';
        alert.textContent = message;
        form.insertBefore(alert, form.firstChild);

        setTimeout(() => alert.remove(), 3000);
    }

    if (concoursForm) {
        concoursForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const theme = document.getElementById('theme').value.trim();
            const dateDeb = new Date(document.getElementById('dateDeb').value);
            const dateFin = new Date(document.getElementById('dateFin').value);
            const nbClubMin = parseInt(document.getElementById('nbClubMin').value);
            const nbParticipantMin = parseInt(document.getElementById('nbParticipantMin').value);

            if (theme.length < 3) {
                showError(this, 'Le thème doit contenir au moins 3 caractères');
                return;
            }

            if (dateFin <= dateDeb) {
                showError(this, 'La date de fin doit être postérieure à la date de début');
                return;
            }

            if (nbClubMin < 1 || nbClubMin > 12) {
                showError(this, 'Le nombre de clubs doit être compris entre 1 et 12');
                return;
            }

            if (nbParticipantMin < 1 || nbParticipantMin > 12) {
                showError(this, 'Le nombre de participants par club doit être compris entre 1 et 12');
                return;
            }

            this.submit();
        });
    }

    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nom = document.getElementById('nom').value.trim();
            const prenom = document.getElementById('prenom').value.trim();
            const age = parseInt(document.getElementById('age').value);

            if (nom.length < 2 || prenom.length < 2) {
                showError(this, 'Le nom et le prénom doivent contenir au moins 2 caractères');
                return;
            }

            if (age < 0 || age > 120) {
                showError(this, 'Veuillez entrer un âge valide');
                return;
            }

            this.submit();
        });
    }
});
