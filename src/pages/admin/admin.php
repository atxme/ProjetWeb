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

// Ajouter cette fonction pour gérer l'ajout d'utilisateur
function ajouterUtilisateur($userType, $nom, $prenom, $age, $adresse, $numConcours) {
    try {
        $db = Database::getInstance();
        $pdo = $db->getConnection();
        $pdo->beginTransaction();

        // Générer un nouveau numUtilisateur
        $sql = "SELECT COALESCE(MAX(numUtilisateur), 0) + 1 as nextNum FROM Utilisateur";
        $stmt = $pdo->query($sql);
        $numUtilisateur = $stmt->fetch(PDO::FETCH_ASSOC)['nextNum'];

        // Générer un login unique (première lettre du prénom + nom + numéro)
        $login = strtolower(substr($prenom, 0, 1) . $nom . $numUtilisateur);
        // Générer un mot de passe temporaire
        $password = bin2hex(random_bytes(4)); // 8 caractères aléatoires
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur
        $sql = "INSERT INTO Utilisateur (numUtilisateur, nom, prenom, age, adresse, login, mdp) 
                VALUES (:numUtilisateur, :nom, :prenom, :age, :adresse, :login, :mdp)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':numUtilisateur' => $numUtilisateur,
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':age' => $age,
            ':adresse' => $adresse,
            ':login' => $login,
            ':mdp' => $hashedPassword
        ]);

        // Insérer dans la table spécifique selon le type
        if ($userType === 'evaluateur') {
            $sql = "INSERT INTO Evaluateur (numEvaluateur, specialite) VALUES (:numUtilisateur, 'générale')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numUtilisateur' => $numUtilisateur]);

            // Ajouter au jury du concours
            $sql = "INSERT INTO Jury (numEvaluateur, numConcours) VALUES (:numEvaluateur, :numConcours)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':numEvaluateur' => $numUtilisateur,
                ':numConcours' => $numConcours
            ]);
        } else {
            $sql = "INSERT INTO Competiteur (numCompetiteur, datePremiereParticipation) 
                    VALUES (:numUtilisateur, CURRENT_DATE)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':numUtilisateur' => $numUtilisateur]);

            // Ajouter à la table CompetiteurParticipe
            $sql = "INSERT INTO CompetiteurParticipe (numCompetiteur, numConcours) 
                    VALUES (:numCompetiteur, :numConcours)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':numCompetiteur' => $numUtilisateur,
                ':numConcours' => $numConcours
            ]);
        }

        $pdo->commit();
        return [
            'success' => true,
            'message' => "Utilisateur créé avec succès. Login: $login, Mot de passe temporaire: $password"
        ];

    } catch (Exception $e) {
        $pdo->rollBack();
        return [
            'success' => false,
            'message' => "Erreur lors de la création de l'utilisateur: " . $e->getMessage()
        ];
    }
}

// Traitement du formulaire d'ajout d'utilisateur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userType'])) {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = "Token CSRF invalide";
        header('Location: admin.php');
        exit;
    }

    $userType = $_POST['userType'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $age = (int)$_POST['age'];
    $adresse = trim($_POST['adresse']);
    $numConcours = (int)$_POST['concours'];

    // Validation des données
    if (empty($nom) || empty($prenom) || empty($adresse) || $age <= 0 || $numConcours <= 0) {
        $_SESSION['error'] = "Tous les champs sont obligatoires";
        header('Location: admin.php');
        exit;
    }

    $result = ajouterUtilisateur($userType, $nom, $prenom, $age, $adresse, $numConcours);
    
    if ($result['success']) {
        $_SESSION['success'] = $result['message'];
    } else {
        $_SESSION['error'] = $result['message'];
    }
    
    header('Location: admin.php');
    exit;
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
            <?php 
            echo htmlspecialchars($_SESSION['login']) . ' : ' . 
                 ucfirst(htmlspecialchars($_SESSION['role'])); 
            ?>
        </div>
        <div class="logout">
            <?php
            if(isset($_GET['logout'])) {
                session_destroy();
                header('Location: ../../index.php');
                exit;
            }
            ?>
            <a href="?logout=true">Déconnexion</a>
        </div>
    </div>
    <div class="admin-container">
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
                <h2>Gestion des Utilisateurs</h2>
            </div>
            <form id="userForm" method="post" action="process_user.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                <div class="form-group">
                    <label for="userType">Type d'utilisateur</label>
                    <select id="userType" name="userType">
                        <option value="evaluateur">Évaluateur</option>
                        <option value="competiteur">Compétiteur</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>

                <div class="form-group">
                    <label for="age">Âge</label>
                    <input type="number" id="age" name="age" required>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" required>
                </div>

                <div class="form-group">
                    <label for="concours">Concours*</label>
                    <select id="concours" name="concours" required>
                        <option value="">Sélectionner un concours</option>
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

                <button type="submit" class="btn-submit">Ajouter l'utilisateur</button>
            </form>
        </div>
    </div>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
