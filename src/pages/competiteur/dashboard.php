<?php
session_start();

$authorizedRoles = ['competiteur']; // Rôles autorisés

if (
    !isset($_SESSION['user_id']) || 
    !isset($_SESSION['roles']) || 
    empty(array_intersect($authorizedRoles, $_SESSION['roles'])) || 
    !isset($_SESSION['login'])
) {
    // Détruire la session si l'accès est non autorisé
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Compétiteur</title>
    <link rel="stylesheet" href="../../assets/css/competiteur.css">
</head>
<body>
    <!-- Ajout du bandeau de statut -->
    <div class="status-bar">
        <div class="status">
            <span>Connecté en tant que :</span>
            <span class="role-badge">Compétiteur</span>
        </div>
        <div class="nav-buttons">
            <a href="../evaluateur/evaluateur.php" class="btn-stats">Mon profil évaluateur</a>
            <a href="../logout.php" class="btn-logout">Déconnexion</a>
        </div>
    </div>
    <div class="competitor-container">
        <div class="admin-box">
            <div class="admin-header">
                <h2>Tableau de bord Compétiteur</h2>
                <p>Gérez vos participations aux concours</p>
            </div>
        </div>
        <div class="actions-grid">
                <a href="submit_drawing.php" class="btn-submit">Soumettre un Dessin</a>
                <a href="view_drawings.php" class="btn-submit">Voir mes Dessins</a>
        </div>
    </div>
</body>
</html>
