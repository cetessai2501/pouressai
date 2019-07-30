<?php

use Psr\Container\ContainerInterface;
use function DI\factory;
use App\HomeController;
use Slim\Factory\AppFactory;
use function DI\create;
use function DI\get;
use Slim\Interfaces\RouteCollectorInterface;
use App\Blog\BlogModule;
use App\Session\FlashService;
use App\Session\SessionInterface;
use App\Session\Session;
use App\Middleware\CsrfMiddleware;

return [
    'blog.prefix' => '/news', 
    'admin.role'  => 'admin', 
    'admin.prefix' => '/admin', 
   
    
    'router' => function (ContainerInterface $c) {
return new \App\Routery($c);
    },
    'homeviewi' => function (ContainerInterface $c) {
    $engine = new \League\Plates\Engine('/home/sophie25/palipum/src/templates');
    


    return $engine;
    },


    //Response
    'response' => DI\autowire(\Slim\Psr7\Response::class),

    
];
