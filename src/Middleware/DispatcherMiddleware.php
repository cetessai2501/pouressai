<?php
namespace Framework\Middleware;

use Framework\Routery;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Framework\Middleware\CombinedMiddleware;
use League\Route\Router;
use Framework\Router\Routy;
use DebugBar\JavascriptRenderer as DebugBarRenderer;
use DebugBar\StandardDebugBar;

class DispatcherMiddleware implements MiddlewareInterface
{
    private $router;

    private $debugBarRenderer;

    public $debugbar;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, Routery $router)
    {
        $this->container = $container;
        $this->router = $this->container->get('Framework\Routery'); 
        $this->debugbar = $this->container->get('debugbar');
        $this->debugBarRenderer = $this->debugbar->getJavascriptRenderer();

    }

    public function dispatchRequest(ServerRequestInterface $request) 
    {
        $route = $request->getAttributes(\Framework\Router\Routy::class)['Framework\Router\Routy'];
        return $route;
        
    }



    
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $delegate
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        //$match = $this->dispatch($request);



       //list($c, $method) = $c;
       //if (is_object($c) && method_exists($c, '__invoke')) {
            //$reflexion =  new \ReflectionMethod($c, '__invoke');
            //return $reflexion->invoke($c, $request);
        //}
       //$collection = $this->getClassMethodsWithoutMagicMethods($controller);  
       //$methods = get_class_methods($controller); 
       //$this->invoker->call($c, [$request]);
       //$controller->__invoke($request);
       //$copie = clone $controller; 
       //$this->createController($controller);
       //$reflectionMethod = new \ReflectionMethod($controller,'checkPermissions');
       //var_dump($reflectionMethod->invokeArgs($controller,[$request]));
       //$collection = $this->getClassMethodsWithoutMagicMethods($c);     
       //$methods = get_class_methods($controller);


 
 if(isset($request->getAttributes(\Framework\Router\RouteGroup::class)['Framework\Router\RouteGroup'])){
$routei = $request->getAttributes(\Framework\Router\RouteGroup::class)['Framework\Router\RouteGroup'];
if (is_null($routei)) {
           return $delegate->process($request);
        }

if(!$routei->getMiddleware() === true){ // a  middlewwares



        $callback = $routei->getCallable()->getCallback();
        //$middle 
        if (!is_array($callback)) {
            $callback = [$callback];

        }

        return (new CombinedMiddleware($this->container, $callback, $this->debugBarRenderer, $this->debugbar))->handle($request);
    } 
$middleware = $routei->getMiddleware();
$request = $request->withAttribute(get_class($routei), $routei);
$callback = $routei->getCallable()->getCallback();
return (new CombinedMiddleware($this->container, [$callback, $middleware] , $this->debugBarRenderer, $this->debugbar))->handle($request);
}
if (!isset($request->getAttributes(\Framework\Router\Routy::class)['Framework\Router\Routy'])) {
           return $delegate->process($request);
 }

        $route = $request->getAttributes(\Framework\Router\Routy::class)['Framework\Router\Routy'];
        $routes = $route->getCallback()->getRouter()->getRoutes();
        
        if (is_null($route)) {
           return $delegate->process($request);
        }
        $callback = $route->getCallback();

        if (!is_array($callback)) {
            $callback = [$callback];

        }
        $request = $request->withAttribute(get_class($route), $route);

        return (new CombinedMiddleware($this->container, $callback, $this->debugBarRenderer,$this->debugbar))->process($request, $delegate);
        //return (new CombinedMiddleware($this->container, $callback))->process($request, $delegate);
    }
}
