<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use App\Entity\User;
use League\OAuth2\Client\Provider\Google;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class GoogleAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $em;
    private $router;
    public function __construct(ClientRegistry $clientRegistry,EntityManagerInterface $em,RouterInterface $router)
    {
        $this->clientRegistry=$clientRegistry;
        $this->em=$em;
        $this->router=$router;
    }
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGooleClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $googleUser = $this->getGooleClient()
        ->fetchUserFromToken($credentials);
        
        $email=$googleUser->getEmail();
        

            $user=$this->em->getRepository(User::class)
               ->findOneBy(['email'=>$email]);
            if(!$user)
            {
                $user=new User();
                $user->setEmail($email);
                $user->setPassword('000000');
                $this->em->persist($user);
                $this->em->flush();
            
        }
        return $user;
    }

    public function getGooleClient()
    {
        return $this->clientRegistry->getClient('google');
    }
//     public function checkCredentials($credentials, UserInterface $user)
//     {
//         return true;
//     }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message=strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message,Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetUrl = $this->router->generate("home");
        return new RedirectResponse($targetUrl);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/login');
    }

//     public function supportsRememberMe()
//     {
//         // todo
//     }
}
