<?php
namespace App\Security;

//use Lexik\Bundle\JWTAuthenticationBundle\Security\JwtTokenAuthenticator as BaseAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    private $jwtEncoder;
    private $em;
    
    public function __construct(JWTEncoderInterface $jwtEncoder,EntityManagerInterface $em)
    {
          $this->jwtEncoder=$jwtEncoder;
          $this->em=$em;
    }
    public function supportsRememberMe()
    {
        return false;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {}

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $data=$this->jwtEncoder->decode($credentials);
        if($data === false)
            throw new CustomUserMessageAuthenticationException('invalid token');
            $username = $data['email'];
            
            return $this->em->getRepository(User::class)
                   ->findOneBy(['email'=>$username]);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // called when authentication info is missing from a
        // request that requires it
        return new JsonResponse([
            'error' => 'auth required'
        ], 401);
    }

    public function supports(Request $request)
    {}

    public function getCredentials(Request $request)
    {
        $extractor=new AuthorizationHeaderTokenExtractor('prifix', 'name');
        $token=$extractor->extract($request);
        if(!$token)
            return;
        return $token;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    
}
?>