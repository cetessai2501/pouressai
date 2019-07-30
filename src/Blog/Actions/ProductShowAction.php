<?php

namespace App\Blog\Actions;

use Psr\Container\ContainerInterface;
use Framework\Renderer\RenderInterface;
use Framework\Routery;
use App\Blog\Table\ProductTable;
use App\Blog\Table\PurchaseTable;
use Framework\Auth;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductShowAction 
{

     private $table;
     private $container;
     private $renderer;
     private $router;  
     private $stripeKey;
     private $auth; 
     private $purchaseTable;


      public function __construct(
        ContainerInterface $container,
        RenderInterface $renderer,
        Routery $router,
        ProductTable $table,
        PurchaseTable $purchaseTable,
        Auth $auth,
        string $stripeKey
    ) {
        $this->table = $table;
        $this->router = $router;
        $this->renderer = $renderer;
        $this->container = $container->get('shop');
        $this->stripeKey = $stripeKey;
        $this->purchaseTable = $purchaseTable;
        $this->auth = $auth;  
    }


      public function shopshow(Request $request)
    {
         //$params = $request->getQueryParams();
 $user = $this->auth->getUser();
         //$page = $params['p'] ?? 1;
         $product = $this->table->findBy('slug', $request->getAttribute('slag'));

         $stripeKey = $this->stripeKey;
         $download = false;
         $user = $this->auth->getUser();
         if ($user !== null && $this->purchaseTable->findFor($product, $user)) {
            $download = true;
         }
         
           
  return $this->container->render('bouticshow', ['product' => $product, 'router' => $this->router, 'stripeKey' => $stripeKey, 'download' => $download, 'user' => $user]);
        

    }







}
