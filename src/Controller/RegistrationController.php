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
use Gedmo\Sluggable\Util\Urlizer;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Aws\S3\Exception\S3Exception;
class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $uploadedFile = $form['image']->getData();
            if($uploadedFile)
            {
                //$destination = $this->getParameter('kernel.project_dir').'/public/uploads';
                //$destination = $this->getParameter('uploads_base_url');
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
             
//                 $uploadedFile->move(
//                     $destination,
//                     $newFilename
//                     );
                   //return new Response($originalFilename);
                try {
                    //Create a S3Client
                    $s3Client = new S3Client([
                        'region' => 'ap-south-1',
                        'version' => 'latest',
                        'credentials' => [
                            'key'    => 'AKIA3T4VXVCDSTDMVR5G',
                            'secret' => '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'
                        ]
                    ]);
                    $result = $s3Client->putObject([
                        'Bucket' => 'priyansh11111',
                        'Key' => $newFilename,
                        'SourceFile' =>$uploadedFile,
                         'ACL' => 'public-read'
                    ]);
                } catch (S3Exception $e) {
                    echo $e->getMessage() . "\n";
                }
                $user->setImage($result['ObjectURL']);
                
            }
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
