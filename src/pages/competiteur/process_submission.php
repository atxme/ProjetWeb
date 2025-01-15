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

    // Process the drawing file
    $uploadDir = '../../src/uploads/';
    $extension = pathinfo($drawing['name'], PATHINFO_EXTENSION);
    $drawingPath = '';

    try {
        $conn->beginTransaction();

        // Insert the drawing record to get the primary key
        $stmt = $conn->prepare("INSERT INTO Dessin (numCompetiteur, numConcours, commentaire, leDessin) VALUES (?, ?, ?, '')");
        $stmt->execute([$userId, $contestId, $comment]);

        $drawingId = $conn->lastInsertId();
        $drawingPath = $uploadDir . $drawingId . '.' . $extension;

        // Update the record with the actual file path
        $stmt = $conn->prepare("UPDATE Dessin SET leDessin = ? WHERE numDessin = ?");
        $stmt->execute([$drawingPath, $drawingId]);

        // Move the uploaded file to the correct directory
        move_uploaded_file($drawing['tmp_name'], $drawingPath);

        $conn->commit();
        echo "Dessin soumis avec succès.";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur lors de la soumission du dessin : " . $e->getMessage();
    }
}
?>