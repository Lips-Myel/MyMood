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

        // Si aucun token n'est trouvé, rediriger vers la page de login
        if (!$token) {
            return $this->redirectToRoute('index');
        }

        /* 
        // Vérifier le token et obtenir le rôle
        $userRole = $this->jwtManager->verifyToken($token);

        // Affichage du rôle pour déboguer
        dump('Rôle de l\'utilisateur:', $userRole);

        // Si le token est invalide ou expiré, rediriger vers la page de login
        if (!$userRole) {
            dump('Token invalide ou expiré, redirection vers la page de login');
            return $this->redirectToRoute('index');
        }
        */

        // Renvoi du message de bienvenue et du rôle
        return new Response(
            json_encode([
                'message' => 'Bienvenue sur le dashboard',
                'role' => 'Rôle de l\'utilisateur',
            ]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
