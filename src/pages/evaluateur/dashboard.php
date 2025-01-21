<?php
session_start();
require_once '../../include/db.php';

if (!isset($_GET['concours_id']) || empty($_GET['concours_id'])) {
    die('Erreur : Aucun concours sélectionné.');
}

$concours_id = urldecode($_GET['concours_id']);

try {
    // Connexion à la base de données
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Récupérer les informations du concours
    $concoursQuery = $pdo->prepare('
        SELECT c.theme, c.dateDeb, c.dateFin, c.etat
        FROM Concours c
        WHERE c.theme = :theme
    ');
    $concoursQuery->execute(['theme' => $concours_id]);
    $concours = $concoursQuery->fetch(PDO::FETCH_ASSOC);

    if (!$concours) {
        die('Erreur : Concours introuvable.');
    }

    $isFinished = ($concours['etat'] === 'fini');

    if ($isFinished) {
        // Si le concours est terminé, récupérer les notes attribuées
        $notesQuery = $pdo->prepare('
            SELECT d.nomDessin, d.auteur, ev.note, ev.commentaire
            FROM Evaluation ev
            INNER JOIN Dessin d ON ev.numDessin = d.numDessin
            WHERE ev.numConcours = (
                SELECT numConcours FROM Concours WHERE theme = :theme
            ) AND ev.numEvaluateur = :evaluateur_id
        ');
        $notesQuery->execute([
            'theme' => $concours_id,
            'evaluateur_id' => $_SESSION['user_id']
        ]);
        $notes = $notesQuery->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Si le concours est en cours, récupérer les dessins attribués
        $dessinsQuery = $pdo->prepare('
            SELECT d.numDessin, d.nomDessin, d.auteur
            FROM Dessin d
            WHERE d.numConcours = (
                SELECT numConcours FROM Concours WHERE theme = :theme
            ) AND d.numDessin NOT IN (
                SELECT numDessin FROM Evaluation WHERE numEvaluateur = :evaluateur_id
            )
        ');
        $dessinsQuery->execute([
            'theme' => $concours_id,
            'evaluateur_id' => $_SESSION['user_id']
        ]);
        $dessins = $dessinsQuery->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die('Erreur de base de données : ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Concours</title>
    <link rel="stylesheet" href="../../assets/css/details_concours.css">
</head>
<body>
    <div class="container">
        <h1>Détails du Concours : <?= htmlspecialchars($concours['theme']) ?></h1>
        <p>Dates : du <?= htmlspecialchars(date('d/m/Y', strtotime($concours['dateDeb']))) ?> 
           au <?= htmlspecialchars(date('d/m/Y', strtotime($concours['dateFin']))) ?></p>
        <p>Statut : <?= htmlspecialchars($concours['etat']) ?></p>

        <?php if ($isFinished): ?>
            <h2>Notes attribuées</h2>
            <table>
                <thead>
                    <tr>
                        <th>Dessin</th>
                        <th>Auteur</th>
                        <th>Note</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notes as $note): ?>
                        <tr>
                            <td><?= htmlspecialchars($note['nomDessin']) ?></td>
                            <td><?= htmlspecialchars($note['auteur']) ?></td>
                            <td><?= htmlspecialchars($note['note']) ?></td>
                            <td><?= htmlspecialchars($note['commentaire']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <h2>Dessins à évaluer</h2>
            <?php if (empty($dessins)): ?>
                <p>Aucun dessin à évaluer pour le moment.</p>
            <?php else: ?>
                <form action="noter_dessin.php" method="POST">
                    <input type="hidden" name="concours_id" value="<?= htmlspecialchars($concours_id) ?>">
                    <?php foreach ($dessins as $dessin): ?>
                        <div class="dessin-card">
                            <h3><?= htmlspecialchars($dessin['nomDessin']) ?></h3>
                            <p>Auteur : <?= htmlspecialchars($dessin['auteur']) ?></p>
                            <label for="note_<?= $dessin['numDessin'] ?>">Note :</label>
                            <input type="number" name="notes[<?= $dessin['numDessin'] ?>]" id="note_<?= $dessin['numDessin'] ?>" min="0" max="10" step="0.5" required>
                            <textarea name="commentaires[<?= $dessin['numDessin'] ?>]" placeholder="Commentaire..."></textarea>
                        </div>
                    <?php endforeach; ?>
                    <button type="submit">Envoyer les notes</button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
