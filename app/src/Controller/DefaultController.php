<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    // Route pour la page d'accueil
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/index.html';

        if (!file_exists($filePath) || !is_file($filePath)) {
            throw $this->createNotFoundException('Le fichier index.html n\'existe pas.');
        }

        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    // Route pour servir les fichiers statiques via l'API
    #[Route('/api/static/{filename}', name: 'api_static_file', requirements: ['filename' => '.+'], methods: ['GET'])]
    public function serveStaticFile(string $filename): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $filename;

        // Liste étendue des extensions autorisées
        $allowedExtensions = [
            'html', 'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico', 
            'json', 'xml', 'txt', 'woff', 'woff2', 'ttf', 'eot', 'otf'
        ];
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Vérification de l'extension
        if (!in_array($extension, $allowedExtensions)) {
            return new Response('Type de fichier non autorisé.', 403, [
                'Content-Type' => 'text/plain',
            ]);
        }

        // Vérification si le fichier existe
        if (!file_exists($filePath) || !is_file($filePath)) {
            return new Response('Fichier introuvable.', 404, [
                'Content-Type' => 'text/plain',
            ]);
        }

        // Détection du type MIME
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        // Réponse avec le contenu du fichier
        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}
