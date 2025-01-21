<?php
session_start();
require_once '../../include/db.php';

// Check if the user is logged in and has the 'competiteur' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'competiteur') {
    header('Location: ../../index.php');
    exit;
}

// Fetch concours from the database
$db = Database::getInstance();
$concours = $db->getConcours();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre un Dessin</title>
    <link rel="stylesheet" href="../../assets/css/competiteur.css">
</head>
<body>
    <div class="container">
        <h1>Soumettre un Dessin</h1>
        <form action="process_submission.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="contest">Concours</label>
                <select name="contest" id="contest" required>
                    <?php foreach ($concours as $concour): ?>
                        <option value="<?php echo htmlspecialchars($concour['numConcours']); ?>">
                            <?php echo htmlspecialchars($concour['saison'] . ' ' . $concour['annee'] . ', ' . $concour['theme']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="drawing">Dessin</label>
                <input type="file" id="drawing" name="drawing" accept="image/*" required>
            </div>
            <div class="form-group">
                <label for="comment">Commentaire</label>
                <textarea id="comment" name="comment"></textarea>
            </div>
            <button type="submit" class="btn">Soumettre</button>
            <button type="submit" name="pass_submit" class="btn">Passer la validation de la date</button>
        </form>
        <a href="dashboard.php" class="btn">Retour au tableau de bord</a>
    </div>
</body>
</html>