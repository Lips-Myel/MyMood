<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    // Route pour la page d'accueil
    #[Route('/', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/index.html';

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier index.html n\'existe pas.');
        }

        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => 'text/html',
        ]);
    }

    // Route pour servir les fichiers statiques
    #[Route('/{filename}', name: 'static_file', requirements: ['filename' => '.+'], methods: ['GET'])]
    public function serveStaticFile(string $filename): Response
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/' . $filename;

        $allowedExtensions = ['html', 'css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico'];
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        if (!in_array($extension, $allowedExtensions)) {
            return new Response(file_get_contents($this->getParameter('kernel.project_dir') . '/public/404.html'), 404, [
                'Content-Type' => 'text/html',
            ]);
        }

        if (!file_exists($filePath)) {
            return new Response(file_get_contents($this->getParameter('kernel.project_dir') . '/public/404.html'), 404, [
                'Content-Type' => 'text/html',
            ]);
        }

        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return new Response(file_get_contents($filePath), 200, [
            'Content-Type' => $mimeType,
        ]);
    }
}
