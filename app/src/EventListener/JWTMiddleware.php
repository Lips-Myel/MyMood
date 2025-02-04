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

#[AsEventListener(event: 'kernel.request', priority: 100)]
class JWTMiddleware
{
    private RequestStack $requestStack;
    private JWTManager $jwtManager;
    private RouterInterface $router;
    private LoggerInterface $logger;

    private array $rolePaths = [
        'Administrateur' => '/admin',
        'Superviseur' => '/supervisor',
        'Étudiant' => '/student',
    ];

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

        // 📝 Log de la requête
        $this->logger->info("Requête reçue : $path");

        // 📌 Ignorer les fichiers statiques
        if ($this->isStaticFile($path)) {
            return;
        }

        // 🚫 Vérifier si la route est protégée
        if (!$this->isProtectedPath($path)) {
            return;
        }

        // 🔍 Récupération du token
        $token = $request->cookies->get('token');
        if (!$token) {
            $this->logger->warning("Accès refusé : Token manquant", ['path' => $path]);
            $event->setResponse($this->redirectToIndex());
            return;
        }

        try {
            // 🔓 Validation et décodage du token JWT
            $decodedToken = $this->jwtManager->parseJWT($token);
            if (!$decodedToken) {
                throw new \Exception("Token invalide");
            }

            // 🎭 Vérification des rôles
            $userRoles = $decodedToken['roles'] ?? [];
            $this->logger->info("Utilisateur authentifié avec les rôles : " . json_encode($userRoles));

            // 🔐 Vérifie si l'utilisateur a le droit d'accéder à cette page
            if (!$this->protectPath($path, $userRoles)) {
                $this->logger->warning("⛔ Accès refusé : rôle non autorisé", ['path' => $path, 'roles' => $userRoles]);
                $event->setResponse($this->redirectToIndex());
                return;
            }


        } catch (\Exception $e) {
            $this->logger->error("Erreur JWT : " . $e->getMessage(), ['path' => $path]);
            $event->setResponse($this->redirectToIndex(true)); // Invalide la session en cas d'erreur JWT
        }
    }

    /**
     * Récupère les rôles de l'utilisateur à partir de la requête.
     */
    public function getUserRolesFromRequest(Request $request): array
    {
        $token = $request->cookies->get('token');
        
        if (!$token) {
            return [];
        }

        try {
            $decodedToken = $this->jwtManager->parseJWT($token);
            return $decodedToken['roles'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Vérifie si la requête est pour un fichier statique (CSS, JS, images…)
     */
    private function isStaticFile(string $path): bool
    {
        return (bool) preg_match('#\.(ico|css|js|png|jpg|jpeg|gif|svg|woff|woff2|ttf|eot|otf|json|xml|txt)$#', $path);
    }

    /**
     * Vérifie si l'URL demande une ressource protégée.
     */
    private function isProtectedPath(string $path): bool
    {
        return (bool) preg_match('#/(admin|supervisor|student|api/student)/.*\.html$#', $path);
    }

    /**
     * Vérifie si l'utilisateur a un rôle autorisé.
     */
    private function isAuthorized(array $userRoles): bool
    {
        foreach ($userRoles as $role) {
            if (isset($this->rolePaths[$role])) {
                $this->logger->info("Utilisateur autorisé avec le rôle : $role");
                return true;
            }
        }

        return false;
    }

    /**
     * Vérifie si l'utilisateur a accès à un chemin spécifique en fonction de son rôle.
     */
    private function protectPath(string $path, array $userRoles): bool
    {
        $this->logger->info("🔍 Vérification d'accès", ['path' => $path, 'roles' => $userRoles]);

        if (empty($userRoles)) {
            $this->logger->warning("⛔ Accès refusé : Aucun rôle détecté pour l'utilisateur.");
            return false;
        }

        foreach ($userRoles as $role) {
            if (!isset($this->rolePaths[$role])) {
                continue;
            }

            $allowedPrefix = $this->rolePaths[$role];

            // ✅ Ajustement pour inclure `/api/` en plus du préfixe attendu
            if (str_starts_with($path, $allowedPrefix) || str_starts_with($path, "/api" . $allowedPrefix)) {
                return true;
            }
        }

        $this->logger->warning("⛔ Accès refusé : Aucun rôle autorisé pour accéder à '$path'.", [
            'userRoles' => $userRoles
        ]);

        return false;
    }

    


    /**
     * Redirige vers l'index avec une option d'invalidation de session.
     */
    private function redirectToIndex(bool $invalidateSession = false): Response
    {
        if ($invalidateSession) {
            $session = $this->requestStack->getSession();
            if ($session) {
                $session->invalidate();
            }
        }

        $request = $this->requestStack->getCurrentRequest();
        if ($request && ($request->getPathInfo() === '/')) {
            return new Response('', Response::HTTP_OK);
        }

        return new RedirectResponse($this->router->generate('index'));
    }
}
