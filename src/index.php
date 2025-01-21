<?php
session_start();
require_once 'include/config.php';

// Génération du token CSRF avec temps d'expiration
if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || 
    (time() - $_SESSION['csrf_token_time']) > 3600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Concours de Dessin </title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <div class="login-header">
                <h2>Connexion</h2>
                <p>Concours de Dessin</p>
            </div>
            <ul>
                <?php foreach ($_SESSION['roles'] as $role): ?>
                    <li><?= htmlspecialchars($role) ?></li>
                <?php endforeach; ?>
            </ul>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                        echo htmlspecialchars($_SESSION['error']);
                        unset($_SESSION['error']); 
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="auth.php" id="loginForm">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                
                <div class="form-group">
                    <label for="login">Identifiant</label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           required 
                           autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="password-container">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="current-password">
                        <span class="toggle-password" title="Afficher/Masquer le mot de passe">
                            <i class="eye-icon">👁️</i>
                        </span>
                    </div>
                </div>

                <button type="submit" class = "torch-button">Se connecter</button>
            </form>
        </div>
    </div>
    <script src="assets/js/authentification.js"></script>
</body>
</html>
