<?php
date_default_timezone_set('Europe/Paris');





require '../vendor/autoload.php';

use DI\Container;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Psr\Container\ContainerInterface;
use App\Middleware\TrailingSlashMiddleware;
use App\MyApp;
use GuzzleHttp\Psr7\ServerRequest;
use Zend\Diactoros\Response;
use App\Blog\BlogModule;
use App\HomeController;
use Slim\Handlers\ErrorHandler;
use function DI\object;
use function DI\get;
use Slim\Middleware\ErrorMiddleware;
use App\Middleware\CsrfMiddleware;
use Slim\CallableResolver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use \Firebase\JWT\JWT;

$appi = new MyApp(AppFactory::determineResponseFactory(), null, null, null, null, dirname(__DIR__) .'/config.php', [ \App\Auth\AuthModule::class,  \App\Registration\RegistrationModule::class ,BlogModule::class ]);

// Set container to create App with on AppFactory
$cont = $appi->getContainer();
//$ress = new CallableResolver($cont);
$reader = new \Doctrine\Common\Annotations\AnnotationReader();
$router = $cont->get('router');
$view = $cont->get('homeview');





$appi->add(new TrailingSlashMiddleware())->add(new \App\Middleware\CsrfMiddleware($cont->get('session')));
//->add(new \App\Auth\Middleware\DebugMiddle($cont, $cont->get('debugbar'), $cont->get('debugbar')->getJavascriptRenderer()->setBaseUrl('/Resources'), $cont->get('App\MyApp')->getFactory()  ));


//$callableResolver = $appi->getCallableResolver();
//$responseFactory = $appi->getFactory();
//$errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, true, true);
//$appi->add($errorMiddleware);


$p = $cont->get('controller', function() use ($view){

     return $view->getInstance();
});
$log = $cont->get('controller')->getContainer();

$resp = $cont->get('response');


//$routingMiddleware = new RoutingMiddleware($routeResolver);
//$app->add($routingMiddleware);
//AppFactory::setContainer($container);

$router->get('/', HomeController::class . ':home')->setName('home');
$router->get('/books', HomeController::class . ':home')->setName('books');







if(isset($_GET['page']) && $_GET['page'] == 1){

$uri = explode('?', $_SERVER['REQUEST_URI'])[0];
$get = $_GET;
unset($get['page']);
$query = http_build_query($get);
if(!empty($query)){
$uri = $uri . '?' . $query;

}
header('Location: ' .$uri); 
http_response_code(301);
exit();
} 

//var_dump($router->getNamedRoutey('home'));



$appi->run();



