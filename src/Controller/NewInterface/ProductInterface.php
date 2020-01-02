<?php
namespace App\Controller\NewInterface;



interface ProductInterface
{
    public function setName(string $name);
    
    /**
     * @return String|null
     */
    public function getName();
    
    public function setPrice(String $price);
    
    /**
     * @return String|null
     */
    public function getPrice();
}
?>