<?php
namespace App;
use Psr\Container\ContainerInterface;
use \Firebase\JWT\JWT;

class HomeController
{

   protected $view;

   protected $session;  

   protected $router;

    public function __construct(ContainerInterface $container) {
        $this->view = $container->get('homeview');
        $this->auth = $container->get('auth.service');
        $this->router = $container->get('router'); 
        $this->router->setContainer($container);
    }
    
    public function home($request, $response, $args) {
      // your code here
      //return $this->view->render($response, 'hello.php');
      // use $this->view to render the HTML
       $router = $this->router;

       $response = $this->view->render($response, 'home', ['title' => 'Jonathan']);
      return $response;
    }









}
