<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTManager
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Crée un JWT pour un utilisateur.
     *
     * @param User $user
     * @return string
     */
    public function createJWT(User $user): string
    {
        // Récupérer le nom du rôle depuis l'entité Role
        $roleName = $user->getHasRole() ? $user->getHasRole()->getName() : 'ROLE_USER';  // Valeur par défaut

        // Créer le payload avec les données nécessaires
        $payload = [
            'user_id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => [$roleName],  // Utilisation du rôle récupéré de l'entité Role
            'exp' => (new \DateTime('+1 hour'))->getTimestamp(),  // Expiration dans 1 heure
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    /**
     * Décoder et vérifier un JWT.
     *
     * @param string $token
     * @return array|null
     */
    public function parseJWT(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            // Si le token est invalide ou expiré, retourner null
            return null;
        }
    }

    /**
     * Vérifie un token JWT et retourne les informations de l'utilisateur, y compris le rôle.
     *
     * @param string $token
     * @return array|null
     */
    public function verifyToken(string $token): ?array
    {
        // Décoder le token pour vérifier sa validité
        $decoded = $this->parseJWT($token);

        // Si le token est valide, retourner les données du token incluant le rôle
        if ($decoded && isset($decoded['roles'])) {
            return [
                'role' => $decoded['roles'][0], // Premier rôle de l'utilisateur
                'user_id' => $decoded['user_id'],
                'email' => $decoded['email'],
            ];
        }

        // Si le token est invalide ou ne contient pas de rôle, retourner null
        return null;
    }

    /**
     * Alias pour parseJWT - Décoder un token JWT.
     *
     * @param string $token
     * @return array|null
     */
    public function decodeToken(string $token): ?array
    {
        // Réutilisation de la méthode parseJWT pour éviter la redondance
        return $this->parseJWT($token);
    }
}
