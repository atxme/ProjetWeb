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

// Récupérer le numClub du directeur connecté
$sql = "SELECT numClub FROM Directeur WHERE numDirecteur = :numDirecteur";
$stmt = $pdo->prepare($sql);
$stmt->execute([':numDirecteur' => $_SESSION['user_id']]);
$numClubDirecteur = $stmt->fetchColumn();

if (!$numClubDirecteur) {
    // Si le numClub n'est pas trouvé, rediriger vers la page de connexion
    header('Location: ../../index.php');
    exit;
}

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
        try {
            $pdo->beginTransaction();

            // Supprimer les évaluations des dessins du compétiteur
            $sql = "DELETE FROM Evaluation WHERE numDessin IN (SELECT numDessin FROM Dessin WHERE numCompetiteur = :numCompetiteur)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numCompetiteur' => $numCompetiteur]);

            // Supprimer les dessins du compétiteur
            $sql = "DELETE FROM Dessin WHERE numCompetiteur = :numCompetiteur";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numCompetiteur' => $numCompetiteur]);

            // Supprimer les participations aux concours
            $sql = "DELETE FROM CompetiteurParticipe WHERE numCompetiteur = :numCompetiteur";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numCompetiteur' => $numCompetiteur]);

            // Supprimer le compétiteur
            $sql = "DELETE FROM Competiteur WHERE numCompetiteur = :numCompetiteur";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numCompetiteur' => $numCompetiteur]);

            // Supprimer l'utilisateur
            $sql = "DELETE FROM Utilisateur WHERE numUtilisateur = :numUtilisateur";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numUtilisateur' => $numCompetiteur]);

            $pdo->commit();
            $message = "Compétiteur supprimé avec succès.";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur lors de la suppression du compétiteur : " . $e->getMessage();
        }
    }
}

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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membres as $membre): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($membre['numUtilisateur']); ?></td>
                            <td><?php echo htmlspecialchars($membre['nom']); ?></td>
                            <td><?php echo htmlspecialchars($membre['prenom']); ?></td>
                            <td>
                                <form method="post" style="margin: 0;">
                                    <input type="hidden" name="action" value="supprimer">
                                    <input type="hidden" name="numCompetiteur" value="<?php echo htmlspecialchars($membre['numUtilisateur']); ?>">
                                    <button type="submit" class="btn-delete" title="Supprimer le compétiteur">
                                        <i class="fas fa-trash"></i>🗑️
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>&