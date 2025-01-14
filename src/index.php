<?php
session_start();
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion - Concours de Dessins</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Connexion</h2>
        <form method="POST" action="auth.php">
            <div class="form-group">
                <label>Identifiant</label>
                <input type="text" name="login" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button class = "torch-button" type="submit">Se connecter</button>
        </form>
    </div>
</body>
</html>
