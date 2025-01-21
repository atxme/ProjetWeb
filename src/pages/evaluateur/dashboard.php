<?php
if (isset($_GET['concours_id'])) {
    $concours_id = urldecode($_GET['concours_id']);
    // Recherchez les informations du concours en fonction de l'ID dans la base de données
    // Exemple :
    // $query = $pdo->prepare('SELECT * FROM Concours WHERE nom = :nom');
    // $query->execute(['nom' => $concours_id]);
    // $concours = $query->fetch(PDO::FETCH_ASSOC);

    echo "Vous consultez les détails pour le concours : " . htmlspecialchars($concours_id);
} else {
    echo "Aucun concours sélectionné.";
}
?>


<div class="evaluator-dashboard">
    <section class="pending-evaluations">
        <h2>Dessins à évaluer</h2>
        <!-- Liste des dessins à évaluer -->
    </section>
    <form class="evaluation-form">
        <input type="number" min="0" max="20" name="note" required>
        <textarea name="commentaire" required></textarea>
        <button type="submit">Soumettre l'évaluation</button>
    </form>
</div>
