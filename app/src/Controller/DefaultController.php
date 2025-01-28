<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    // Route spécifique pour la page d'accueil
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        // Chemin vers le fichier index.html
        $filePath = $this->getParameter('kernel.project_dir') . '/public/index.html';

        // Vérifiez si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier index.html n\'existe pas.');
        }

        // Retourne le contenu du fichier HTML
        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    // Route pour servir les fichiers statiques (CSS, JS, etc.)
    #[Route('/{filename}', name: 'static_file', requirements: ['filename' => '.+'], methods: ['GET'])]
    public function serveStaticFile(string $filename): Response
    {
        // Chemin vers le fichier dans le dossier public/
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $filename;

        // Vérifiez si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException(sprintf('Le fichier %s n\'existe pas.', $filename));
        }

        // Devinez le type MIME du fichier
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        // Retourne le contenu du fichier avec le bon type MIME
        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}