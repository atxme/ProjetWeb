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

// Animation torch-button
const button = document.querySelector('.torch-button');

button.addEventListener('mousemove', (e) => {
  const rect = button.getBoundingClientRect();
  const x = e.clientX - rect.left;
  const y = e.clientY - rect.top;

  const spotlight = button.querySelector('::after');
  button.style.setProperty('--mouse-x', `${x}px`);
  button.style.setProperty('--mouse-y', `${y}px`);
});

button.addEventListener('mouseleave', () => {
  button.style.setProperty('--mouse-x', `-50%`);
  button.style.setProperty('--mouse-y', `-50%`);
});
