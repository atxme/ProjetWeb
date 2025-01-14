<?php
require_once '../src/include/db.php';

$passwords = [
    'pwd123' => 'admin',
    'pwd124' => 'pdubois',
    'pwd125' => 'mmartin',
    'pwd126' => 'jdupont',
    'pwd127' => 'mdurand',
    'pwd128' => 'plefevre',
    'pwd129' => 'amoreau',
    'pwd130' => 'proux',
    'pwd131' => 'csimon',
    'pwd132' => 'lmichel',
    'pwd133' => 'sbertrand',
    'pwd134' => 'mpetit',
    'pwd135' => 'jlaurent',
    'pwd136' => 'tgirard',
    'pwd137' => 'amorel',
    'pwd140' => 'pleroy1',
    'pwd141' => 'cmoreau1',
    'pwd142' => 'mdupont1',
    'pwd160' => 'lbernard1',
    'pwd161' => 'ethomas1',
    'pwd162' => 'jrobert1',
    'pwd163' => 'lmichel1',
    'pwd164' => 'hdurand1',
    'pwd165' => 'alefebvre1'
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
