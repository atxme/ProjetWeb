<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'DIRECTEUR') {
    header('Location: ../index.php');
    exit();
}

// Connexion à la base de données
require_once '../../include/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $numCompetiteur = (int)$_POST['numCompetiteur'];
    $numClub = (int)$_POST['numClub'];

    $db = Database::getInstance();
    $pdo = $db->getConnection();

    if ($action === 'ajouter') {
        // Ajouter un compétiteur
        $sql = "INSERT INTO Competiteur (numCompetiteur, datePremiereParticipation) VALUES (:numCompetiteur, CURRENT_DATE)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':numCompetiteur' => $numCompetiteur]);
        $message = "Compétiteur ajouté avec succès.";
    } elseif ($action === 'supprimer') {
        // Supprimer un compétiteur
        $sql = "DELETE FROM Competiteur WHERE numCompetiteur = :numCompetiteur";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':numCompetiteur' => $numCompetiteur]);
        $message = "Compétiteur supprimé avec succès.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Compétiteurs</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>
    <div class="admin-dashboard">
        <h2>Gestion des Compétiteurs</h2>
        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-group">
                <label for="numCompetiteur">Numéro du Compétiteur à Ajouter</label>
                <input type="number" id="numCompetiteur" name="numCompetiteur" required>
            </div>
            <div class="form-group">
                <label for="numClub">Numéro du Club</label>
                <input type="number" id="numClub" name="numClub" required>
            </div>
            <button type="submit" class="btn-submit">Ajouter Compétiteur</button>
        </form>

        <form method="post">
            <input type="hidden" name="action" value="supprimer">
            <div class="form-group">
                <label for="numCompetiteur">Numéro du Compétiteur à Supprimer</label>
                <input type="number" id="numCompetiteur" name="numCompetiteur" required>
            </div>
            <button type="submit" class="btn-submit">Supprimer Compétiteur</button>
        </form>
    </div>
</body>

</html>