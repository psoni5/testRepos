<?php
namespace App\Controller\Rest;


use FOS\RestBundle\Controller\FOSRestController;
use GuzzleHttp\Psr7\Request;
use Doctrine\DBAL\Schema\View;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use  Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations ;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Config\Definition\Exception\Exception;

class ApiController extends AbstractFOSRestController
{
    /**
     *@Route("/login_check", methods={"POST"})
     */
    public function login(\Symfony\Component\HttpFoundation\Request $request,EntityManagerInterface $em)
    {
       
       try {
           $email=$request->request->get('email');
           $password=$request->request->get('password');
           $user=$em->getRepository(User::class)->findOneBy(['email'=>$email,'password'=>$password]);
           
       } catch (Exception $e) {
           return new Response($e->getMessage()."ero");
           
       }
       if($user)
       {
           try{
           $token= $this->get('lexik_jwt_authentication.encoder')
           ->encode([
               'username' => $user->getEmail(),
               'exp' => time() + 3600 // 1 hour expiration
           ]);
           return new JsonResponse(['token'=>$token]);
           }
           catch (Exception $e)
           {
               return new Response($e->getMessage()."erov");
           }
           //return \FOS\RestBundle\View\View::create($user,Response::HTTP_OK);
       }
       return \FOS\RestBundle\View\View::create("wrong credantial",Response::HTTP_OK);
     
    }
    /**
     * @Rest\Post("/user")
     * @return \FOS\RestBundle\View\View
     *
     */
    public function register(\Symfony\Component\HttpFoundation\Request $request,EntityManagerInterface $em)  {
        $user=new User();
        $user->setEmail($request->request->get('email'));
        $user->setPassword($request->request->get('password'));
        $user->setMobile($request->request->get('mobile'));
         $em->persist($user);
         $em->flush();
        return \FOS\RestBundle\View\View::create($user,Response::HTTP_CREATED);
    }
    /**
     * @Rest\Get("/user/{id}")
     * 
     */
    public function getData($id,EntityManagerInterface $em)
    {
        $user=$em->getRepository(User::class)->find($id);
       
        return \FOS\RestBundle\View\View::create($user,Response::HTTP_OK);
    }
    /**
     * @Rest\Get("/user")
     * 
     */
    public function getAll(EntityManagerInterface $em)
    {
        $users=$em->getRepository(User::class)->findAll();
        
        return \FOS\RestBundle\View\View::create($users,Response::HTTP_OK);
    }
    /**
     * @Rest\Put("/user/{id}")
     * 
     */
    public function updateUser($id,EntityManagerInterface $em,\Symfony\Component\HttpFoundation\Request $request)
    {
        $user=$em->getRepository(User::class)->find($id);
        $email=$request->request->get('email');
        $password=$request->request->get('password');
        $mobile=$request->request->get('mobile');
        
        if($email)
        {
            $user->setEmail($email);
        }
        if($mobile)
        {
            $user->setMobile($mobile);
        }
        if($password)
        {
            $user->setPassword($password);
        }
        $em->flush();
        return \FOS\RestBundle\View\View::create($user,Response::HTTP_OK);
        
    }
    /**
     * @Rest\Delete("/user/{id}")
     * 
     */
    public function deleteData($id,EntityManagerInterface $em)
    {
        $user=$em->getRepository(User::class)->find($id);
        $em->remove($user);
        return \FOS\RestBundle\View\View::create($user,Response::HTTP_OK);
    }
}
?>