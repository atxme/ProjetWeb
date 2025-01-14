<?php
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

foreach ($passwords as $pwd => $login) {
    $hash = password_hash($pwd, PASSWORD_DEFAULT);
    echo "UPDATE Utilisateur SET mdp = '$hash' WHERE login = '$login';\n";
}
