<?php
session_start();
require_once '../../include/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'competiteur') {
    header('Location: ../../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $contestId = $_POST['contest'];
    $comment = $_POST['comment'] ?? '';
    $drawing = $_FILES['drawing'];

    // Check if the user has already submitted three drawings for the contest
    $db = Database::getInstance();
    $conn = $db->getConnection();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM Dessin WHERE numCompetiteur = ? AND numConcours = ?");
    $stmt->execute([$userId, $contestId]);
    $count = $stmt->fetchColumn();

    if ($count >= 3) {
        echo "Vous avez déjà soumis trois dessins pour ce concours.";
        exit;
    }

    // Process the drawing file and store it in the database
    $drawingContent = file_get_contents($drawing['tmp_name']);
    $stmt = $conn->prepare("INSERT INTO Dessin (numCompetiteur, numConcours, commentaire, leDessin) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $contestId, $comment, $drawingContent]);

    echo "Dessin soumis avec succès.";
}
?>