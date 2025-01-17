<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'directeur') {
    header('Location: ../../index.php');
    exit;
}

// Connexion à la base de données
require_once '../../include/db.php';
$db = Database::getInstance();
$pdo = $db->getConnection();

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

// Récupérer les membres du club
$numClubDirecteur = $_SESSION['numClub']; // Assurez-vous que le numéro du club est stocké dans la session
$sql = "SELECT numUtilisateur, nom, prenom FROM Utilisateur WHERE numClub = :numClub";
$stmt = $pdo->prepare($sql);
$stmt->execute([':numClub' => $numClubDirecteur]);
$membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <div class="status-bar">
        <div class="status">
            <?php
            echo htmlspecialchars($_SESSION['login']) . ' : ' .
                ucfirst(htmlspecialchars($_SESSION['user_type']));
            ?>
        </div>
        <div class="logout">
            <a href="?logout=true">Déconnexion</a>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-box">
            <div class="admin-header">
                <h2>Gestion des Compétiteurs</h2>
            </div>
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
                    <input type="number" id="numClub" name="numClub" value="<?php echo htmlspecialchars($numClubDirecteur); ?>" readonly>
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

        <div class="admin-box">
            <div class="admin-header">
                <h2>Membres Actuels du Club</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Numéro d'Utilisateur</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membres as $membre): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($membre['numUtilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($membre['nom']); ?></td>
                            <td><?php echo htmlspecialchars($membre['prenom']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>&