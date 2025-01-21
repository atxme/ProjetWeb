<?php
require_once '/srv/siteweb/dessin/include/db.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    // Récupère tous les utilisateurs
    $stmt = $pdo->query("SELECT login, mdp FROM Utilisateur");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prépare la requête de mise à jour
    $updateStmt = $pdo->prepare("UPDATE Utilisateur SET mdp = :hash WHERE login = :login");

    // Pour chaque utilisateur, hash son mot de passe actuel
    foreach ($users as $user) {
        $hash = password_hash($user['mdp'], PASSWORD_DEFAULT);
        $updateStmt->execute([
            'hash' => $hash,
            'login' => $user['login']
        ]);
        echo "Mot de passe mis à jour pour {$user['login']}\n";
    }

    echo "Tous les mots de passe ont été mis à jour avec succès!\n";
} catch (PDOException $e) {
    die("Erreur lors de la mise à jour des mots de passe : " . $e->getMessage());
}
