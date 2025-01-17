<?php
session_start();
require_once '../../include/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit;
}

// Fetch years from the database
$db = Database::getInstance();
$conn = $db->getConnection();
$stmt = $conn->prepare("SELECT DISTINCT YEAR(dateDeb) as year FROM Concours ORDER BY year");
$stmt->execute();
$years = $stmt->fetchAll();

$selectedYear = null;
$concoursDetails = null; // Initialize the variable to avoid undefined variable error
$participants = [];
$evaluatedDrawings = [];
$order = 'ASC';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['year'])) {
    $selectedYear = $_POST['year'];
    $order = $_POST['order'] ?? 'ASC';

    // Fetch concours details for the selected year
    $stmt = $conn->prepare("SELECT theme, descriptif, dateDeb, dateFin FROM Concours WHERE YEAR(dateDeb) = ?");
    $stmt->execute([$selectedYear]);
    $concoursDetails = $stmt->fetchAll();

    // Fetch participants for the selected year
    $stmt = $conn->prepare("
        SELECT u.nom, u.prenom, u.age, u.adresse, c.nomClub, c.departement, c.region
        FROM CompetiteurParticipe cp
        JOIN Utilisateur u ON cp.numCompetiteur = u.numUtilisateur
        JOIN Club c ON u.numClub = c.numClub
        JOIN Concours co ON cp.numConcours = co.numConcours
        WHERE YEAR(co.dateDeb) = ?
    ");
    $stmt->execute([$selectedYear]);
    $participants = $stmt->fetchAll();

    // Fetch evaluated drawings for the selected year
    $stmt = $conn->prepare("
        SELECT d.numDessin, e.note, u.nom, co.descriptif, co.theme
        FROM Evaluation e
        JOIN Dessin d ON e.numDessin = d.numDessin
        JOIN Utilisateur u ON d.numCompetiteur = u.numUtilisateur
        JOIN Concours co ON d.numConcours = co.numConcours
        WHERE YEAR(co.dateDeb) = ?
        ORDER BY e.note $order
    ");
    $stmt->execute([$selectedYear]);
    $evaluatedDrawings = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="../../assets/css/statistics.css">
</head>
<body>
    <div class="container">
        <h1>Statistiques des Concours</h1>
        <form method="POST">
            <div class="form-group">
                <label for="year">Sélectionnez une année</label>
                <select name="year" id="year" required>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo htmlspecialchars($year['year']); ?>" <?php echo ($selectedYear == $year['year']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year['year']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="order">Ordre de tri</label>
                <select name="order" id="order" required>
                    <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>Croissant</option>
                    <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>Décroissant</option>
                </select>
            </div>
            <button type="submit" class="btn">Afficher les statistiques</button>
        </form>

        <?php if ($selectedYear && $concoursDetails): ?>
            <h2>Détails des Concours de l'année <?php echo htmlspecialchars($selectedYear); ?></h2>
            <?php foreach ($concoursDetails as $concoursDetail): ?>
                <div class="concours-detail">
                    <p><strong>Thème:</strong> <?php echo htmlspecialchars($concoursDetail['theme']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($concoursDetail['descriptif']); ?></p>
                    <p><strong>Date de début:</strong> <?php echo htmlspecialchars($concoursDetail['dateDeb']); ?></p>
                    <p><strong>Date de fin:</strong> <?php echo htmlspecialchars($concoursDetail['dateFin']); ?></p>
                </div>
            <?php endforeach; ?>

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

            <h2>Dessins Évalués</h2>
            <table>
                <thead>
                    <tr>
                        <th>Numéro du Dessin</th>
                        <th>Note Attribuée</th>
                        <th>Nom du Compétiteur</th>
                        <th>Description du Concours</th>
                        <th>Thème du Concours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluatedDrawings as $drawing): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drawing['numDessin']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['note']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['nom']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['descriptif']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['theme']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>