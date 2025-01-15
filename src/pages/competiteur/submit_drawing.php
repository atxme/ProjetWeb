<?php
session_start();

// Check if the user is logged in and has the 'competiteur' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'competiteur') {
    header('Location: ../../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Soumettre un Dessin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Soumettre un Dessin</h1>
        <form action="process_submission.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="contest">Concours</label>
                <select name="contest" id="contest" required>
                    <!-- Dynamically populate contests here -->
                    <option value="1">Concours 1</option>
                    <option value="2">Concours 2</option>
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
            <button type="submit">Soumettre</button>
        </form>
    </div>
</body>
</html>