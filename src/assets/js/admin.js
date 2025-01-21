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
                field: 'description'
            };
        }
        if (description.length < 10) {
            return {
                error: "La description doit contenir au moins 10 caractères",
                field: 'description'
            };
        }
        if (description.length > 500) {
            return {
                error: "La description ne peut pas dépasser 500 caractères",
                field: 'description'
            };
        }
        return null;
    }

    // Fonction pour marquer un champ comme invalide
    function markFieldAsInvalid(fieldName) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            // Retirer d'abord toutes les classes
            field.classList.remove('invalid-field', 'shake');
            // Forcer un reflow pour assurer que l'animation se déclenche
            void field.offsetWidth;
            // Ajouter les classes
            field.classList.add('invalid-field');
            field.classList.add('shake');
            
            // Retirer l'animation après qu'elle soit terminée
            setTimeout(() => {
                field.classList.remove('shake');
            }, 500);

            // Focus sur le champ invalide
            field.focus();
        }
    }

    // Fonction pour réinitialiser tous les champs
    function resetFields() {
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            field.classList.remove('invalid-field', 'shake');
        });
    }

    // Ajouter des écouteurs pour retirer la classe invalid-field lors de la modification
    form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('invalid-field');
        });
        
        field.addEventListener('focus', function() {
            if (this.classList.contains('invalid-field')) {
                this.classList.add('focused-invalid');
            }
        });
        
        field.addEventListener('blur', function() {
            this.classList.remove('focused-invalid');
        });
    });

    // Fonction pour afficher les messages d'erreur
    function showError(message, fieldName = null) {
        const popup = document.createElement('div');
        popup.className = 'popup popup-error';
        popup.textContent = message;
        document.body.appendChild(popup);
        
        if (fieldName) {
            markFieldAsInvalid(fieldName);
        }
        
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

    // Gestionnaire de soumission du formulaire
    const form = document.getElementById('creation-concours-form');
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            resetFields();
            
            const theme = form.querySelector('[name="theme"]').value.trim();
            const description = form.querySelector('[name="description"]').value.trim();
            const dateDeb = form.querySelector('[name="dateDeb"]').value;
            const dateFin = form.querySelector('[name="dateFin"]').value;

            // Validation du thème
            const themeError = validateTheme(theme);
            if (themeError) {
                showError(themeError.error, themeError.field);
                return;
            }

            // Validation de la description
            const descriptionError = validateDescription(description);
            if (descriptionError) {
                showError(descriptionError.error, descriptionError.field);
                return;
            }

            // Validation des dates
            const dateError = validateDates(dateDeb, dateFin);
            if (dateError) {
                showError(dateError.error, dateError.field);
                return;
            }

            // Si toutes les validations sont passées, soumettre le formulaire
            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                let result;
                try {
                    result = await response.json();
                } catch (e) {
                    throw new Error('Réponse invalide du serveur');
                }

                if (!response.ok) {
                    throw new Error(result.message || 'Erreur lors de la création du concours');
                }

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
