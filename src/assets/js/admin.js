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
        const clubSelect = document.getElementById('club');
        const userTypeSelect = document.getElementById('userType');
        const utilisateurSelect = document.getElementById('utilisateur');
        const concoursSelect = document.getElementById('concours');

        // Désactiver les sélections qui dépendent d'autres choix
        function updateSelectStates() {
            // Activer/désactiver les sélections en cascade
            if (concoursSelect.value) {
                clubSelect.disabled = false;
                if (clubSelect.value) {
                    userTypeSelect.disabled = false;
                    if (userTypeSelect.value) {
                        utilisateurSelect.disabled = false;
                    }
                }
            }

            // Réinitialiser les sélections en cascade si nécessaire
            if (!concoursSelect.value) {
                clubSelect.value = '';
                clubSelect.disabled = true;
                userTypeSelect.value = '';
                userTypeSelect.disabled = true;
                utilisateurSelect.value = '';
                utilisateurSelect.disabled = true;
            }
        }

        async function loadClubs() {
            if (!concoursSelect.value) return;

            try {
                const response = await fetch(`admin.php?action=getClubs&concours=${concoursSelect.value}`);
                if (!response.ok) throw new Error('Erreur réseau');
                
                const clubs = await response.json();
                
                clubSelect.innerHTML = '<option value="">Sélectionner un club</option>';
                clubs.forEach(club => {
                    const option = new Option(club.nomClub, club.numClub);
                    clubSelect.add(option);
                });
                clubSelect.disabled = false;
            } catch (error) {
                console.error('Erreur:', error);
                showPopup('Erreur lors du chargement des clubs', 'error');
            }
        }

        async function loadUtilisateurs() {
            if (!clubSelect.value || !userTypeSelect.value || !concoursSelect.value) return;

            try {
                const response = await fetch(`admin.php?action=getUsers&club=${clubSelect.value}&type=${userTypeSelect.value}&concours=${concoursSelect.value}`);
                if (!response.ok) throw new Error('Erreur réseau');
                
                const users = await response.json();
                
                utilisateurSelect.innerHTML = '<option value="">Sélectionner un utilisateur</option>';
                if (users.length === 0) {
                    utilisateurSelect.innerHTML = '<option value="">Aucun utilisateur disponible</option>';
                    showPopup('Aucun utilisateur disponible pour ce rôle', 'error');
                } else {
                    users.forEach(user => {
                        const option = new Option(`${user.prenom} ${user.nom}`, user.numUtilisateur);
                        utilisateurSelect.add(option);
                    });
                    utilisateurSelect.disabled = false;
                }
            } catch (error) {
                console.error('Erreur:', error);
                showPopup('Erreur lors du chargement des utilisateurs', 'error');
            }
        }

        // Gérer les changements de sélection
        concoursSelect.addEventListener('change', () => {
            loadClubs();
            updateSelectStates();
        });

        clubSelect.addEventListener('change', () => {
            userTypeSelect.disabled = false;
            updateSelectStates();
        });

        userTypeSelect.addEventListener('change', () => {
            loadUtilisateurs();
            updateSelectStates();
        });

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
                    updateSelectStates();
                    // Recharger les clubs pour le concours sélectionné
                    loadClubs();
                }
            } catch (error) {
                console.error('Erreur:', error);
                showPopup('Erreur lors de l\'ajout du participant', 'error');
            }
        });

        // Initialiser l'état des sélections au chargement
        updateSelectStates();
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
