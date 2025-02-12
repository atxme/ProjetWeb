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
    $passSubmit = isset($_POST['pass_submit']) ? true : false;

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

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($drawing['type'], $allowedTypes)) {
        echo "Type de fichier non autorisé. Veuillez télécharger une image au format JPEG, PNG ou GIF.";
        exit;
    }

    // Sanitize file name
    $extension = pathinfo($drawing['name'], PATHINFO_EXTENSION);
    $extension = strtolower($extension);
    $safeName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($drawing['name'], PATHINFO_FILENAME));
    $drawingPath = '';

    // Ensure upload directory exists
    $uploadDir = '../../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    try {
        $conn->beginTransaction();

        // Insert the drawing record to get the primary key
        $stmt = $conn->prepare("INSERT INTO Dessin (numCompetiteur, numConcours, commentaire, leDessin, dateRemise) VALUES (?, ?, ?, '', ?)");
        $stmt->execute([$userId, $contestId, $comment, date('Y-m-d')]);

        $drawingId = $conn->lastInsertId();
        $drawingPath = $uploadDir . $drawingId . '.' . $extension;
        $publicPath = "/uploads/" . $drawingId . '.' . $extension;

        // Date validation
        if (!$passSubmit) {
            $stmt = $conn->prepare("SELECT dateDeb, dateFin FROM Concours WHERE numConcours = ?");
            $stmt->execute([$contestId]);
            $contestDates = $stmt->fetch();

            $currentDate = date('Y-m-d');
            if ($currentDate < $contestDates['dateDeb'] || $currentDate > $contestDates['dateFin']) {
                throw new Exception("La date de remise doit être comprise entre la date de début et de fin du concours.");
            }
        }

        // Update the record with the actual file path
        $stmt = $conn->prepare("UPDATE Dessin SET leDessin = ? WHERE numDessin = ?");
        $stmt->execute([$publicPath, $drawingId]);

        // Move the uploaded file to the correct directory
        if (!move_uploaded_file($drawing['tmp_name'], $drawingPath)) {
            throw new Exception("Échec du téléchargement du fichier.");
        }

        $conn->commit();
        echo "Dessin soumis avec succès.";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur lors de la soumission du dessin : " . $e->getMessage();
    }
}
?>