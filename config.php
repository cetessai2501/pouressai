<?php

use Psr\Container\ContainerInterface;
use function DI\factory;
use App\HomeController;
use Slim\Factory\AppFactory;
use function DI\create;
use function DI\get;
use DebugBar\StandardDebugBar;
use Slim\Interfaces\RouteCollectorInterface;
use App\Blog\BlogModule;
use App\Session\FlashService;
use App\Session\SessionInterface;
use App\Session\Session;
use App\Middleware\CsrfMiddleware;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'blog.prefix' => '/news', 
    'admin.role'  => 'admin', 
    'admin.prefix' => '/admin', 
    'auth.service' => \DI\autowire(\App\Auth\AuthService::class),
    'blog.path' => '/home/sophie25/palipum/src/Blog/views',
'doctrine' => [
    'meta' => [
        'entity_path' => [
            'app/src/Entity'
        ],
        'auto_generate_proxies' => true,
        'proxy_dir' =>  __DIR__.'/../cache/proxies',
        'cache' => null,
    ],
    'connection' => [
        'driver' => 'pdo_sqlite',
        'path' => __DIR__ . '/pom.sqlite',
        
    ]
],
'em' => function (ContainerInterface $c) {
    $settings = $c->get('doctrine');
    $config = \Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
        $settings['meta']['entity_path'],
        $settings['meta']['auto_generate_proxies'],
        $settings['meta']['proxy_dir'],
        $settings['meta']['cache'],
        false
    );
    
    return \Doctrine\ORM\EntityManager::create($settings['connection'], $config);
},


    'router' => function (ContainerInterface $c) {
return new \App\Routery($c);
    },
  EntityManager::class => \DI\autowire(EntityManager::class),
  SessionInterface::class => \DI\autowire(Session::class),
  CsrfMiddleware::class => DI\autowire()->constructor(\DI\get(SessionInterface::class)),

'admin.middleware'             => DI\autowire(\App\Auth\Middleware\RoleMiddleware::class)
                                        ->constructor(
                                            \DI\get('auth.service'),
                                            \DI\get('admin.role')
                                        ), 
BlogModule::class => DI\autowire()->constructorParameter('prefix', \DI\get('blog.prefix'))->constructorParameter('path', \DI\get('blog.path')),
    'HomeController' => function (ContainerInterface $c) {
    $view = $c->get('view'); // retrieve the 'view' from the container
     $router = $c->get('router');
    
    return new HomeController($view, $router);
    },
    
    'homeview' => function (ContainerInterface $c) {
    $engine = new \League\Plates\Engine('/home/sophie25/palipum/src/templates');
    $engine->loadExtension(new \App\PlatesExt\Csrf(new \App\Middleware\CsrfMiddleware($c->get('session'))));


    return $engine;
    },
    
     'controller' => function (ContainerInterface $c) {
    return new \App\Controller($c, $c->get('homeview'));
    },
    // Session
    'session'                                    => DI\autowire(\App\Session\Session::class),
    'session.flash'                              => DI\autowire(FlashService::class)
        ->constructor(\DI\get('session')),
    \App\Session\SessionInterface::class   => \DI\get('session'),
    \App\Session\FlashService::class                  => \DI\get('session.flash'), 

    //Response
    'response' => DI\autowire(\Slim\Psr7\Response::class),
\PDO::class => function (\Psr\Container\ContainerInterface $c) {
        $DB = new \PDO('sqlite:/home/sophie25/palipum/pom.sqlite'); 
        $DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
        return $DB;  
    },


'debugbar' => function (\Psr\Container\ContainerInterface $container){
    $debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer();





//$myLogger = new \RedBeanPHP\Logger\RDefault;







return $debugbar;  
    },


    
];
