<?php
session_start();

// Vérification de base de l'authentification
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !isset($_SESSION['login'])) {
    session_destroy();
    header('Location: /index.php');
    exit;
}

// Régénérer le token CSRF si nécessaire
if (empty($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) || (time() - $_SESSION['csrf_token_time']) > 3600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Vérifier l'expiration de la session (30 minutes d'inactivité)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: /index.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Gestion de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Concours de Dessin' ?></title>
    <?php if (isset($additionalCss)): ?>
        <?php foreach ($additionalCss as $css): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body>
    <div class="status-bar">
        <div class="status">
            <?php echo htmlspecialchars($_SESSION['login']) . ' : ' . ucfirst(htmlspecialchars($_SESSION['role'])); ?>
        </div>
        <div class="logout">
            <a href="?logout=true">Déconnexion</a>
        </div>
    </div>