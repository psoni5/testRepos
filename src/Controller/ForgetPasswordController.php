<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Form\ForgotPasswordType;
use Symfony\Component\Config\Definition\Exception\Exception;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ForgetPasswordController extends AbstractController
{
    /**
     * @Route("/forget_password/{email}",defaults={"email" = 0}, name="forget_password")
     * 
     */
    //private $awsmail;
//     public function __construct($mail)
//     {
//         $this->awsmail=$mail;
//     }
    public function forgotPassword(\Swift_Mailer $mailer,MailerInterface $awsMailer,Request $request,UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted())
        {
            $email=$form->get('email')->getData();
            $em = $this->getDoctrine()->getManager();
            
                
                $user =$em->getRepository(User::class)->findOneBy(['email'=>$email]);
               if($user)
               {
                $password='1234567';
                $encodePassword = $passwordEncoder->encodePassword($user,$password);
                $user->setPassword($encodePassword);
                $em->flush();
                try
                {
                $message=(new \Swift_Message("Hi Email"))
                ->setFrom('priyanshsoni95@gmail.com')
                ->setTo('jayvant.padsala@techjini.com')
                      ->setBody(
                          $this->renderView(
                              'emails/email.html.twig',
                              ['name' => 'jayant'],['new_password'=>$password]),
                          'text/html'
                          )
                ;
                 $mailer->send($message);
                 return new Response("successfully update password please check your email");
                }
                catch (Exception $e)
                {
                    return new Response($e->getMessage());
                } 
                
              }
//           if($user)
//           {
//               try {
//                   $message=(new Email())
//                   ->from('priyanshsoni95@gmail.com')
//                   ->to('jayvant.padsala@techjini.com')
//                   ->subject('aws mail')
//                   ->text('aws mail')
//                   ->html('<p>See Twig integration for better HTML integration!</p>');
              
//               $Mailer->send($message);
                  
//               } catch (Exception $e) {
//                   return new Response($e->getMessage());
//               }
//           }
            
            else
            {
                return $this->render('forget_password/forget.html.twig', [
                    'forgetpassword' => $form->createView(),
                    'error'=>'email not found',
                ]);
            }
        }
        return $this->render('forget_password/forget.html.twig', [
            'forgetpassword' => $form->createView(),
            'error'=>' ',
        ]);
    }
    
  
}
