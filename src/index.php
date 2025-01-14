<?php
session_start();
require_once 'include/config.php';
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
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="auth.php">
            <div class="form-group">
                <label>Identifiant</label>
                <input type="text" name="login" required>
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Se connecter</button>
        </form>
    </div>
</body>

</html>