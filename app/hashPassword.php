<?php

// Le mot de passe que vous souhaitez hacher
$password = "mot2pass";

// Hachage du mot de passe avec bcrypt
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Affichage du mot de passe haché
echo "Mot de passe haché: " . $hashedPassword . "\n";
?>
