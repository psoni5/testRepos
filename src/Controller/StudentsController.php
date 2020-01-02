<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Student;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class StudentsController extends AbstractController
{
    /**
     * @Route("/students", name="students")
     */
    public function ajaxAction(Request $request)
    {
        $students= $this->getDoctrine()->getManager()->getRepository(Student::class)->findAll();
        if(($request->isXmlHttpRequest()))
        {
            $jsonData=array();
            $id=0;
            foreach ($students as $student)
            {
                $temp=array(
                    'name'=>$student->getName(),
                    'email'=>$student->getEmail(),
                    'mobile'=>$student->getMobile(),
                );
                $jsonData[$id++]=$temp;
            }
            return new JsonResponse($jsonData);
            }
        return $this->render('students/students.html.twig');
    }
}
