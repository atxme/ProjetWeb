<?php
session_start();
require_once '../../include/db.php';

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

if (!isset($_SESSION['user_id'])) {
    die('Erreur : user_id non défini dans la session.');
}

try {
    // Connexion à la base de données
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Récupérer l'id_user depuis la session
    $id_user = $_SESSION['user_id'];

    // Préparer la requête pour récupérer les données utilisateur et du club associé
    $query = $pdo->prepare('
        DESCRIBE Utilisateur;
    ');

    // Exécuter la requête avec le paramètre id_user
    $query->execute(['user_id' => $id_user]);

    // Récupérer les résultats sous forme associative
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Si l'utilisateur n'est pas trouvé
    if (!$user) {
        die('Erreur : Aucun utilisateur trouvé.');
    }
} catch (PDOException $e) {
    // Si une erreur de base de données se produit, l'afficher
    die('Erreur de base de données : ' . $e->getMessage());
}

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
