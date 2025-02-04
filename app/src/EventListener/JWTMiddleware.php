<?php

namespace App\EventListener;

use App\Service\JWTManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: 'kernel.request', priority: 30)]
class JWTMiddleware
{
    private RequestStack $requestStack;
    private JWTManager $jwtManager;
    private RouterInterface $router;
    private LoggerInterface $logger;

    public function __construct(RequestStack $requestStack, JWTManager $jwtManager, RouterInterface $router, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->jwtManager = $jwtManager;
        $this->router = $router;
        $this->logger = $logger;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        // Ignorer certaines routes comme l'accueil
        if ($path === $this->router->generate('index')) {
            return;
        }

        $this->logger->info("Chemin actuel : $path");
        $this->logger->info('Vérification de la requête : ' . $request->getPathInfo());


        $token = $request->cookies->get('token');

        // Vérifier la présence du token
        if (!$token) {
            $this->logger->warning("Accès refusé : token manquant", ['path' => $path]);
            $event->setResponse($this->redirectToIndex());
            return;
        }
    
        try {
            $decodedToken = $this->jwtManager->parseJWT($token);
            if (!$decodedToken) {
                throw new \Exception("JWT invalide");
            }
    
            $roles = $decodedToken['roles'] ?? [];
    
            // Vérification des accès
            if ($this->isAccessDeniedForRole($path, $roles, 'Administrateur', '/admin')) {
                $event->setResponse($this->redirectToIndex());
                return;
            }
            if ($this->isAccessDeniedForRole($path, $roles, 'Superviseur', '/supervisor')) {
                $event->setResponse($this->redirectToIndex());
                return;
            }
            if ($this->isAccessDeniedForRole($path, $roles, 'Étudiant', '/student')) {
                $event->setResponse($this->redirectToIndex());
                return;
            }
    
        } catch (\Exception $e) {
            $this->logger->error("Erreur JWT : " . $e->getMessage(), ['path' => $path]);
            $event->setResponse($this->redirectToIndex(true));
        }
    }

    private function isAccessDeniedForRole(string $path, array $roles, string $requiredRole, string $routePrefix): bool
    {
        return (str_starts_with($path, $routePrefix) || preg_match('#^' . $routePrefix . '/.*\.html$#', $path))
            && !in_array($requiredRole, $roles);
    }

    private function redirectToIndex(bool $invalidateSession = false): Response
    {
        // Si le paramètre est vrai, invalide la session
        if ($invalidateSession) {
            $session = $this->requestStack->getSession();
            if ($session) {
                $session->invalidate();
            }
        }
    
        // Obtient la requête courante via RequestStack
        $request = $this->requestStack->getCurrentRequest();
    
        // Vérifie si l'utilisateur est déjà sur la page d'accueil
        if ($request && ($request->getPathInfo() === '/')) {
            // Si déjà sur la page d'accueil, ne redirigez pas
            return new Response('', Response::HTTP_OK); // Retourne juste une réponse vide avec code 200
        }
    
        // Retourne la réponse de redirection vers la route d'accueil
        return new RedirectResponse($this->router->generate('index')); // Redirection vers la route 'index'
    }
    
}
