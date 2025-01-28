<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class SecurityController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/security', name: 'api_security', methods: ['GET'])]
    public function securityCheck(Request $request): JsonResponse
    {
        // Vérification du token JWT dans les en-têtes
        $token = $request->headers->get('Authorization');
        if (!$token) {
            return new JsonResponse(['error' => 'Token manquant'], 400);
        }

        // Suppression de "Bearer " du token (si nécessaire)
        $token = str_replace('Bearer ', '', $token);

        try {
            // Vérification du token avec LexikJWT
            $data = $this->jwtManager->parse($token);
            if (!$data) {
                return new JsonResponse(['error' => 'Token invalide'], 401);
            }

            return new JsonResponse(['message' => 'Security check passed', 'user' => $data]);

        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur lors de la vérification du token'], 500);
        }
    }
}

/*
namespace App\Controller;

use App\Entity\User;
use App\Service\JWTManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private JWTManager $jwtManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(
        JWTManager $jwtManager,
        UserPasswordHasherInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    ) {
        $this->jwtManager = $jwtManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Parse JSON request body
        $data = json_decode($request->getContent(), true);

        // Validate input data
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email ou password manquant'], 400);
        }

        // Fetch user by email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Check user existence and validate password
        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Email ou password invalide'], 401);
        }

        // Generate JWT
        $jwt = $this->jwtManager->createJWT($user);
    
        // Return JSON response with token and redirection URL
        return new JsonResponse([
            'success' => true,
            'message' => 'Connection réussie',
            'token' => $jwt]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Inform client to clear token (actual invalidation requires additional mechanisms)
        return new JsonResponse(['message' => 'Déconnexion réussie']);
    }
}
*/
