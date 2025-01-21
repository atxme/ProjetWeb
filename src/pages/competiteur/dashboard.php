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
            <form action="../evaluateur/evaluateur.php" method="post">
            <!-- Champ caché pour le token CSRF -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']); ?>">
                <button type="submit" class="btn-stats">Go to évaluateur</button>
            </form>
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
