<?php

namespace App\Blog\Actions;

use Psr\Container\ContainerInterface;
use Framework\Renderer\RenderInterface;
use Framework\Routery;
use App\Blog\Table\ProductTable;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductListingAction 
{

     private $table;
     private $container;
     private $renderer;
     private $router;  


      public function __construct(
        ContainerInterface $container,
        RenderInterface $renderer,
        Routery $router,
        ProductTable $table
    ) {
        $this->table = $table;
        $this->container = $container;  
        $this->router =  $container->get('\Framework\Routery');
        $this->renderer = $container->get('shop');
        
    }


      public function bout(Request $request)
    {
         $params = $request->getQueryParams();
         //var_dump($params);
         $page = $params['page'] ?? 1;
         $products = $this->table->findPublic();


           
         return $this->renderer->render('bouticindex', ['products' => $products, 'router' => $this->router]);
        

    }







}
