<?php
namespace App\Controller;

use App\Service\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    private JWTManager $jwtManager;

    public function __construct(JWTManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function index(RequestStack $requestStack): Response
    {
        // Récupérer le token depuis les cookies
        $token = $requestStack->getCurrentRequest()->cookies->get('token');
    
        //Si aucun token n'est trouvé, rediriger vers la page de login
        if (!$token) {
            return $this->redirectToRoute('index');
        }
    
        // Débogage du token avant décodage
        error_log('Token reçu: ' . $token);
    
        // Décoder le token et obtenir les informations de l'utilisateur
        $userData = $this->jwtManager->parseJWT($token);
    
        // Si le token est invalide ou expiré, rediriger vers la page de login
        if (!$userData) {
            return $this->redirectToRoute('index');
        }
    
        // Affichage des informations décodées
        $roles = $userData['roles'] ?? ['Rôle non défini'];
        $username = $userData['email'] ?? 'Nom d\'utilisateur non défini';
    
        // Renvoi du message de bienvenue avec les informations décodées
        return new Response(
            json_encode([
                'message' => 'Bienvenue sur le dashboard',
                'username' => $username,
                'roles' => $roles,
                $userData,
                $token
            ]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}    