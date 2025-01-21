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

// Récupérer les informations du club
$sql = "SELECT * FROM Club WHERE numClub = :numClub";
$stmt = $pdo->prepare($sql);
$stmt->execute([':numClub' => $numClubDirecteur]);
$clubInfo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$numClubDirecteur) {
    // Si le numClub n'est pas trouvé, rediriger vers la page de connexion
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $numCompetiteur = isset($_POST['numCompetiteur']) ? (int)$_POST['numCompetiteur'] : 0;
    $numClub = isset($_POST['numClub']) ? (int)$_POST['numClub'] : 0;

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

            // Vérifier si l'utilisateur est un président
            $sql = "SELECT COUNT(*) FROM President WHERE numPresident = :numPresident";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numPresident' => $numCompetiteur]);
            if ($stmt->fetchColumn() > 0) {
                throw new PDOException("Impossible de supprimer un président.");
            }

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
    } elseif ($action === 'ajouter_utilisateur') {
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $age = $_POST['age'];
        $adresse = $_POST['adresse'];
        $login = $_POST['login'];
        $mdp = $_POST['mdp'];

        $sql = "INSERT INTO Utilisateur (nom, prenom, age, adresse, login, mdp, numClub) VALUES (:nom, :prenom, :age, :adresse, :login, :mdp, :numClub)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':age' => $age,
            ':adresse' => $adresse,
            ':login' => $login,
            ':mdp' => password_hash($mdp, PASSWORD_DEFAULT),
            ':numClub' => $numClub
        ]);
        $message = "Utilisateur ajouté avec succès.";
    }
}

$sql = "SELECT u.numUtilisateur, u.login, u.nom, u.prenom, u.age,
        CASE 
            WHEN p.numPresident IS NOT NULL THEN 'Président'
            WHEN c.numCompetiteur IS NOT NULL THEN 'Compétiteur'
            WHEN e.numEvaluateur IS NOT NULL THEN 'Évaluateur'
            ELSE 'Membre'
        END as role
        FROM Utilisateur u
        LEFT JOIN President p ON u.numUtilisateur = p.numPresident
        LEFT JOIN Competiteur c ON u.numUtilisateur = c.numCompetiteur
        LEFT JOIN Evaluateur e ON u.numUtilisateur = e.numEvaluateur
        WHERE u.numClub = :numClub 
        AND u.numUtilisateur != :currentUser";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':numClub' => $numClubDirecteur,
    ':currentUser' => $_SESSION['user_id']
]);
$membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/directeur.css">
    <title>Gestion des Compétiteurs</title>
</head>

<body>
    <div class="status-bar">
        <div class="status">
            <?php
            echo htmlspecialchars($_SESSION['login']) . ' : Directeur';
            ?>
        </div>
        <div class="logout">
            <a href="?logout=true">Déconnexion</a>
        </div>
    </div>

    <div class="admin-container">
        <div class="admin-box">
            <div class="admin-header">
                <h2>Informations du Club</h2>
            </div>
            <div class="club-info">
                <div class="info-group">
                    <label>Nom du Club:</label>
                    <span><?php echo htmlspecialchars($clubInfo['nomClub']); ?></span>
                </div>
                <div class="info-group">
                    <label>Numéro du Club:</label>
                    <span><?php echo htmlspecialchars($clubInfo['numClub']); ?></span>
                </div>
                <div class="info-group">
                    <label>Adresse:</label>
                    <span><?php echo htmlspecialchars($clubInfo['adresse']); ?></span>
                </div>
                <div class="info-group">
                    <label>Téléphone:</label>
                    <span><?php echo htmlspecialchars($clubInfo['numTel']); ?></span>
                </div>
                <div class="info-group">
                    <label>Nombre d'Adhérents:</label>
                    <span><?php echo htmlspecialchars($clubInfo['nbAdherents']); ?></span>
                </div>
                <div class="info-group">
                    <label>Ville:</label>
                    <span><?php echo htmlspecialchars($clubInfo['ville']); ?></span>
                </div>
                <div class="info-group">
                    <label>Département:</label>
                    <span><?php echo htmlspecialchars($clubInfo['departement']); ?></span>
                </div>
                <div class="info-group">
                    <label>Région:</label>
                    <span><?php echo htmlspecialchars($clubInfo['region']); ?></span>
                </div>
            </div>
        </div>

        <div class="admin-box">
            <div class="admin-header">
                <h2>Ajouter un Nouvel Utilisateur</h2>
            </div>
            <form method="post">
                <input type="hidden" name="action" value="ajouter_utilisateur">
                <input type="hidden" name="numClub" value="<?php echo htmlspecialchars($numClubDirecteur); ?>">

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>

                <div class="form-group">
                    <label for="age">Âge</label>
                    <input type="number" id="age" name="age" required min="0">
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required>
                </div>

                <div class="form-group">
                    <label for="login">Login</label>
                    <input type="text" id="login" name="login" required>
                </div>

                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <input type="password" id="mdp" name="mdp" required>
                </div>

                <button type="submit" class="btn-submit">Ajouter l'utilisateur</button>
            </form>
        </div>

    </div>
    <div class="admin-container" style="margin-top: 0;">
        <div class="admin-box">
            <div class="admin-header">
                <h2>Membres Actuels du Club</h2>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Login</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Âge</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($membres as $membre): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($membre['login']); ?></td>
                            <td><?php echo htmlspecialchars($membre['nom']); ?></td>
                            <td><?php echo htmlspecialchars($membre['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($membre['age']); ?></td>
                            <td><?php echo htmlspecialchars($membre['role']); ?></td>
                            <td>
                                <div style="display: flex; gap: 10px;">
                                    <form method="post" style="margin: 0;">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="numCompetiteur" value="<?php echo htmlspecialchars($membre['numUtilisateur']); ?>">
                                        <button type="submit" class="btn-delete" title="Supprimer le compétiteur">
                                            <i class="fas fa-trash"></i>🗑️
                                        </button>
                                    </form>
                                    <form action="change_password.php" method="get" style="margin: 0;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($membre['numUtilisateur']); ?>">
                                        <button type="submit" class="btn-edit" title="Changer le mot de passe">
                                            🔑
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>