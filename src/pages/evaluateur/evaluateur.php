<?php
session_start();

// Vérification plus stricte de l'authentification et du rôle
if (!isset($_SESSION['user_id']) || 
    !isset($_SESSION['role']) || 
    $_SESSION['role'] !== 'evaluateur' || 
    !isset($_SESSION['login'])) {
    // Détruire la session si l'accès est non autorisé
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

$numUtilisateur = $_SESSION['numUtilisateur'];

// Récupération des données de l'utilisateur
$query = $pdo->prepare('SELECT nom, prenom, age, adresse, numClub FROM Utilisateur WHERE numUtilisateur = :numUtilisateur');
$query->execute(['numUtilisateur' => $numUtilisateur]);
$user = $query->fetch(PDO::FETCH_ASSOC);

// Régénérer le token CSRF si nécessaire
if (empty($_SESSION['csrf_token']) || 
    !isset($_SESSION['csrf_token_time']) || 
    (time() - $_SESSION['csrf_token_time']) > 3600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Vérifier l'expiration de la session (30 minutes d'inactivité)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluateur - Concours de Dessin</title>
    <link rel="stylesheet" href="../../assets/css/evaluateur.css">
</head>
<body>
    <div class="status-bar">
        <div class="status">
            <?php 
            echo htmlspecialchars($_SESSION['login']) . ' : ' . 
                 ucfirst(htmlspecialchars($_SESSION['role'])); 
            ?>
        </div>
        <div class="logout">
            <?php
            if(isset($_GET['logout'])) {
                session_destroy();
                header('Location: ../../index.php');
                exit;
            }
            ?>
            <a href="?logout=true">Déconnexion</a>
        </div>
    </div>
    <div class="container">
        <div class="left-column">
            <div class="box-info">
                <div class="header">
                    <h2>Mon profil</h2>
                    <div>
                        <label>Nom :</label>
                        <input type="text" value="<?= htmlspecialchars($user['nom']) ?>" disabled>
                    </div>
                    <div>
                        <label>Prénom :</label>
                        <input type="text" value="<?= htmlspecialchars($user['prenom']) ?>" disabled>
                    </div>
                    <div>
                        <label>Âge :</label>
                        <input type="text" value="<?= htmlspecialchars($user['age']) ?>" disabled>
                    </div>
                    <div>
                        <label>Adresse :</label>
                        <input type="text" value="<?= htmlspecialchars($user['adresse']) ?>" disabled>
                    </div>
                    <div>
                        <label>Club :</label>
                        <input type="text" value="<?= htmlspecialchars($user['numClub']) ?>" disabled>
                    </div>
                </div>
            </div>
            <div class="box-info">
                <div class="header">
                    <h2>Mes statistiques</h2>
                </div>
            </div>
        </div>
        <div class="right-column">
            <div class="box">
                <div class="header">
                    <h2>Liste des concours</h2>
                </div>
            </div>
        </div>
    </div>
</body>
