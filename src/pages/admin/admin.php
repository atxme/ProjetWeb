<?php
session_start();
require_once '../../include/db.php';

// Vérification plus stricte de l'authentification et du rôle
if (!isset($_SESSION['user_id']) || 
    !isset($_SESSION['role']) || 
    $_SESSION['role'] !== 'admin' || 
    !isset($_SESSION['login'])) {
    // Détruire la session si l'accès est non autorisé
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

// Régénérer le token CSRF si nécessaire
if (empty($_SESSION['csrf_token']) || 
    !isset($_SESSION['csrf_token_time']) || 
    (time() - $_SESSION['csrf_token_time']) > 3600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Vérifier l'expiration de la session (30 minutes d'inactivité)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Ajouter ces fonctions au début du fichier admin.php après les vérifications de session

function verifierEligibiliteCompetiteur($numUtilisateur, $numConcours) {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Vérifier que l'utilisateur n'est pas déjà compétiteur, évaluateur ou président dans ce concours
    $sql = "SELECT 1 FROM CompetiteurParticipe WHERE numCompetiteur = :numUtilisateur AND numConcours = :numConcours
            UNION
            SELECT 1 FROM Jury WHERE numEvaluateur = :numUtilisateur AND numConcours = :numConcours
            UNION
            SELECT 1 FROM Concours WHERE numPresident = :numUtilisateur AND numConcours = :numConcours";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':numUtilisateur' => $numUtilisateur,
        ':numConcours' => $numConcours
    ]);
    
    return $stmt->rowCount() === 0;
}

function verifierEligibiliteEvaluateur($numUtilisateur, $numConcours) {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    // Vérifier le nombre total d'évaluations
    $sql = "SELECT COUNT(*) FROM Evaluation WHERE numEvaluateur = :numUtilisateur";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':numUtilisateur' => $numUtilisateur]);
    if ($stmt->fetchColumn() >= 8) {
        return false;
    }
    
    // Vérifier les autres conditions (pas compétiteur/président dans ce concours)
    return verifierEligibiliteCompetiteur($numUtilisateur, $numConcours);
}

function ajouterParticipant($userType, $numUtilisateur, $numConcours, $numClub) {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    try {
        $pdo->beginTransaction();

        // Vérifier l'éligibilité selon le type
        $eligible = ($userType === 'evaluateur') ? 
            verifierEligibiliteEvaluateur($numUtilisateur, $numConcours) : 
            verifierEligibiliteCompetiteur($numUtilisateur, $numConcours);

        if (!$eligible) {
            throw new Exception("L'utilisateur n'est pas éligible pour ce rôle");
        }

        // Ajouter le participant selon son type
        if ($userType === 'evaluateur') {
            // Vérifier si l'utilisateur est déjà évaluateur
            $sql = "INSERT IGNORE INTO Evaluateur (numEvaluateur, specialite) VALUES (:numUtilisateur, 'générale')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numUtilisateur' => $numUtilisateur]);

            // Ajouter au jury
            $sql = "INSERT INTO Jury (numEvaluateur, numConcours) VALUES (:numUtilisateur, :numConcours)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':numUtilisateur' => $numUtilisateur,
                ':numConcours' => $numConcours
            ]);
        } else {
            // D'abord ajouter l'utilisateur comme compétiteur s'il ne l'est pas déjà
            $sql = "INSERT IGNORE INTO Competiteur (numCompetiteur, datePremiereParticipation) 
                    VALUES (:numUtilisateur, CURRENT_DATE)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numUtilisateur' => $numUtilisateur]);

            // Ensuite l'ajouter à la participation au concours
            $sql = "INSERT INTO CompetiteurParticipe (numCompetiteur, numConcours) 
                    VALUES (:numUtilisateur, :numConcours)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':numUtilisateur' => $numUtilisateur,
                ':numConcours' => $numConcours
            ]);
        }

        // Ajouter le club au concours s'il n'y est pas déjà
        $sql = "INSERT IGNORE INTO ClubParticipe (numClub, numConcours) VALUES (:numClub, :numConcours)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':numClub' => $numClub,
            ':numConcours' => $numConcours
        ]);

        $pdo->commit();
        return ['success' => true, 'message' => 'Participant ajouté avec succès au concours'];

    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
        }
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Modifier le traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userType'])) {
    header('Content-Type: application/json');
    
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode([
            'success' => false,
            'message' => "Token CSRF invalide"
        ]);
        exit;
    }

    $userType = $_POST['userType'];
    $numUtilisateur = (int)$_POST['utilisateur'];
    $numConcours = (int)$_POST['concours'];
    $numClub = (int)$_POST['club'];

    // Validation des données
    if (!$numUtilisateur || !$numConcours || !$numClub) {
        echo json_encode([
            'success' => false,
            'message' => "Tous les champs sont obligatoires"
        ]);
        exit;
    }

    $result = ajouterParticipant($userType, $numUtilisateur, $numConcours, $numClub);
    echo json_encode($result);
    exit;
}

// Ajouter après les vérifications de session
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getUsers') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit;
    }

    $club = (int)$_GET['club'];
    $type = $_GET['type'];
    $concours = (int)$_GET['concours'];

    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        if ($type === 'competiteur') {
            // Sélectionner les utilisateurs éligibles comme compétiteurs
            $sql = "SELECT u.numUtilisateur, u.nom, u.prenom 
                    FROM Utilisateur u
                    WHERE u.numClub = :club
                    AND NOT EXISTS (
                        SELECT 1 FROM CompetiteurParticipe cp 
                        WHERE cp.numCompetiteur = u.numUtilisateur 
                        AND cp.numConcours = :concours
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM Jury j 
                        WHERE j.numEvaluateur = u.numUtilisateur 
                        AND j.numConcours = :concours
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM Concours c 
                        WHERE c.numPresident = u.numUtilisateur 
                        AND c.numConcours = :concours
                    )";
        } else {
            // Sélectionner les utilisateurs éligibles comme évaluateurs
            $sql = "SELECT u.numUtilisateur, u.nom, u.prenom 
                    FROM Utilisateur u
                    LEFT JOIN Evaluateur e ON u.numUtilisateur = e.numEvaluateur
                    WHERE u.numClub = :club
                    AND NOT EXISTS (
                        SELECT 1 FROM Jury j 
                        WHERE j.numEvaluateur = u.numUtilisateur 
                        AND j.numConcours = :concours
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM CompetiteurParticipe cp 
                        WHERE cp.numCompetiteur = u.numUtilisateur 
                        AND cp.numConcours = :concours
                    )
                    AND NOT EXISTS (
                        SELECT 1 FROM Concours c 
                        WHERE c.numPresident = u.numUtilisateur 
                        AND c.numConcours = :concours
                    )
                    AND (
                        SELECT COUNT(*) FROM Evaluation ev 
                        WHERE ev.numEvaluateur = u.numUtilisateur
                    ) < 8";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':club' => $club, ':concours' => $concours]);
        echo json_encode($stmt->fetchAll());
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Ajouter après la route getUsers
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getClubs') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        exit;
    }

    $concours = (int)$_GET['concours'];

    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();

        // Sélectionner les clubs qui ont des utilisateurs et qui ne participent pas déjà au concours
        $sql = "SELECT DISTINCT c.numClub, c.nomClub 
                FROM Club c 
                INNER JOIN Utilisateur u ON c.numClub = u.numClub
                WHERE NOT EXISTS (
                    SELECT 1 FROM ClubParticipe cp 
                    WHERE cp.numClub = c.numClub 
                    AND cp.numConcours = :concours
                )";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':concours' => $concours]);
        echo json_encode($stmt->fetchAll());
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
        exit;
    }
}

// Dans la partie traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['theme'])) {
    try {
        // Obtenir la connexion à la base de données
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Validation côté serveur
        if (empty($_POST['theme']) || strlen($_POST['theme']) < 3) {
            throw new Exception("Le thème est invalide");
        }
        if (empty($_POST['description']) || strlen($_POST['description']) < 10) {
            throw new Exception("La description est invalide");
        }
        if (empty($_POST['dateDeb']) || empty($_POST['dateFin'])) {
            throw new Exception("Les dates sont invalides");
        }

        // Vérification des dates
        $dateDeb = new DateTime($_POST['dateDeb']);
        $dateFin = new DateTime($_POST['dateFin']);
        $today = new DateTime();
        
        if ($dateDeb < $today) {
            throw new Exception("La date de début ne peut pas être dans le passé");
        }
        if ($dateFin <= $dateDeb) {
            throw new Exception("La date de fin doit être après la date de début");
        }

        // Insertion dans la base de données
        $stmt = $conn->prepare("INSERT INTO Concours (theme, descriptif, dateDeb, dateFin, etat) VALUES (?, ?, ?, ?, 'pas commence')");
        $success = $stmt->execute([$_POST['theme'], $_POST['description'], $_POST['dateDeb'], $_POST['dateFin']]);

        if (!$success) {
            throw new Exception("Erreur lors de l'insertion en base de données");
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Concours créé avec succès']);
        exit;

    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Concours de Dessin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <?php if (isset($_SESSION['success'])): ?>
        <div id="success-message" data-message="<?php echo htmlspecialchars($_SESSION['success']); ?>" style="display: none;"></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div id="error-message" data-message="<?php echo htmlspecialchars($_SESSION['error']); ?>" style="display: none;"></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    <div class="status-bar">
        <div class="status">
            <?php echo htmlspecialchars($_SESSION['login']); ?> : 
            <span class="role-badge"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></span>
        </div>
        <div class="nav-buttons">
            <a href="statistics.php" class="btn-stats">Statistiques</a>
            <?php
            if(isset($_GET['logout'])) {
                session_destroy();
                header('Location: ../../index.php');
                exit;
            }
            ?>
            <a href="?logout=true" class="btn-logout">Déconnexion</a>
        </div>
    </div>
    <div class="admin-container">
        <!-- Colonne gauche - Formulaires existants -->
        <div class="admin-forms">
            <div class="admin-box">
                <div class="admin-header">
                    <h2>Gestion des Concours</h2>
                </div>
                <form id="concoursForm" method="post" action="concourManagement.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    
                    <div class="form-group">
                        <label for="theme">Thème du concours*</label>
                        <input type="text" id="theme" name="theme" maxlength="100" required>
                    </div>

                    <div class="form-group">
                        <label for="descriptif">Descriptif détaillé*</label>
                        <textarea id="descriptif" name="descriptif" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="dateDeb">Date de début*</label>
                        <input type="date" id="dateDeb" name="dateDeb" required>
                    </div>

                    <div class="form-group">
                        <label for="dateFin">Date de fin*</label>
                        <input type="date" id="dateFin" name="dateFin" required>
                    </div>

                    <div class="form-group">
                        <label for="president_id">Président du concours*</label>
                        <select id="president_id" name="president_id" required>
                            <option value="">Sélectionner un président</option>
                            <?php
                            $db = Database::getInstance();
                            $pdo = $db->getConnection();
                            $sql = "SELECT DISTINCT u.numUtilisateur, u.nom, u.prenom 
                                    FROM Utilisateur u 
                                    INNER JOIN President p ON u.numUtilisateur = p.numPresident 
                                    LEFT JOIN Concours c ON p.numPresident = c.numPresident 
                                    WHERE c.numConcours IS NULL 
                                       OR c.etat NOT IN ('pas commence', 'en cours')
                                    GROUP BY u.numUtilisateur";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($row['numUtilisateur']) . '">' . 
                                     htmlspecialchars($row['prenom'] . ' ' . $row['nom']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nbClubMin">Nombre minimum de clubs requis*</label>
                        <input type="number" id="nbClubMin" name="nbClubMin" 
                               min="1" max="12" value="1" required>
                        <small>Le nombre de clubs doit être compris entre 1 et 12</small>
                    </div>

                    <div class="form-group">
                        <label for="nbParticipantMin">Nombre minimum de participants par club*</label>
                        <input type="number" id="nbParticipantMin" name="nbParticipantMin" 
                               min="1" max="12" value="1" required>
                        <small>Le nombre de participants par club doit être compris entre 1 et 12</small>
                    </div>

                    <div class="form-group">
                        <label for="etat">État du concours</label>
                        <select id="etat" name="etat" disabled>
                            <option value="pas commence" selected>Pas commencé</option>
                            <option value="en cours">En cours</option>
                            <option value="attente">En attente</option>
                            <option value="resultat">Résultats</option>
                            <option value="evalue">Évalué</option>
                        </select>
                        <input type="hidden" name="etat" value="pas commence">
                    </div>

                    <button type="submit" class="btn-submit">Créer le concours</button>
                    <p class="form-info">* Champs obligatoires</p>
                </form>
            </div>

            <div class="admin-box">
                <div class="admin-header">
                    <h2>Ajout de Participants au Concours</h2>
                </div>
                <form id="userForm" method="post" action="admin.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    
                    <div class="form-group">
                        <label for="concours">Sélectionner un concours*</label>
                        <select id="concours" name="concours" required>
                            <option value="">Choisir un concours</option>
                            <?php
                            $db = Database::getInstance();
                            $pdo = $db->getConnection();
                            $sql = "SELECT numConcours, theme 
                                    FROM Concours 
                                    WHERE etat IN ('pas commence', 'en cours')
                                    ORDER BY dateDeb DESC";
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . htmlspecialchars($row['numConcours']) . '">' . 
                                     htmlspecialchars($row['theme']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="club">Sélectionner un club*</label>
                        <select id="club" name="club" required disabled>
                            <option value="">Choisir d'abord un concours</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="userType">Type de participation*</label>
                        <select id="userType" name="userType" required>
                            <option value="">Choisir un rôle</option>
                            <option value="competiteur">Compétiteur</option>
                            <option value="evaluateur">Évaluateur</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="utilisateur">Sélectionner un utilisateur*</label>
                        <select id="utilisateur" name="utilisateur" required disabled>
                            <option value="">Choisir d'abord un club et un type</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-submit">Ajouter au concours</button>
                    <p class="form-info">* Champs obligatoires</p>
                </form>
            </div>
        </div>

        <!-- Nouvelle colonne droite - Liste des concours -->
        <div class="concours-list">
            <div class="header">
                <h2>Prochains Concours</h2>
            </div>
            <div class="concours-cards-container">
                <?php
                $db = Database::getInstance();
                $pdo = $db->getConnection();
                $sql = "SELECT numConcours, theme, dateDeb, dateFin, etat 
                        FROM Concours 
                        WHERE dateFin >= CURRENT_DATE()
                        ORDER BY dateDeb ASC
                        LIMIT 12";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                
                while ($concours = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $statusClass = match($concours['etat']) {
                        'pas commence' => 'status-not-started',
                        'en cours' => 'status-in-progress',
                        'evaluation' => 'status-evaluation',
                        'termine' => 'status-finished',
                        default => 'status-not-started'
                    };
                    ?>
                    <a href="statistics.php?concours=<?php echo $concours['numConcours']; ?>" 
                       class="concours-card">
                        <div class="status-indicator <?php echo $statusClass; ?>"></div>
                        <div class="concours-info">
                            <div class="concours-title"><?php echo htmlspecialchars($concours['theme']); ?></div>
                            <div class="concours-date">
                                Du <?php echo date('d/m/Y', strtotime($concours['dateDeb'])); ?>
                                au <?php echo date('d/m/Y', strtotime($concours['dateFin'])); ?>
                            </div>
                            <div class="concours-status">
                                <?php echo ucfirst(htmlspecialchars($concours['etat'])); ?>
                            </div>
                        </div>
                    </a>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
