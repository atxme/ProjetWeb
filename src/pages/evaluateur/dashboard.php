<?php
if (isset($_GET['concours_id'])) {
    $concours_id = urldecode($_GET['concours_id']);
    // Recherchez les informations du concours en fonction de l'ID dans la base de données
    // Exemple :
    // $query = $pdo->prepare('SELECT * FROM Concours WHERE nom = :nom');
    // $query->execute(['nom' => $concours_id]);
    // $concours = $query->fetch(PDO::FETCH_ASSOC);

    echo "Vous consultez les détails pour le concours : " . htmlspecialchars($concours_id);
} else {
    echo "Aucun concours sélectionné.";
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
            <?php echo htmlspecialchars($_SESSION['login']); ?> : 
            <span class="role-badge"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></span>
        </div>
        <div><?php echo htmlspecialchars($concours_id); ?></div>
        <div class="nav-buttons">
            <!-- Afficher le bouton si l'utilisateur est à la fois évaluateur et compétiteur -->
            <a href="../evaluateur/evaluateur.php" class="btn-stats">Retour</a>
            <div>
                <?php
                if(isset($_GET['logout'])) {
                    session_destroy();
                    header('Location: ../../index.php');
                    exit;
                }
                ?>
                <a class="btn-logout" href="?logout=true">Déconnexion</a>
            </div>
        </div>
    </div>
</body>
</html>

