<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class GoogleController extends AbstractController
{
    /**
     * @Route("/connect/google", name="connect_google")
     * 
     */
    public function connect(ClientRegistry $clg)
    {
        return $clg
             ->getClient('google')
             ->redirect(['profile','email']);
    }
    /**
     * @Route("/connect/google/check",name="connect_google_check")
     */
    public function connectCheckAction(Request $request) {
        if($this->getUser())
        {
            return $this->redirectToRoute('home');
        }
        else 
        {
            return new JsonResponse(['status'=>false,'message'=>'User not found']);
        }
    }
}
