<?php
require_once '../../include/db.php';

/**
 * Vérifie le nombre de concours pour une année donnée
 * 
 * @param int $annee L'année à vérifier
 * @return bool True si on peut créer un nouveau concours, False sinon
 */
function verifierNombreConcours($annee) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        // Requête pour compter le nombre de concours pour l'année spécifiée
        $sql = "SELECT COUNT(*) as nombre 
                FROM Concours 
                WHERE YEAR(dateDeb) = :annee";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':annee' => $annee]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Retourne true si moins de 4 concours existent pour cette année
        return ($result['nombre'] < 4);
        
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification du nombre de concours: " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un utilisateur est président
 */
function verifierEligibilitePresident($numUtilisateur) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        
        $sql = "SELECT numPresident FROM President WHERE numPresident = :numUtilisateur";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':numUtilisateur' => $numUtilisateur]);
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        error_log("Erreur lors de la vérification d'éligibilité: " . $e->getMessage());
        return false;
    }
}

/**
 * Crée un nouveau concours après vérification
 */
function creerConcours($theme, $descriptif, $dateDeb, $dateFin, $numPresident, $etat = 'pas commence') {
    try {
        // Validation des dates
        $dateDebObj = new DateTime($dateDeb);
        $dateFinObj = new DateTime($dateFin);
        $aujourd_hui = new DateTime();
        $annee = (int)$dateDebObj->format('Y');  // Cast en int pour la vérification

        // Vérifications de base
        if ($dateDebObj < $aujourd_hui) {
            return ['success' => false, 'message' => 'La date de début ne peut pas être dans le passé'];
        }

        if ($dateFinObj <= $dateDebObj) {
            return ['success' => false, 'message' => 'La date de fin doit être postérieure à la date de début'];
        }

        // Vérification du nombre de concours pour l'année
        if (!verifierNombreConcours($annee)) {
            return ['success' => false, 'message' => 'Le nombre maximum de concours pour l\'année ' . $annee . ' est atteint (4 maximum)'];
        }

        // Vérification du président
        if (!verifierEligibilitePresident($numPresident)) {
            return ['success' => false, 'message' => 'La personne sélectionnée n\'est pas président'];
        }

        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $pdo->beginTransaction();

        try {
            // Génération d'un nouveau numConcours
            $sql = "SELECT COALESCE(MAX(numConcours), 0) + 1 as nextNum FROM Concours";
            $stmt = $pdo->query($sql);
            $numConcours = $stmt->fetch(PDO::FETCH_ASSOC)['nextNum'];

            // Insertion du concours
            $sql = "INSERT INTO Concours (numConcours, numPresident, theme, dateDeb, dateFin, etat, descriptif, nbClub, nbParticipant) 
                    VALUES (:numConcours, :numPresident, :theme, :dateDeb, :dateFin, :etat, :descriptif, 0, 0)";
            
            $stmt = $pdo->prepare($sql);
            $success = $stmt->execute([
                ':numConcours' => $numConcours,
                ':numPresident' => $numPresident,
                ':theme' => $theme,
                ':dateDeb' => $dateDeb,
                ':dateFin' => $dateFin,
                ':etat' => $etat,
                ':descriptif' => $descriptif
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

    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Erreur de base de données: ' . $e->getMessage()];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();
    
    // Vérifications d'authentification et CSRF...
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../../index.php');
        exit;
    }

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }

    // Validation des données
    $theme = isset($_POST['theme']) ? trim($_POST['theme']) : '';
    $descriptif = isset($_POST['descriptif']) ? trim($_POST['descriptif']) : '';
    $dateDeb = isset($_POST['dateDeb']) ? trim($_POST['dateDeb']) : '';
    $dateFin = isset($_POST['dateFin']) ? trim($_POST['dateFin']) : '';
    $numPresident = isset($_POST['president_id']) ? (int)$_POST['president_id'] : 0;
    $etat = 'pas commence';

    // Validations
    if (empty($theme) || empty($descriptif) || empty($dateDeb) || empty($dateFin) || empty($numPresident)) {
        $_SESSION['error'] = "Tous les champs obligatoires doivent être remplis";
        header('Location: admin.php');
        exit;
    }

    // Création du concours
    $result = creerConcours($theme, $descriptif, $dateDeb, $dateFin, $numPresident, $etat);

    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }

    header('Location: admin.php');
    exit;
} 