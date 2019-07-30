<?php
namespace Framework\Middleware;

use Framework\Routery;
use Psr\Http\Message\ServerRequestInterface;
use Framework\App;
use League\Route\Router;
use FastRoute\Dispatcher as FastRoute;
use FastRoute\Dispatcher\GroupCountBased as GroupCountBasedDispatcher;
use Framework\Router\MiddlewareApp;
use League\Route\RouteCollection;
use App\Blog\Actions\TestAction;
use Framework\Router\Routy;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Framework\Middleware\NotFoundMiddleware;
use Framework\Router\RouteGroup;


class RouterMiddleware  extends GroupCountBasedDispatcher
{
    public $routess = [];   
    private $route;
    /**
     * @var Router
     */
    public $router;

    protected $container; 

    private $callback = [];  

    public function __construct(ContainerInterface $container, Routery $router)
    {
        $this->container = $container;
        $this->router = $this->container->get('Framework\Routery'); 
        
    }

    public function getRoutess() 
    {
         return $this->routess;
    }

    public function dispatchi(ServerRequestInterface $request) 
    {
        var_dump($request);
die();
    }
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {

        $pieces = explode("/", $request->getRequestTarget());
        $baseroute = $this->router->match($request);

        //$route = $this->router->match($request);
        $app = $this->container->get(App::class);

        if (is_null($baseroute)) {
            return (new NotFoundMiddleware())->__invoke($request, $next);
        } 

        if (isset($baseroute->getAttributes(\Framework\Router\Routy::class)['Framework\Router\Routy'])) {
          $route = $baseroute->getAttributes(\Framework\Router\Routy::class)['Framework\Router\Routy'];

           if(isset($pieces[1]) && isset($pieces[2]) && $pieces[1] === 'boutique'){
             $slug = $pieces[2];
          $params = [ 'slag' => $slug];
           $route->setParams($params);
            $paramis = $route->getParams(); 
  
         $request = array_reduce(array_keys($paramis), function ($request, $key) use ($paramis) {
          return $request->withAttribute($key, $paramis[$key]);
           }, $request);
        
 
          } 


        if(isset($pieces[2]) && isset($pieces[3])){
                $slug = $pieces[2];
                $id =  $pieces[3];

                $params = ['id' => intval($id), 'slug' => $slug];
                $route->setParams($params);
                $paramis = $route->getParams(); 

                //$router->setMatched($route);
        $request = array_reduce(array_keys($paramis), function ($request, $key) use ($paramis) {
            return $request->withAttribute($key, $paramis[$key]);
        }, $request);
        }   


         $request = $request->withAttribute(get_class($route), $route);

           return $next($request);  



        }elseif(isset($baseroute->getAttributes(\Framework\Router\RouteGroup::class)['Framework\Router\RouteGroup'])){
           $route = $baseroute->getAttributes(\Framework\Router\RouteGroup::class)['Framework\Router\RouteGroup'];

             if(isset($pieces[1]) && isset($pieces[2]) && $pieces[1] === 'boutique'){
             $slug = $pieces[2];
          $params = [ 'slag' => $slug];
           $route->setParams($params);
            $paramis = $route->getParams(); 
  
         $request = array_reduce(array_keys($paramis), function ($request, $key) use ($paramis) {
          return $request->withAttribute($key, $paramis[$key]);
           }, $request);

 
          } 


        if(isset($pieces[2]) && isset($pieces[3])){
                $slug = $pieces[2];
                $id =  $pieces[3];

                $params = ['id' => intval($id), 'slug' => $slug];
            if(method_exists($route, 'getParams')){
                $route->setParams($params);
                $paramis = $route->getParams(); 

                //$router->setMatched($route);
        $request = array_reduce(array_keys($paramis), function ($request, $key) use ($paramis) {
            return $request->withAttribute($key, $paramis[$key]);
        }, $request);
      }
        }     

$request = $request->withAttribute(get_class($route), $route);








           return $next($request);    

        }

              
        
        


        
    }
}
