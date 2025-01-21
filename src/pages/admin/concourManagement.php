<?php
require_once '../../include/db.php';

/**
 * Détermine la saison en fonction de la date
 */
function determinerSaison($date) {
    $mois = (int)date('n', strtotime($date));
    return match(true) {
        $mois >= 3 && $mois <= 5 => 'printemps',
        $mois >= 6 && $mois <= 8 => 'ete',
        $mois >= 9 && $mois <= 11 => 'automne',
        default => 'hiver'
    };
}

/**
 * Vérifie si un concours existe déjà pour la saison et l'année données
 */
function verifierConcoursSaison($saison, $annee) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $sql = "SELECT COUNT(*) as nombre FROM Concours WHERE saison = :saison AND annee = :annee";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':saison' => $saison, ':annee' => $annee]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['nombre'] === 0;
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification du concours par saison: " . $e->getMessage());
        return false;
    }
}

/**
 * Crée un nouveau concours après vérification
 */
function creerConcours($theme, $descriptif, $dateDeb, $dateFin, $nbClubMin, $nbParticipantMin) {
    try {
        // Validation des dates
        $dateDebObj = new DateTime($dateDeb);
        $dateFinObj = new DateTime($dateFin);
        $aujourd_hui = new DateTime();
        
        // Déterminer la saison et l'année
        $saison = determinerSaison($dateDeb);
        $annee = (int)$dateDebObj->format('Y');

        // Vérifications
        if ($dateDebObj < $aujourd_hui) {
            return ['success' => false, 'message' => 'La date de début ne peut pas être dans le passé'];
        }

        if ($dateFinObj <= $dateDebObj) {
            return ['success' => false, 'message' => 'La date de fin doit être postérieure à la date de début'];
        }

        // Vérification de l'unicité saison/année
        if (!verifierConcoursSaison($saison, $annee)) {
            return ['success' => false, 'message' => 'Un concours existe déjà pour cette saison et cette année'];
        }

        // Validation des nombres minimums
        if ($nbClubMin < 6) {
            return ['success' => false, 'message' => 'Le nombre minimum de clubs doit être au moins 6'];
        }

        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $pdo->beginTransaction();

        try {
            // Génération d'un nouveau numConcours
            $sql = "SELECT COALESCE(MAX(numConcours), 0) + 1 as nextNum FROM Concours";
            $stmt = $pdo->query($sql);
            $numConcours = $stmt->fetch(PDO::FETCH_ASSOC)['nextNum'];

            // Sélection d'un président disponible
            $sqlPresident = "SELECT numPresident FROM President 
                           WHERE numPresident NOT IN (
                               SELECT numPresident FROM Concours 
                               WHERE dateFin >= :dateDeb
                           ) LIMIT 1";
            $stmt = $pdo->prepare($sqlPresident);
            $stmt->execute([':dateDeb' => $dateDeb]);
            $president = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$president) {
                throw new Exception('Aucun président disponible pour cette période');
            }

            // Insertion du concours
            $sql = "INSERT INTO Concours (
                        numConcours, 
                        numPresident,
                        theme, 
                        dateDeb, 
                        dateFin, 
                        etat,
                        nbClub,
                        descriptif,
                        saison,
                        annee
                    ) VALUES (
                        :numConcours,
                        :numPresident,
                        :theme, 
                        :dateDeb, 
                        :dateFin, 
                        'pas commence',
                        :nbClub,
                        :descriptif,
                        :saison,
                        :annee
                    )";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':numConcours' => $numConcours,
                ':numPresident' => $president['numPresident'],
                ':theme' => $theme,
                ':dateDeb' => $dateDeb,
                ':dateFin' => $dateFin,
                ':nbClub' => $nbClubMin,
                ':descriptif' => $descriptif,
                ':saison' => $saison,
                ':annee' => $annee
            ]);

            if (!$success) {
                throw new Exception('Erreur lors de la création du concours');
            }

            $pdo->commit();
            return ['success' => true, 'message' => 'Le concours a été créé avec succès'];

        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../../index.php');
        exit;
    }

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    $theme = isset($_POST['theme']) ? trim($_POST['theme']) : '';
    $descriptif = isset($_POST['descriptif']) ? trim($_POST['descriptif']) : '';
    $dateDeb = isset($_POST['dateDeb']) ? trim($_POST['dateDeb']) : '';
    $dateFin = isset($_POST['dateFin']) ? trim($_POST['dateFin']) : '';
    $nbClubMin = isset($_POST['nbClubMin']) ? (int)$_POST['nbClubMin'] : 6;
    $nbParticipantMin = isset($_POST['nbParticipantMin']) ? (int)$_POST['nbParticipantMin'] : 1;

    if (empty($theme) || empty($descriptif) || empty($dateDeb) || empty($dateFin)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Tous les champs obligatoires doivent être remplis"]);
        exit;
    }

    $result = creerConcours($theme, $descriptif, $dateDeb, $dateFin, $nbClubMin, $nbParticipantMin);
    
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} 