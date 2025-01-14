<?php
session_start();
require_once 'include/config.php';

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// En-têtes de sécurité
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");

// Fonction pour échapper les sorties HTML
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Concours de Dessins</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <?php
    echo '<div class="login-container">';
    echo '<h2>Connexion</h2>';

    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger">';
        echo e($_SESSION['error']);
        unset($_SESSION['error']);
        echo '</div>';
    }

    echo '<form method="POST" action="auth.php" autocomplete="off">';
    echo '<input type="hidden" name="csrf_token" value="' . e($_SESSION['csrf_token']) . '">';
    
    echo '<div class="form-group">';
    echo '<label for="login">Identifiant</label>';
    echo '<input type="text" 
             id="login" 
             name="login" 
             required 
             autocomplete="username"
             minlength="3"
             maxlength="50"
             value="' . (isset($_POST['login']) ? e($_POST['login']) : '') . '">';
    echo '</div>';
    
    echo '<div class="form-group">';
    echo '<label for="password">Mot de passe</label>';
    echo '<input type="password" 
             id="password" 
             name="password" 
             required
             autocomplete="current-password"
             minlength="8">';
    echo '</div>';
    
    echo '<button type="submit" class="btn-submit">Se connecter</button>';
    echo '</form>';
    echo '</div>';
    ?>
    <script src="assets/js/login.js"></script>
</body>
</html>
