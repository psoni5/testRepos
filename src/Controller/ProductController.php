<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Controller\NewInterface\ProductApiInterface;
use Namshi\JOSE\Signer\OpenSSL\PublicKey;

class ProductController extends AbstractController implements ProductApiInterface
{
    /**
     * @Route("/products", name="product",methods={"GET"})
     *
     */
    public function getAllProducts()
    {
        $em=$this->getDoctrine()->getManager();
       $products=$em->getRepository(Product::class)->findAll();
       if(empty($products))
           return new JsonResponse(['Message'=>'empty']);
       return $this->Json($products);
    }
    /**
     * @Route("/products/{id}",methods={"GET"})
     */
    public function getProduct($id,Request $request) {
          $em=$this->getDoctrine()->getManager();
         $product=$em->getRepository(Product::class)->find($id);
         if(empty($product))
             return new JsonResponse(['Message'=>'product not found']);
         return $this->Json($product);
    }
    /**
     * @Route("/products",methods={"POST"})
     */
    public function createProduct(Request $request) {
           $em=$this->getDoctrine()->getManager();
             $name=$request->request->get('name');
             if(empty($name))
                 return new JsonResponse(['Message'=>'name of product not empty'],Response::HTTP_NOT_FOUND);
             $price=$request->request->get('price');
             if(empty($price))
                 return new JsonResponse(['Message'=>'price of product not empty'],Response::HTTP_NOT_FOUND);
             $product=new Product();
             $product->setName($name);
             $product->setPrice($price);
             $em->persist($product);
             $em->flush();
             return $this->Json($product);
    }
    /**
     * @Route("/products/{id}",methods={"DELETE"})
     */
    public function removeProduct($id,Request $request) {
         $em=$this->getDoctrine()->getManager();
        $product=$em->getRepository(Product::class)->find($id);
        if(empty($product))
            return new JsonResponse(['Message'=>'product not found'],Response::HTTP_NOT_FOUND);
        $em->remove($product);
        $em->flush();
        return new JsonResponse(['Message'=>'product Deleted'],Response::HTTP_OK);
    }
    /**
     * @Route("/products/{id}",methods={"PUT"})
     * 
     */
    public function UpdateProduct($id,Request $request) {
        $em=$this->getDoctrine()->getManager();
        $product=$em->getRepository(Product::class)->find($id);
        if(empty($product))
         return new JsonResponse(['Message'=>'product not found'],Response::HTTP_NOT_FOUND);
        $name=$request->request->get('name');
        $price=$request->request->get('price');
        if(empty($name)||empty($price))
            return new JsonResponse(['Message'=>'please provide all field'],Response::HTTP_OK);
        if(!($name == $product->getName()))
              $product->setName($name);
        if(!($price == $product->getPrice()))
              $product->setPrice($price);
        $em->flush();
        return $this->Json($product);
    }
    /**
     *@Route("/products/{id}",methods={"PATCH"}) 
     */
    public function Update($id,Request $request)
    {
        $em=$this->getDoctrine()->getManager();
        $product=$em->getRepository(Product::class)->find($id);
        if(empty($product))
            return new JsonResponse(['Message'=>'product not found'],Response::HTTP_NOT_FOUND);
        $name=$request->request->get('name');
        $price=$request->request->get('price');
        if(!empty($name))
            $product->setName($name);
        if(!empty($price))
            $product->setPrice($price);
        $em->flush();
        return $this->Json($product);
        
    }
}
