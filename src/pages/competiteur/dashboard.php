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
    <title>Competiteur Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue, Competiteur!</h1>
        <p>Voici votre tableau de bord.</p>
        <a href="submit_drawing.php" class="btn">Soumettre un Dessin</a>
        <!-- Add more content specific to competiteur here -->
    </div>
</body>
</html>