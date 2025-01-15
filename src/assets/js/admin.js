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
            clubSelect.disabled = !concoursSelect.value;
            userTypeSelect.disabled = !clubSelect.value;
            utilisateurSelect.disabled = !userTypeSelect.value;

            // Réinitialiser les sélections en cascade
            if (!concoursSelect.value) {
                clubSelect.value = '';
                userTypeSelect.value = '';
                utilisateurSelect.value = '';
            }
            if (!clubSelect.value) {
                userTypeSelect.value = '';
                utilisateurSelect.value = '';
            }
            if (!userTypeSelect.value) {
                utilisateurSelect.value = '';
            }
        }

        async function loadUtilisateurs() {
            if (!clubSelect.value || !userTypeSelect.value || !concoursSelect.value) {
                utilisateurSelect.disabled = true;
                utilisateurSelect.innerHTML = '<option value="">Choisir d'abord un club et un type</option>';
                return;
            }

            try {
                const response = await fetch(`admin.php?action=getUsers&club=${clubSelect.value}&type=${userTypeSelect.value}&concours=${concoursSelect.value}`);
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                const users = await response.json();
                
                utilisateurSelect.innerHTML = '<option value="">Sélectionner un utilisateur</option>';
                users.forEach(user => {
                    const option = new Option(`${user.prenom} ${user.nom}`, user.numUtilisateur);
                    utilisateurSelect.add(option);
                });
                utilisateurSelect.disabled = false;
            } catch (error) {
                console.error('Erreur:', error);
                showPopup('Erreur lors du chargement des utilisateurs', 'error');
            }
        }

        // Initialiser l'état des sélections
        updateSelectStates();

        // Gérer les changements de sélection
        concoursSelect.addEventListener('change', () => {
            updateSelectStates();
            if (concoursSelect.value) {
                clubSelect.disabled = false;
            }
        });

        clubSelect.addEventListener('change', () => {
            updateSelectStates();
            if (clubSelect.value) {
                userTypeSelect.disabled = false;
            }
            loadUtilisateurs();
        });

        userTypeSelect.addEventListener('change', () => {
            updateSelectStates();
            loadUtilisateurs();
        });

        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!utilisateurSelect.value) {
                showPopup('Veuillez sélectionner un utilisateur', 'error');
                return;
            }

            const formData = new FormData(this);
            submitForm(formData, 'admin.php');
        });
    }
});
