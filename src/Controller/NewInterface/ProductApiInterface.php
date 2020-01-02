<?php
namespace App\Controller\NewInterface;

use Symfony\Component\HttpFoundation\Request;

interface ProductApiInterface
{
    /**
     * 
     */
    public function getAllProducts();
    
    /**
     * 
     */
    public function getProduct($id,Request $request); 
    
    /**
     * 
     */
    public function createProduct(Request $request);
    
    /**
     * 
     */
    public function removeProduct($id,Request $request);
    
    /**
     * 
     */
    public function updateProduct($id,Request $request);
}
?>