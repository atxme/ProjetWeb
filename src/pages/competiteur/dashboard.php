<?php
session_start();

// Vérification de la session
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'competiteur') {
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Compétiteur</title>
    <link rel="stylesheet" href="css/competiteur.css">
</head>
<body>
    <!-- Ajout du bandeau de statut -->
    <div class="status-bar">
        <div class="status">
            <span>Connecté en tant que :</span>
            <span class="role-badge">Compétiteur</span>
        </div>
        <div class="nav-buttons">
            <a href="dashboard.php" class="btn-stats">Tableau de bord</a>
            <a href="../logout.php" class="btn-logout">Déconnexion</a>
        </div>
    </div>

    <div class="competitor-container">
        <div class="admin-box">
            <div class="admin-header">
                <h2>Tableau de bord Compétiteur</h2>
                <p>Gérez vos participations aux concours</p>
            </div>

            <div class="actions-grid">
                <a href="soumettre_dessin.php" class="btn-submit">Soumettre un Dessin</a>
                <a href="mes_dessins.php" class="btn-submit">Voir mes Dessins</a>
            </div>
        </div>
    </div>
</body>
</html>
