<?php

namespace App\Service;

use Firebase\JWT\JWT;

class JWTManager
{
    private $secretKey = 'votre_clé_secrète';

    public function decodeToken(string $token): ?string
    {
        try {
            $algorithms = ['HS256'];
            $decoded = JWT::decode($token, $this->secretKey, $algorithms);
            return $decoded->role;  // Exemple : retourner le rôle décodé
        } catch (\Exception $e) {
            return null;  // Si le token est invalide ou expiré
        }
    }
}
