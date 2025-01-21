<?php
require_once '../../include/db.php';

// Définir le titre de la page
$pageTitle = 'Evaluateur - Concours de Dessin';
$additionalCss = ['../../assets/css/evaluateur.css'];

// Inclure le header commun
require_once '../../components/header.php';

// Vérification spécifique du rôle évaluateur
if ($_SESSION['role'] !== 'evaluateur') {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

try {
    // Connexion à la base de données
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Récupérer l'id_user depuis la session
    $id_user = $_SESSION['user_id'];

    // Préparer la requête pour récupérer les données utilisateur et du club associé
    $query = $pdo->prepare('
        SELECT u.nom, u.prenom, u.age, u.adresse, c.nomClub
        FROM Utilisateur u
        LEFT JOIN Club c ON u.numClub = c.numClub
        WHERE u.numUtilisateur = :user_id
    ');

    // Exécuter la requête avec le paramètre id_user
    $query->execute(['user_id' => $id_user]);

    // Récupérer les résultats sous forme associative
    $user = $query->fetch(PDO::FETCH_ASSOC);

    // Si l'utilisateur n'est pas trouvé
    if (!$user) {
        die('Erreur : Aucun utilisateur trouvé.');
    }

    // Préparer la requête pour récupérer les statistiques de l'évaluateur
    $statsQuery = $pdo->prepare('
        SELECT 
            AVG(ev.note) AS moyenne_notes,
            MAX(ev.note) AS note_max,
            MIN(ev.note) AS note_min,
            c.theme AS nom_concours,
            MAX(c.dateFin) AS dernier_concours
        FROM 
            Evaluation ev
        INNER JOIN 
            Jury j ON ev.numEvaluateur = j.numEvaluateur
        INNER JOIN 
            Concours c ON j.numConcours = c.numConcours
        WHERE 
            ev.numEvaluateur = :user_id
        GROUP BY 
            ev.numEvaluateur
    ');

    // Exécuter la requête avec le paramètre id_user
    $statsQuery->execute(['user_id' => $id_user]);

    // Récupérer les résultats sous forme associative
    $stats = $statsQuery->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Si une erreur de base de données se produit, l'afficher
    die('Erreur de base de données : ' . $e->getMessage());
}
?>

<div class="container">
    <div class="left-column">
        <div class="box-info">
            <div class="header">
                <h2>Mon profil</h2>
                <table class="profile-table">
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Nom :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($user['nom']) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Prénom :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($user['prenom']) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Âge :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($user['age']) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Adresse :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($user['adresse']) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Club :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($user['nomClub']) ?>" disabled></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="box-info">
            <div class="header">
                <h2>Mes statistiques</h2>
                <table class="profile-table">
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Moyenne des notes :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars(strval(round($stats['moyenne_notes'], 2))) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Note maximale :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($stats['note_max']) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Note minimale :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($stats['note_min']) ?>" disabled></td>
                    </tr>
                    <tr class="profile-row">
                        <td class="label-cell"><label class="cat">Dernier concours :</label></td>
                        <td class="input-cell"><input class="profile-input textarea" value="<?= htmlspecialchars($stats['nom_concours'] . ' (' . $stats['dernier_concours'] . ')') ?>" disabled></td>
                    </tr>
                </table>
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

</html>