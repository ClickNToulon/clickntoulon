<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    #[Route(path: "/oauth/google/connexion", name: "google_connect")]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('google')->redirect(['profile', 'email', 'openid']);
    }

    #[Route(path: "/oauth/google/check", name: "google_connect_check")]
    public function connectCheckAction()
    {
        // Méthode qui doit être nulle pour effectuer l'auth dans GoogleAuthenticator
    }
}