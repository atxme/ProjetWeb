<?php
require_once '../../include/db.php';

// Définir le titre de la page et le CSS additionnel
$pageTitle = 'Tableau de bord - Compétiteur';
$additionalCss = ['../../assets/css/competitieur.css'];

// Inclure le header commun
require_once '../../components/header.php';

// Vérification spécifique du rôle compétiteur
if ($_SESSION['role'] !== 'competiteur') {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<body>
    <div class="container">
        <h1>Bienvenue, Competiteur!</h1>
        <p>Voici votre tableau de bord.</p>
        <a href="submit_drawing.php" class="btn">Soumettre un Dessin</a>
        <a href="view_drawings.php" class="btn">Voir mes Dessins</a>
    </div>
</body>

</html>