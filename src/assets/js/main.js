// Validation côté client
document.querySelector('.evaluation-form').addEventListener('submit', function(e) {
    const note = document.querySelector('input[name="note"]').value;
    if (note < 0 || note > 20) {
        e.preventDefault();
        alert('La note doit être comprise entre 0 et 20');
    }
});

// Mise à jour dynamique des états des concours
function updateContestStatus() {
    fetch('api/contest-status.php')
        .then(response => response.json())
        .then(data => {
            // Mise à jour de l'interface
        });
}

