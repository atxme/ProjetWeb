<?php
session_start();
require_once '../../include/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

// Fetch concours from the database
$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT numConcours, theme FROM Concours");
$stmt->execute();
$concours = $stmt->fetchAll();

$selectedConcours = null;
$concoursDetails = null; // Initialize the variable to avoid undefined variable error
$participants = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concours'])) {
    $selectedConcours = $_POST['concours'];

    // Fetch concours details
    $stmt = $conn->prepare("SELECT theme, descriptif, dateDeb, dateFin FROM Concours WHERE numConcours = ?");
    $stmt->execute([$selectedConcours]);
    $concoursDetails = $stmt->fetch();

    // Fetch participants
    $stmt = $conn->prepare("
        SELECT u.nom, u.prenom, u.age, u.adresse, c.nomClub, c.departement, c.region
        FROM CompetiteurParticipe cp
        JOIN Utilisateur u ON cp.numCompetiteur = u.numUtilisateur
        JOIN Club c ON u.numClub = c.numClub
        WHERE cp.numConcours = ?
    ");
    $stmt->execute([$selectedConcours]);
    $participants = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Statistiques des Concours</h1>
        <form method="POST">
            <div class="form-group">
                <label for="concours">Sélectionnez un concours</label>
                <select name="concours" id="concours" required>
                    <?php foreach ($concours as $concour): ?>
                        <option value="<?php echo htmlspecialchars($concour['numConcours']); ?>" <?php echo ($selectedConcours == $concour['numConcours']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($concour['theme']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn">Afficher les statistiques</button>
        </form>

        <?php if ($selectedConcours && $concoursDetails): ?>
            <h2>Détails du Concours</h2>
            <p><strong>Thème:</strong> <?php echo htmlspecialchars($concoursDetails['theme']); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($concoursDetails['descriptif']); ?></p>
            <p><strong>Date de début:</strong> <?php echo htmlspecialchars($concoursDetails['dateDeb']); ?></p>
            <p><strong>Date de fin:</strong> <?php echo htmlspecialchars($concoursDetails['dateFin']); ?></p>

            <h2>Participants</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Âge</th>
                        <th>Adresse</th>
                        <th>Club</th>
                        <th>Département</th>
                        <th>Région</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participant['nom']); ?></td>
                            <td><?php echo htmlspecialchars($participant['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($participant['age']); ?></td>
                            <td><?php echo htmlspecialchars($participant['adresse']); ?></td>
                            <td><?php echo htmlspecialchars($participant['nomClub']); ?></td>
                            <td><?php echo htmlspecialchars($participant['departement']); ?></td>
                            <td><?php echo htmlspecialchars($participant['region']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>