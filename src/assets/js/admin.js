document.addEventListener('DOMContentLoaded', function() {
    const concoursForm = document.getElementById('creation-concours-form');
    const userForm = document.getElementById('userForm');

    function showPopup(message, type) {
        const popup = document.createElement('div');
        popup.className = `popup popup-${type}`;
        popup.textContent = message;
        document.body.appendChild(popup);

        setTimeout(() => {
            popup.remove();
        }, 3000);
    }

    function showSuccess(message) {
        showPopup(message, 'success');
    }

    function showError(message) {
        showPopup(message, 'error');
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
        const themeInput = concoursForm.querySelector('[name="theme"]');
        const descInput = concoursForm.querySelector('[name="descriptif_detaille"]');
        const dateDebInput = concoursForm.querySelector('[name="dateDeb"]');
        const dateFinInput = concoursForm.querySelector('[name="dateFin"]');

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

    if (userForm) {
        const concoursSelect = userForm.querySelector('[name="concours"]');
        const clubSelect = userForm.querySelector('[name="club"]');
        const userTypeSelect = userForm.querySelector('[name="userType"]');
        const utilisateurSelect = userForm.querySelector('[name="utilisateur"]');

        // Désactiver initialement les sélections dépendantes
        if (clubSelect) clubSelect.disabled = true;
        if (userTypeSelect) userTypeSelect.disabled = true;
        if (utilisateurSelect) utilisateurSelect.disabled = true;

        // Écouteur pour le changement de concours
        if (concoursSelect) {
            concoursSelect.addEventListener('change', function() {
                if (clubSelect) {
                    clubSelect.disabled = !this.value;
                    clubSelect.value = '';
                }
                if (userTypeSelect) {
                    userTypeSelect.disabled = true;
                    userTypeSelect.value = '';
                }
                if (utilisateurSelect) {
                    utilisateurSelect.disabled = true;
                    utilisateurSelect.value = '';
                }
            });
        }

        // Écouteur pour le changement de club
        if (clubSelect) {
            clubSelect.addEventListener('change', function() {
                if (userTypeSelect) {
                    userTypeSelect.disabled = !this.value;
                    userTypeSelect.value = '';
                }
                if (utilisateurSelect) {
                    utilisateurSelect.disabled = true;
                    utilisateurSelect.value = '';
                }
            });
        }

        // Écouteur pour le changement de type d'utilisateur
        if (userTypeSelect) {
            userTypeSelect.addEventListener('change', function() {
                if (!this.value || !clubSelect.value) {
                    if (utilisateurSelect) {
                        utilisateurSelect.disabled = true;
                        utilisateurSelect.value = '';
                    }
                    return;
                }

                // Charger les utilisateurs correspondants
                fetch(`get_users.php?club=${clubSelect.value}&type=${this.value}`)
                    .then(response => response.json())
                    .then(users => {
                        if (utilisateurSelect) {
                            utilisateurSelect.innerHTML = '<option value="">Sélectionnez un utilisateur</option>';
                            users.forEach(user => {
                                const option = document.createElement('option');
                                option.value = user.id;
                                option.textContent = `${user.nom} ${user.prenom}`;
                                utilisateurSelect.appendChild(option);
                            });
                            utilisateurSelect.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        showError('Erreur lors du chargement des utilisateurs');
                    });
            });
        }

        // Gestion de la soumission du formulaire d'ajout de participant
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
});
