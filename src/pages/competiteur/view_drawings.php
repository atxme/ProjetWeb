<?php
session_start();
require_once '../../include/db.php';

// Check if the user is logged in and has the 'competiteur' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'competiteur') {
    header('Location: ../../index.php');
    exit;
}

$db = Database::getInstance();
$conn = $db->getConnection();
$userId = $_SESSION['user_id'];

// Fetch drawings from the database
$stmt = $conn->prepare("SELECT d.numDessin, d.leDessin, c.theme FROM Dessin d LEFT JOIN Concours c ON d.numConcours = c.numConcours WHERE d.numCompetiteur = ?");
$stmt->execute([$userId]);
$drawings = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Dessins</title>
    <link rel="stylesheet" href="../../assets/css/competitieur.css">
</head>
<body>
    <div class="container">
        <h1>Mes Dessins</h1>
        <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
        <div class="form-group">
            <label for="sort">Trier par Concours</label>
            <select id="sort" onchange="sortDrawings()">
                <option value="all">Tous</option>
                <?php
                $stmt = $conn->prepare("SELECT numConcours, theme FROM Concours");
                $stmt->execute();
                $contests = $stmt->fetchAll();
                foreach ($contests as $contest) {
                    echo "<option value='" . htmlspecialchars($contest['numConcours']) . "'>" . htmlspecialchars($contest['theme']) . "</option>";
                }
                ?>
            </select>
        </div>
        <div id="drawings-list">
            <?php foreach ($drawings as $drawing): ?>
                <div class="drawing" data-contest="<?php echo htmlspecialchars($drawing['numConcours']); ?>">
                    <img src="<?php echo htmlspecialchars($drawing['leDessin']); ?>" alt="Drawing">
                    <p><?php echo htmlspecialchars($drawing['theme']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
        function sortDrawings() {
            var select = document.getElementById('sort');
            var value = select.value;
            var drawings = document.querySelectorAll('.drawing');
            drawings.forEach(function(drawing) {
                if (value === 'all' || drawing.getAttribute('data-contest') === value) {
                    drawing.style.display = 'block';
                } else {
                    drawing.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>