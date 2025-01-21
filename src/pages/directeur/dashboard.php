<?php
require_once '../../include/db.php';

// Définir le titre de la page et le CSS additionnel
$pageTitle = 'Gestion des Compétiteurs';
$additionalCss = ['/assets/css/admin.css'];

// Inclure le header commun
require_once '../../components/header.php';

// Vérification spécifique du rôle directeur
if ($_SESSION['role'] !== 'directeur') {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

// Connexion à la base de données
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

<body>
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