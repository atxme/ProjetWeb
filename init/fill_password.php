<?php
require_once '/srv/siteweb/dessin/include/db.php';

$passwords = [
    // Admin
    'pwd123' => 'admin',

    // Présidents
    'pwd124' => 'pdubois',
    'pwd125' => 'mmartin',
    'pwd126' => 'jpetit',
    'pwd127' => 'proux',
    'pwd128' => 'cblanc',

    // Directeurs
    'pwd130' => 'sdurand',
    'pwd131' => 'mlambert',
    'pwd132' => 'agarcia',

    // Evaluateurs
    'pwd140' => 'pleroy',
    'pwd141' => 'cmoreau',
    'pwd142' => 'mdupont',
    'pwd143' => 'jsimon',
    'pwd144' => 'tlaurent',

    // Compétiteurs
    'pwd160' => 'lbernard',
    'pwd161' => 'ethomas',
    'pwd162' => 'srichard',
    'pwd163' => 'agirard',
    'pwd164' => 'lmorel',
    'pwd165' => 'mfournier'
];

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $stmt = $pdo->prepare("UPDATE Utilisateur SET mdp = :hash WHERE login = :login");

    foreach ($passwords as $pwd => $login) {
        $hash = password_hash($pwd, PASSWORD_DEFAULT);
        $stmt->execute([
            'hash' => $hash,
            'login' => $login
        ]);
        echo "Mot de passe mis à jour pour $login\n";
    }

    echo "Tous les mots de passe ont été mis à jour avec succès!\n";
} catch (PDOException $e) {
    die("Erreur lors de la mise à jour des mots de passe : " . $e->getMessage());
}
