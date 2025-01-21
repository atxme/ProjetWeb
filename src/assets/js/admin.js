document.addEventListener('DOMContentLoaded', function() {
    const concoursForm = document.getElementById('concoursForm');
    const userForm = document.getElementById('userForm');

    function showPopup(message, type) {
        const popup = document.createElement('div');
        popup.className = `popup popup-${type}`;
        popup.textContent = message;
        document.body.appendChild(popup);

        setTimeout(() => {
            popup.remove();
        }, 3500);
    }

    async function submitForm(formData, url) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            showPopup(data.message, data.success ? 'success' : 'error');
            
            if (data.success) {
                // Réinitialiser le formulaire si succès
                document.querySelector('form').reset();
            }
        } catch (error) {
            showPopup('Une erreur est survenue', 'error');
            console.error('Erreur:', error);
        }
    }

    // Récupérer les messages de session s'ils existent
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');

    if (successMessage) {
        showPopup(successMessage.dataset.message, 'success');
        successMessage.remove();
    }

    if (errorMessage) {
        showPopup(errorMessage.dataset.message, 'error');
        errorMessage.remove();
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
                showPopup('Le thème doit contenir au moins 3 caractères', 'error');
                return;
            }

            if (dateFin <= dateDeb) {
                showPopup('La date de fin doit être postérieure à la date de début', 'error');
                return;
            }

            if (nbClubMin < 1 || nbClubMin > 12) {
                showPopup('Le nombre de clubs doit être compris entre 1 et 12', 'error');
                return;
            }

            if (nbParticipantMin < 1 || nbParticipantMin > 12) {
                showPopup('Le nombre de participants par club doit être compris entre 1 et 12', 'error');
                return;
            }

            const formData = new FormData(this);
            submitForm(formData, 'admin.php');
        });
    }

    if (userForm) {
        const concoursSelect = userForm.querySelector('[name="concours"]');
        const clubSelect = userForm.querySelector('[name="club"]');
        const userTypeSelect = userForm.querySelector('[name="userType"]');
        const utilisateurSelect = userForm.querySelector('[name="utilisateur"]');

        // Désactiver initialement les sélections dépendantes
        clubSelect.disabled = true;
        userTypeSelect.disabled = true;
        utilisateurSelect.disabled = true;

        // Écouteur pour le changement de concours
        concoursSelect.addEventListener('change', function() {
            clubSelect.disabled = !this.value;
            if (!this.value) {
                clubSelect.value = '';
                userTypeSelect.value = '';
                utilisateurSelect.value = '';
                userTypeSelect.disabled = true;
                utilisateurSelect.disabled = true;
            }
        });

        // Écouteur pour le changement de club
        clubSelect.addEventListener('change', function() {
            userTypeSelect.disabled = !this.value;
            if (!this.value) {
                userTypeSelect.value = '';
                utilisateurSelect.value = '';
                utilisateurSelect.disabled = true;
            }
        });

        // Écouteur pour le changement de type d'utilisateur
        userTypeSelect.addEventListener('change', function() {
            utilisateurSelect.disabled = !this.value;
            if (!this.value) {
                utilisateurSelect.value = '';
            }
            
            // Mettre à jour la liste des utilisateurs en fonction du club et du type
            if (this.value && clubSelect.value) {
                updateUsersList(clubSelect.value, this.value);
            }
        });

        // Fonction pour mettre à jour la liste des utilisateurs
        async function updateUsersList(clubId, userType) {
            try {
                const response = await fetch(`get_users.php?club=${clubId}&type=${userType}`);
                const users = await response.json();
                
                // Vider et remplir la liste des utilisateurs
                utilisateurSelect.innerHTML = '<option value="">Sélectionnez un utilisateur</option>';
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.nom} ${user.prenom}`;
                    utilisateurSelect.appendChild(option);
                });
                
                utilisateurSelect.disabled = false;
            } catch (error) {
                console.error('Erreur lors de la récupération des utilisateurs:', error);
                showError('Erreur lors de la récupération des utilisateurs');
            }
        }

        userForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!utilisateurSelect.value) {
                showPopup('Veuillez sélectionner un utilisateur', 'error');
                return;
            }

            try {
                const formData = new FormData(this);
                const response = await fetch('admin.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                showPopup(result.message, result.success ? 'success' : 'error');
                
                if (result.success) {
                    this.reset();
                    // Recharger les clubs pour le concours sélectionné
                    loadClubs();
                }
            } catch (error) {
                console.error('Erreur:', error);
                showPopup('Erreur lors de l\'ajout du participant', 'error');
            }
        });
    }

    // Fonction pour formater la date au format YYYY-MM-DD
    function formatDate(date) {
        return date.toISOString().split('T')[0];
    }

    // Récupérer les éléments de date
    const dateDebInput = document.getElementById('dateDeb');
    const dateFinInput = document.getElementById('dateFin');

    if (dateDebInput && dateFinInput) {
        // Obtenir la date d'aujourd'hui
        const today = new Date();
        const minDate = formatDate(today);

        // Définir la date minimale pour les deux champs
        dateDebInput.min = minDate;
        dateFinInput.min = minDate;

        // Ajouter un écouteur d'événement pour la date de début
        dateDebInput.addEventListener('change', function() {
            // La date de fin ne peut pas être antérieure à la date de début
            dateFinInput.min = dateDebInput.value;
            
            // Si la date de fin est antérieure à la date de début, la réinitialiser
            if (dateFinInput.value && dateFinInput.value < dateDebInput.value) {
                dateFinInput.value = dateDebInput.value;
            }
        });

        // Ajouter un écouteur d'événement pour la date de fin
        dateFinInput.addEventListener('change', function() {
            // Si la date de fin est antérieure à la date de début, afficher une erreur
            if (this.value < dateDebInput.value) {
                alert('La date de fin ne peut pas être antérieure à la date de début');
                this.value = dateDebInput.value;
            }
        });
    }

    // Fonction pour valider les dates
    function validateDates(dateDeb, dateFin) {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        const dateDebObj = new Date(dateDeb);
        const dateFinObj = new Date(dateFin);
        
        if (!dateDeb || !dateFin) {
            return {
                error: "Les dates de début et de fin sont obligatoires",
                field: !dateDeb ? 'dateDeb' : 'dateFin'
            };
        }
        
        if (dateDebObj < today) {
            return {
                error: "La date de début ne peut pas être antérieure à aujourd'hui",
                field: 'dateDeb'
            };
        }
        if (dateFinObj <= dateDebObj) {
            return {
                error: "La date de fin doit être postérieure à la date de début",
                field: 'dateFin'
            };
        }
        if ((dateFinObj - dateDebObj) / (1000 * 60 * 60 * 24) > 365) {
            return {
                error: "La durée du concours ne peut pas dépasser un an",
                field: 'dateFin'
            };
        }
        return null;
    }

    // Fonction pour valider le thème
    function validateTheme(theme) {
        if (!theme) {
            return {
                error: "Le thème est obligatoire",
                field: 'theme'
            };
        }
        if (theme.length < 3) {
            return {
                error: "Le thème doit contenir au moins 3 caractères",
                field: 'theme'
            };
        }
        if (theme.length > 100) {
            return {
                error: "Le thème ne peut pas dépasser 100 caractères",
                field: 'theme'
            };
        }
        return null;
    }

    // Fonction pour valider la description
    function validateDescription(description) {
        if (!description) {
            return {
                error: "La description est obligatoire",
                field: 'descriptif_detaille'
            };
        }
        if (description.length < 10) {
            return {
                error: "La description doit contenir au moins 10 caractères",
                field: 'descriptif_detaille'
            };
        }
        if (description.length > 500) {
            return {
                error: "La description ne peut pas dépasser 500 caractères",
                field: 'descriptif_detaille'
            };
        }
        return null;
    }

    // Fonction pour afficher l'erreur sous le champ
    function showFieldError(field, message) {
        const existingError = field.parentElement.querySelector('.field-error');
        if (existingError) {
            existingError.textContent = message;
        } else {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error';
            errorDiv.textContent = message;
            field.parentElement.appendChild(errorDiv);
        }
        field.classList.add('invalid-field');
    }

    // Fonction pour retirer l'erreur
    function removeFieldError(field) {
        const errorDiv = field.parentElement.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.remove();
        }
        field.classList.remove('invalid-field');
    }

    // Validation en temps réel pour le thème
    const themeInput = document.getElementById('theme');
    themeInput.addEventListener('input', function() {
        const error = validateTheme(this.value.trim());
        if (error) {
            showFieldError(this, error.error);
        } else {
            removeFieldError(this);
        }
    });

    // Validation en temps réel pour la description
    const descInput = document.getElementById('descriptif_detaille');
    descInput.addEventListener('input', function() {
        const error = validateDescription(this.value.trim());
        if (error) {
            showFieldError(this, error.error);
        } else {
            removeFieldError(this);
        }
    });

    // Validation en temps réel pour les dates
    const dateDebInput = document.getElementById('dateDeb');
    const dateFinInput = document.getElementById('dateFin');

    function validateDateInputs() {
        const error = validateDates(dateDebInput.value, dateFinInput.value);
        if (error) {
            const field = error.field === 'dateDeb' ? dateDebInput : dateFinInput;
            showFieldError(field, error.error);
        } else {
            removeFieldError(dateDebInput);
            removeFieldError(dateFinInput);
        }
    }

    dateDebInput.addEventListener('input', validateDateInputs);
    dateFinInput.addEventListener('input', validateDateInputs);

    // Désactiver le bouton de soumission si des erreurs sont présentes
    function updateSubmitButton() {
        const submitBtn = document.getElementById('creation-concours-form').querySelector('button[type="submit"]');
        const hasErrors = document.querySelectorAll('.field-error').length > 0;
        submitBtn.disabled = hasErrors;
        submitBtn.style.opacity = hasErrors ? '0.5' : '1';
    }

    // Ajouter les écouteurs pour mettre à jour le bouton
    document.querySelectorAll('input, textarea').forEach(field => {
        field.addEventListener('input', updateSubmitButton);
    });

    // Fonction pour afficher les messages d'erreur
    function showError(message) {
        const popup = document.createElement('div');
        popup.className = 'popup popup-error';
        popup.textContent = message;
        document.body.appendChild(popup);
        
        setTimeout(() => {
            popup.remove();
        }, 5000);
    }

    // Fonction pour afficher les messages de succès
    function showSuccess(message) {
        const popup = document.createElement('div');
        popup.className = 'popup popup-success';
        popup.textContent = message;
        document.body.appendChild(popup);
        
        setTimeout(() => {
            popup.remove();
        }, 3000);
    }

    // Soumission du formulaire
    const form = document.getElementById('creation-concours-form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Vérifier une dernière fois toutes les validations
            const themeError = validateTheme(themeInput.value.trim());
            const descError = validateDescription(descInput.value.trim());
            const dateError = validateDates(dateDebInput.value, dateFinInput.value);

            if (themeError || descError || dateError) {
                if (themeError) showError(themeError.error);
                if (descError) showError(descError.error);
                if (dateError) showError(dateError.error);
                return;
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                
                if (result.success) {
                    showSuccess('Concours créé avec succès !');
                    form.reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showError(result.message || 'Erreur lors de la création du concours');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showError(error.message || 'Une erreur est survenue lors de la création du concours');
            }
        });
    }
});
