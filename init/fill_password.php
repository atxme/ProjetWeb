<?php
require_once '/srv/siteweb/dessin/include/db.php';

$passwords = [
    'pwd1231' => 'admin',
    'pwd124' => 'pdubois',
    'pwd125' => 'mmartin', 
    'pwd126' => 'jdupont',
    'pwd127' => 'mdurand',
    'pwd128' => 'plefevre',
    'pwd129' => 'amoreau',
    'pwd130' => 'croux',
    'pwd131' => 'csimon',
    'pwd140' => 'pleroy1',
    'pwd141' => 'cmoreau1',
    'pwd160' => 'lbernard1',
    'pwd161' => 'ethomas1',
    'pwd162' => 'spetit',
    'pwd163' => 'mrobert',
    'pwd164' => 'jrichard',
    'pwd165' => 'tlaurent',
    'pwd166' => 'lgarcia',
    'pwd167' => 'amichel',
    'pwd168' => 'sdavid',
    'pwd169' => 'nbertrand',
    'pwd170' => 'iroux',
    'pwd171' => 'pvincent',
    'pwd172' => 'afournier',
    'pwd173' => 'emorel',
    'pwd174' => 'candre',
    'pwd175' => 'dlefevre',
    'pwd176' => 'nmercier',
    'pwd177' => 'sblanc',
    'pwd178' => 'cguerin',
    'pwd179' => 'lboyer',
    'pwd180' => 'vgarnier',
    'pwd181' => 'pchevalier',
    'pwd182' => 'sfrancois',
    'pwd183' => 'jlegrand',
    'pwd184' => 'srousseau',
    'pwd185' => 'fgauthier',
    'pwd186' => 'clopez'
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
