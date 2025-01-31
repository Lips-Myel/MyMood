<?php
namespace App\Security;

use App\Service\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private JWTTokenManagerInterface $jwtManager;

    // Injection du service JWTService
    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        // Récupère les rôles de l'utilisateur
        $roles = $token->getRoleNames();

        // Récupère l'utilisateur à partir du token
        $user = $token->getUser();

        // Vérifie si l'utilisateur implémente UserInterface
        if (!$user instanceof UserInterface) {
            return new JsonResponse(['error' => 'Utilisateur non valide'], 400);
        }

        // Crée un JWT avec les informations de l'utilisateur
        $jwt = $this->jwtManager->create($user);

        // Retourne une réponse JSON avec les rôles et les informations de l'utilisateur
        $response = new JsonResponse([
            'success' => true,
            'roles' => $roles,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'first_name' => $user->getFirstName(),
                'last_name' => $user->getLastName(),
                'roles' => $user->getHasRole(), // Envoie les rôles de l'utilisateur
            ],
        ]);

        // Ajouter un cookie avec le JWT
        $response->headers->setCookie(new Cookie('token', $jwt, time() + 3600, '/', null, false, true));

        return $response;
    }
}
