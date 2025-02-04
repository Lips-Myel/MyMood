<?php
namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Entity\User;

class JWTManager
{
    private JWTTokenManagerInterface $jwtManager;
    private JWTEncoderInterface $jwtEncoder;

    public function __construct(JWTTokenManagerInterface $jwtManager, JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtManager = $jwtManager;
        $this->jwtEncoder = $jwtEncoder;
    }

    /**
     * Crée un JWT pour un utilisateur.
     *
     * @param User $user
     * @return string
     */
    public function createJWT(User $user): string
    {
        return $this->jwtManager->create($user);
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
            $decoded = $this->jwtEncoder->decode($token);
            return $decoded ?: null;
        } catch (\Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException $e) {
            error_log('Erreur lors du décodage du token : ' . $e->getMessage());
        }
        return null;
    }
}


    // /**
    //  * Décoder et vérifier un JWT.
    //  *
    //  * @param string $token
    //  * @return array|null
    //  */
    // public function parseJWT(string $token): ?array
    // {
    //     try {
    //         // Décoder le token
    //         $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
    //         $decodedArray = (array) $decoded;
    
    //         // Vérifiez si le champ 'username' existe dans le payload
    //         if (isset($decodedArray['email'])) {
    //             return $decodedArray;
    //         } else {
    //             // Si le champ 'username' n'est pas présent, loggez l'erreur
    //             error_log("Le champ 'username' est manquant dans le token");
    //         }
    //     } catch (\Firebase\JWT\ExpiredException $e) {
    //         // Cas d'un token expiré
    //         error_log('Token expiré : ' . $e->getMessage());
    //     } catch (\Firebase\JWT\SignatureInvalidException $e) {
    //         // Cas d'une signature invalide
    //         error_log('Signature invalide : ' . $e->getMessage());
    //     } catch (\Exception $e) {
    //         // Autres erreurs de décodage
    //         error_log('Erreur lors du décodage du token : ' . $e->getMessage());
    //     }
    
    //     return null;
    // }
    

    // /**
    //  * Vérifie un token JWT et retourne les informations de l'utilisateur, y compris le rôle.
    //  *
    //  * @param string $token
    //  * @return array|null
    //  */
    // public function verifyToken(string $token): ?array
    // {
    //     // Décoder le token pour vérifier sa validité
    //     $decoded = $this->parseJWT($token);

    //     // Si le token est valide, retourner les données du token incluant le rôle
    //     if ($decoded && isset($decoded['roles'])) {
    //         return [
    //             'role' => $decoded['roles'][0], // Premier rôle de l'utilisateur
    //             'user_id' => $decoded['user_id'],
    //             'email' => $decoded['email'],
    //         ];
    //     }

    //     // Si le token est invalide ou ne contient pas de rôle, retourner null
    //     return null;
    // }
// }
