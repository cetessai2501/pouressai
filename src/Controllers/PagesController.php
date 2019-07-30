<?php

namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Projek\Slim\Plates;
use \Projek\Slim\PlatesExtension;
use App\Controller;

class PagesController {

      
      public function home(RequestInterface $request, ResponseInterface $response, Plates $plates){
          #$response->getBody()->write('salu les gens');
          return $plates->render('homepag', array('response' => $response));


      } 
      public function getContact(RequestInterface $request, ResponseInterface $response, Plates $plates){
          #$response->getBody()->write('salu les gens');
          //$this->container->view->render('contact', array('response' => $response));
          return $plates->render('contact', array('response' => $response));  

      }
      
   
      public function postContact(RequestInterface $request, ResponseInterface $response){
           //$params = $request->getParams(); 
          var_dump($request->getParams());
          die();
          #$response->getBody()->write('salu les gens');
          //$this->container->view->render('contact', array('response' => $response));
          return $response->getBody()->write($params);  

      }         
}
