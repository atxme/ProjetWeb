document.addEventListener('DOMContentLoaded', function() {
    // Sélecteurs de formulaires
    const concoursForm = document.getElementById('concoursForm');
    const userForm = document.getElementById('userForm');

    // Fonctions utilitaires pour les popups
    function showPopup(message, type) {
        const popup = document.createElement('div');
        popup.className = `popup popup-${type}`;
        popup.textContent = message;
        document.body.appendChild(popup);
        setTimeout(() => popup.remove(), 3000);
    }

    function showSuccess(message) {
        showPopup(message, 'success');
    }

    function showError(message) {
        showPopup(message, 'error');
    }

    // Fonctions de validation
    function validateTheme(theme) {
        if (!theme) return { error: "Le thème est obligatoire", field: 'theme' };
        if (theme.length < 3) return { error: "Le thème doit contenir au moins 3 caractères", field: 'theme' };
        if (theme.length > 100) return { error: "Le thème ne peut pas dépasser 100 caractères", field: 'theme' };
        return null;
    }

    function validateDescription(description) {
        if (!description) return { error: "La description est obligatoire", field: 'descriptif' };
        if (description.length < 10) return { error: "La description doit contenir au moins 10 caractères", field: 'descriptif' };
        if (description.length > 500) return { error: "La description ne peut pas dépasser 500 caractères", field: 'descriptif' };
        return null;
    }

    function validateDates(dateDeb, dateFin) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const dateDebObj = new Date(dateDeb);
        const dateFinObj = new Date(dateFin);

        if (!dateDeb || !dateFin) {
            return { error: "Les dates sont obligatoires", field: !dateDeb ? 'dateDeb' : 'dateFin' };
        }
        if (dateDebObj < today) {
            return { error: "La date de début ne peut pas être antérieure à aujourd'hui", field: 'dateDeb' };
        }
        if (dateFinObj <= dateDebObj) {
            return { error: "La date de fin doit être postérieure à la date de début", field: 'dateFin' };
        }
        if ((dateFinObj - dateDebObj) / (1000 * 60 * 60 * 24) > 365) {
            return { error: "La durée du concours ne peut pas dépasser un an", field: 'dateFin' };
        }
        return null;
    }

    // Gestion des erreurs de champs
    function showFieldError(field, message) {
        removeFieldError(field);
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        field.parentElement.appendChild(errorDiv);
        field.classList.add('invalid-field');
    }

    function removeFieldError(field) {
        const errorDiv = field.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
            field.classList.remove('invalid-field');
        }
    }

    // Gestion du formulaire de concours
    if (concoursForm) {
        const dateDebInput = document.getElementById('dateDeb');
        const dateFinInput = document.getElementById('dateFin');
        const themeInput = document.getElementById('theme');
        const descInput = document.getElementById('descriptif');

        // Initialisation des dates
        const today = new Date();
        const minDate = today.toISOString().split('T')[0];
        dateDebInput.min = minDate;
        dateFinInput.min = minDate;

        // Validation en temps réel
        themeInput.addEventListener('input', () => {
            const error = validateTheme(themeInput.value.trim());
            if (error) showFieldError(themeInput, error.error);
            else removeFieldError(themeInput);
        });

        descInput.addEventListener('input', () => {
            const error = validateDescription(descInput.value.trim());
            if (error) showFieldError(descInput, error.error);
            else removeFieldError(descInput);
        });

        dateDebInput.addEventListener('change', function() {
            dateFinInput.min = this.value;
            const error = validateDates(dateDebInput.value, dateFinInput.value);
            if (error) showFieldError(error.field === 'dateDeb' ? dateDebInput : dateFinInput, error.error);
            else {
                removeFieldError(dateDebInput);
                removeFieldError(dateFinInput);
            }
        });

        // Soumission du formulaire de concours
        concoursForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showSuccess('Concours créé avec succès !');
                    this.reset();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showError(result.message || 'Erreur lors de la création du concours');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showError('Une erreur est survenue lors de la création du concours');
            }
        });
    }

    // Gestion du formulaire utilisateur
    if (userForm) {
        const concoursSelect = userForm.querySelector('[name="concours"]');
        const clubSelect = userForm.querySelector('[name="club"]');
        const userTypeSelect = userForm.querySelector('[name="userType"]');
        const utilisateurSelect = userForm.querySelector('[name="utilisateur"]');

        // État initial
        clubSelect.disabled = true;
        userTypeSelect.disabled = true;
        utilisateurSelect.disabled = true;

        // Gestion des changements
        concoursSelect.addEventListener('change', function() {
            clubSelect.disabled = !this.value;
            clubSelect.value = '';
            userTypeSelect.disabled = true;
            userTypeSelect.value = '';
            utilisateurSelect.disabled = true;
            utilisateurSelect.value = '';
        });

        clubSelect.addEventListener('change', function() {
            userTypeSelect.disabled = !this.value;
            userTypeSelect.value = '';
            utilisateurSelect.disabled = true;
            utilisateurSelect.value = '';
        });

        userTypeSelect.addEventListener('change', async function() {
            if (!this.value || !clubSelect.value) {
                utilisateurSelect.disabled = true;
                utilisateurSelect.value = '';
                return;
            }

            try {
                const response = await fetch(`get_users.php?club=${clubSelect.value}&type=${this.value}`);
                const users = await response.json();
                utilisateurSelect.innerHTML = '<option value="">Sélectionnez un utilisateur</option>';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.nom} ${user.prenom}`;
                    utilisateurSelect.appendChild(option);
                });
                utilisateurSelect.disabled = false;
            } catch (error) {
                console.error('Erreur:', error);
                showError('Erreur lors du chargement des utilisateurs');
            }
        });

        // Soumission du formulaire utilisateur
        userForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                if (result.success) {
                    showSuccess('Participant ajouté avec succès !');
                    this.reset();
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    showError(result.message || 'Erreur lors de l\'ajout du participant');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showError('Une erreur est survenue lors de l\'ajout du participant');
            }
        });
    }
});
