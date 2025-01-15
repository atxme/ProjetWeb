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

                <button type="submit" class="btn-submit">Ajouter l'utilisateur</button>
            </form>
        </div>
    </div>
    <script src="../../assets/js/admin.js"></script>
</body>
</html>
