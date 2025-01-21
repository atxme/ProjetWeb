<?php
session_start();
require_once '../../include/db.php';

// Vérifications de sécurité
if (!isset($_SESSION['user_id']) || 
    !isset($_SESSION['role']) || 
    $_SESSION['role'] !== 'admin' || 
    !isset($_SESSION['login'])) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

// Gestion du token CSRF
if (empty($_SESSION['csrf_token']) || 
    !isset($_SESSION['csrf_token_time']) || 
    (time() - $_SESSION['csrf_token_time']) > 3600) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_SESSION['csrf_token_time'] = time();
}

// Vérification de l'expiration de session
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Initialisation des variables
$db = Database::getInstance();
$conn = $db->getConnection();
$concoursId = isset($_GET['concours']) ? (int)$_GET['concours'] : null;
$concoursDetails = null;
$selectedYear = null;
$participants = [];
$evaluatedDrawings = [];
$allEvaluatedDrawings = [];
$order = isset($_POST['order']) ? $_POST['order'] : 'ASC';

// Récupération des détails du concours spécifique si demandé
if ($concoursId) {
    $stmt = $conn->prepare("
        SELECT c.*, 
               COUNT(DISTINCT cp.numCompetiteur) as nb_participants,
               COUNT(DISTINCT j.numEvaluateur) as nb_evaluateurs,
               COUNT(DISTINCT d.numDessin) as nb_dessins,
               COUNT(DISTINCT e.numEvaluation) as nb_evaluations,
               AVG(e.note) as moyenne_notes
        FROM Concours c
        LEFT JOIN CompetiteurParticipe cp ON c.numConcours = cp.numConcours
        LEFT JOIN Jury j ON c.numConcours = j.numConcours
        LEFT JOIN Dessin d ON c.numConcours = d.numConcours
        LEFT JOIN Evaluation e ON d.numDessin = e.numDessin
        WHERE c.numConcours = ?
        GROUP BY c.numConcours
    ");
    $stmt->execute([$concoursId]);
    $concoursDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupération des années pour le filtre
$stmt = $conn->prepare("SELECT DISTINCT YEAR(dateDeb) as year FROM Concours ORDER BY year DESC");
$stmt->execute();
$years = $stmt->fetchAll();

// Traitement du formulaire de filtre par année
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['year'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token');
    }
    
    $selectedYear = $_POST['year'];
    $order = $_POST['order'] ?? 'ASC';
    
    // Fetch concours details for the selected year
    $stmt = $conn->prepare("SELECT theme, descriptif, dateDeb, dateFin FROM Concours WHERE YEAR(dateDeb) = ?");
    $stmt->execute([$selectedYear]);
    $concoursDetails = $stmt->fetchAll();

    // Fetch participants for the selected year
    $stmt = $conn->prepare("
        SELECT u.nom, u.prenom, u.age, u.adresse, c.nomClub, c.departement, c.region
        FROM CompetiteurParticipe cp
        JOIN Utilisateur u ON cp.numCompetiteur = u.numUtilisateur
        JOIN Club c ON u.numClub = c.numClub
        JOIN Concours co ON cp.numConcours = co.numConcours
        WHERE YEAR(co.dateDeb) = ?
    ");
    $stmt->execute([$selectedYear]);
    $participants = $stmt->fetchAll();

    // Fetch evaluated drawings for the selected year
    $stmt = $conn->prepare("
        SELECT d.numDessin, e.note, u.nom, co.descriptif, co.theme
        FROM Evaluation e
        JOIN Dessin d ON e.numDessin = d.numDessin
        JOIN Utilisateur u ON d.numCompetiteur = u.numUtilisateur
        JOIN Concours co ON d.numConcours = co.numConcours
        WHERE YEAR(co.dateDeb) = ?
        ORDER BY e.note $order
    ");
    $stmt->execute([$selectedYear]);
    $evaluatedDrawings = $stmt->fetchAll();
}

// Fetch all evaluated drawings
$stmt = $conn->prepare("
    SELECT d.numDessin, YEAR(co.dateDeb) as year, co.descriptif, u.nom as competiteur, d.commentaire as dessin_comment, e.note, e.commentaire as evaluation_comment, eval.nom as evaluateur
    FROM Evaluation e
    JOIN Dessin d ON e.numDessin = d.numDessin
    JOIN Utilisateur u ON d.numCompetiteur = u.numUtilisateur
    JOIN Concours co ON d.numConcours = co.numConcours
    JOIN Utilisateur eval ON e.numEvaluateur = eval.numUtilisateur
");
$stmt->execute();
$allEvaluatedDrawings = $stmt->fetchAll();

// Récupérer les compétiteurs qui ont participé à tous les concours
$stmt = $conn->prepare("
    SELECT u.nom, u.prenom, u.age
    FROM Utilisateur u
    JOIN CompetiteurParticipe cp ON u.numUtilisateur = cp.numCompetiteur
    GROUP BY u.numUtilisateur
    HAVING COUNT(DISTINCT cp.numConcours) = (SELECT COUNT(*) FROM Concours)
    ORDER BY u.age " . $order);
$stmt->execute();
$allContestParticipants = $stmt->fetchAll();


// Si un concours spécifique est demandé
if ($concoursId) {
    // Récupérer les détails du concours
    $stmt = $conn->prepare("
        SELECT c.*, 
               COUNT(DISTINCT cp.numCompetiteur) as nb_participants,
               COUNT(DISTINCT j.numEvaluateur) as nb_evaluateurs,
               COUNT(DISTINCT d.numDessin) as nb_dessins,
               COUNT(DISTINCT e.numEvaluation) as nb_evaluations
        FROM Concours c
        LEFT JOIN CompetiteurParticipe cp ON c.numConcours = cp.numConcours
        LEFT JOIN Jury j ON c.numConcours = j.numConcours
        LEFT JOIN Dessin d ON c.numConcours = d.numConcours
        LEFT JOIN Evaluation e ON d.numDessin = e.numDessin
        WHERE c.numConcours = ?
        GROUP BY c.numConcours
    ");
    $stmt->execute([$concoursId]);
    $concoursDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($concoursDetails) {
        // Afficher les statistiques spécifiques au concours
        ?>
        <div class="container">
            <h1><?php echo htmlspecialchars($concoursDetails['theme']); ?></h1>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Participants</h3>
                    <div class="stat-number"><?php echo $concoursDetails['nb_participants']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Évaluateurs</h3>
                    <div class="stat-number"><?php echo $concoursDetails['nb_evaluateurs']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Dessins soumis</h3>
                    <div class="stat-number"><?php echo $concoursDetails['nb_dessins']; ?></div>
                </div>
                <div class="stat-card">
                    <h3>Évaluations réalisées</h3>
                    <div class="stat-number"><?php echo $concoursDetails['nb_evaluations']; ?></div>
                </div>
            </div>
            
            <!-- Ajouter d'autres statistiques spécifiques au concours ici -->
        </div>
        <?php
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Administration</title>
    <link rel="stylesheet" href="../../assets/css/statistics.css">
</head>
<body>
    <div class="status-bar">
        <div class="status">
            <?php echo htmlspecialchars($_SESSION['login']); ?> : 
            <span class="role-badge"><?php echo ucfirst(htmlspecialchars($_SESSION['role'])); ?></span>
        </div>
        <div class="nav-buttons">
            <a href="admin.php" class="btn-stats">Retour</a>
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

    <?php if ($concoursDetails): ?>
    <div class="container">
        <h1><?php echo htmlspecialchars($concoursDetails['theme']); ?></h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Participants</h3>
                <div class="stat-number"><?php echo $concoursDetails['nb_participants']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Évaluateurs</h3>
                <div class="stat-number"><?php echo $concoursDetails['nb_evaluateurs']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Dessins soumis</h3>
                <div class="stat-number"><?php echo $concoursDetails['nb_dessins']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Évaluations réalisées</h3>
                <div class="stat-number"><?php echo $concoursDetails['nb_evaluations']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Moyenne des notes</h3>
                <div class="stat-number">
                    <?php echo $concoursDetails['moyenne_notes'] ? number_format($concoursDetails['moyenne_notes'], 2) : 'N/A'; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container">
        <h1>Statistiques des Concours</h1>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="year">Sélectionnez une année</label>
                <select name="year" id="year" required>
                    <?php foreach ($years as $year): ?>
                        <option value="<?php echo htmlspecialchars($year['year']); ?>" <?php echo ($selectedYear == $year['year']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($year['year']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="order">Ordre de tri</label>
                <select name="order" id="order" required>
                    <option value="ASC" <?php echo ($order == 'ASC') ? 'selected' : ''; ?>>Croissant</option>
                    <option value="DESC" <?php echo ($order == 'DESC') ? 'selected' : ''; ?>>Décroissant</option>
                </select>
            </div>
            <button type="submit" class="btn">Afficher les statistiques</button>
        </form>

        <?php if ($selectedYear && $concoursDetails): ?>
            <h2>Détails des Concours de l'année <?php echo htmlspecialchars($selectedYear); ?></h2>
            <?php foreach ($concoursDetails as $concoursDetail): ?>
                <div class="concours-detail">
                    <p><strong>Thème:</strong> <?php echo htmlspecialchars($concoursDetail['theme']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($concoursDetail['descriptif']); ?></p>
                    <p><strong>Date de début:</strong> <?php echo htmlspecialchars($concoursDetail['dateDeb']); ?></p>
                    <p><strong>Date de fin:</strong> <?php echo htmlspecialchars($concoursDetail['dateFin']); ?></p>
                </div>
            <?php endforeach; ?>

            <h2>Participants</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Âge</th>
                        <th>Adresse</th>
                        <th>Club</th>
                        <th>Département</th>
                        <th>Région</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($participant['nom']); ?></td>
                            <td><?php echo htmlspecialchars($participant['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($participant['age']); ?></td>
                            <td><?php echo htmlspecialchars($participant['adresse']); ?></td>
                            <td><?php echo htmlspecialchars($participant['nomClub']); ?></td>
                            <td><?php echo htmlspecialchars($participant['departement']); ?></td>
                            <td><?php echo htmlspecialchars($participant['region']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Dessins Évalués</h2>
            <table>
                <thead>
                    <tr>
                        <th>Numéro du Dessin</th>
                        <th>Note Attribuée</th>
                        <th>Nom du Compétiteur</th>
                        <th>Description du Concours</th>
                        <th>Thème du Concours</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluatedDrawings as $drawing): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($drawing['numDessin']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['note']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['nom']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['descriptif']); ?></td>
                            <td><?php echo htmlspecialchars($drawing['theme']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="container">
        <h2>Tous les Dessins Évalués</h2>
        <table>
            <thead>
                <tr>
                    <th>Numéro du Dessin</th>
                    <th>Année</th>
                    <th>Description du Concours</th>
                    <th>Nom du Compétiteur</th>
                    <th>Commentaire du Dessin</th>
                    <th>Note</th>
                    <th>Commentaire de l'Évaluation</th>
                    <th>Nom de l'Évaluateur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($allEvaluatedDrawings as $drawing): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($drawing['numDessin']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['year']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['descriptif']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['competiteur']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['dessin_comment']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['note']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['evaluation_comment']); ?></td>
                        <td><?php echo htmlspecialchars($drawing['evaluateur']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="container mt-4">
    <h3>Compétiteurs ayant participé à tous les concours</h3>
    
    <form method="POST" class="mb-3">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <select name="order" class="form-select" onchange="this.form.submit()">
            <option value="ASC" <?php echo $order === 'ASC' ? 'selected' : ''; ?>>Âge croissant</option>
            <option value="DESC" <?php echo $order === 'DESC' ? 'selected' : ''; ?>>Âge décroissant</option>
        </select>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Âge</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($allContestParticipants)): ?>
                <?php foreach ($allContestParticipants as $participant): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($participant['nom']); ?></td>
                        <td><?php echo htmlspecialchars($participant['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($participant['age']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center">Aucun compétiteur n'a participé à tous les concours</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>