<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class AuthController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, JWTTokenManagerInterface $jwtManager)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Décodage des données envoyées dans la requête
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Validation des données
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email ou mot de passe manquant'], 400);
        }

        // Récupération de l'utilisateur
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Vérification du mot de passe
        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Email ou mot de passe invalide'], 401);
        }

       // Création du token JWT
    $jwt = $this->jwtManager->create($user);

    // Définition du cookie sécurisé avec le token JWT
    $response = new JsonResponse([
        'success' => true,
        'message' => 'Connexion réussie',
        'user' => [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'roles' => $user->getRoles(),
        ],
    ]);
        // Ajout du cookie JWT avec SameSite=None et Secure
        $response->headers->setCookie(
            new Cookie(
                'token',
                $jwt,
                time() + 3600, // Expiration dans 1 heure
                '/',
                null,
                true,  // Secure (nécessite HTTPS)
                true,  // HttpOnly (non accessible via JavaScript)
                false,
                'None' // SameSite=None pour permettre l'accès en cross-site
            )
        );

        return $response;
    }

    #[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Définition d'un cookie expiré pour supprimer le token du navigateur
        $response = new JsonResponse(['message' => 'Déconnexion réussie']);
        $response->headers->setCookie(
            new Cookie('token', '', time() - 3600, '/', null, true, true, false, 'None')
        );

        return $response;
    }
}
