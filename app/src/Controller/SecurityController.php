<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\JWTManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private JWTManager $jwtManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private EntityManagerInterface $entityManager;

    public function __construct(JWTManager $jwtManager, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {
        $this->jwtManager = $jwtManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        // Récupérer les données JSON envoyées par l'utilisateur
        $data = json_decode($request->getContent(), true);

        // Récupérer l'email et le mot de passe
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Vérifier si l'email et le mot de passe sont présents
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email ou password manquant'], 400);
        }

        // Chercher l'utilisateur dans la base de données
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if (!$user || !$this->passwordEncoder->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Email ou password invalide'], 401);
        }

        // Générer un JWT pour l'utilisateur
        $jwt = $this->jwtManager->createJWT($user);

        // Déterminer le rôle de l'utilisateur et rediriger en conséquence
        $role = $user->getHasRole()->getName();
        switch ($role) {
            case 'Étudiant':
                $route = 'student_dashboard';
                break;
            case 'Superviseur':
                $route = 'supervisor_dashboard';
                break;
            case 'Administrateur':
                $route = 'admin_dashboard';
                break;
            default:
                $route = 'default_dashboard';
                break;
        }

        // Retourner le JWT avec une réponse JSON et redirection
        return new JsonResponse([
            'success' => true,
            'message' => 'Connection réussie',
            'token' => $jwt,
            'redirect' => $this->generateUrl($route),
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        // Simule une déconnexion en supprimant le token du client côté frontend
        // Le token ne peut pas être invalidé côté serveur sans un mécanisme supplémentaire
        return new JsonResponse(['message' => 'Déconnection réussie']);
    }
}