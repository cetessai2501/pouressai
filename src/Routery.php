<?php

namespace App;


use Psr\Container\ContainerInterface;

use FastRoute\DataGenerator\GroupCountBased as RouteGenerator;
use Closure;
use Slim\Middleware\RoutingMiddleware;
use Slim\Middleware\MethodOverrideMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RoutingResults;
use Slim\Routing\Route;
use Slim\DeferredCallable;
use Slim\Factory\AppFactory;
use FastRoute\RouteParser\Std as RouteParser;
use Slim\Interfaces\RouteInterface;
use Slim\Routing\RouteCollectorProxy;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Routing\Dispatcher;

/**
 * Register and match routes
 */
class Routery extends RouteCollectorProxy 
{
     /**
     * FastRoute router
     *
     * @var RouteCollector
     */
    private $router;

    protected $container;

    protected $routes;  

    protected $currentRoute;

    protected $routeGroups; 

    protected $namedRoutes; 

     protected $matchedRoutes; 
     /**
     * Parser
     *
     * @var \FastRoute\RouteParser
     */
    public $parser;

    public $url;

    /**
     * Base path used in pathFor()
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Path to fast route cache file. Set to false to disable route caching
     *
     * @var string|False
     */
    protected $cacheFile = false;

    public $collectioni = [];

    public $collection = [];


    public $matched;

    private $path; 

    /**
     * Routes
     *
     * @var Route[]
     */
     
    /**
     * Route counter incrementer
     * @var int
     */
    protected $routeCounter = 0;

    public $stock = [];  

     
    public $dispatchData = [];
    /**
     * Route groups
     *
     * @var RouteGroup[]
     */
    
    
    /**
     * @var \FastRoute\Dispatcher
     */
    public $dispatcher;

    protected $name;

     
    
    /**
     * Create new router
     *
     * @param RouteParser   $parser
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        // build parent route collector
        $this->routes = array();
$this->router = $this->getContainer()->get('App\MyApp')->getRouteCollector(); 
         $this->routeGroups = array();
$this->matchedRoutes = array();
        $this->parser    =  new RouteParser;
     $this->dispatcher = $dispatcher ?? new Dispatcher($this->router);   
        
        
    }

public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }



    public function getCurrentRoute()
     {
          
 
         if (is_array($this->matchedRoutes) && count($this->matchedRoutes) > 0) {
              return $this->matchedRoutes[0];
          }
  
          return null;
     }



    public function getMatchedRoutes($httpMethod, $resourceUri, $reload = false)
     {
foreach ($this->getContainer()->get('App\MyApp')->getRouteCollector()->getRoutes() as $route) {
$this->matchedRoutes[] = $route;
}


        
 
         return $this->matchedRoutes;
    }
    

public function dispatch(string $method, string $uri)
    {
        $dispatcher = $this->createDispatcher();
        $results = $dispatcher->dispatch($method, $uri);
return $results;

        //return new RoutingResults($this, $method, $uri, $results[0], $results[1], $results[2]);
    }




 public function map(array $methods, string $pattern, $callable): RouteInterface
    {
        $pattern = $this->basePath . $pattern;

        if ($this->getContainer() && $callable instanceof Closure) {
            $callable = $callable->bindTo($this->getContainer());
        }

        return $this->getContainer()->get('App\MyApp')->getRouteCollector()->map($methods, $pattern, $callable);
    }

    /**
     * {@inheritdoc}
     */
    protected function createRoute(array $methods, string $pattern, $callable): RouteInterface
    {
        return new Route(
            $methods,
            $pattern,
            $callable,
            AppFactory::determineResponseFactory(),
            $this->getContainer()->get('App\MyApp')->getCallableResolver(),
            $this->getContainer(),
            null,
            $this->routeGroups,
            $this->routeCounter
        );
    }    


protected function processGroups()
    {
        $pattern = "";
        $middleware = array();
        foreach ($this->routeGroups as $group) {
         $k = key($group);
             $pattern .= $k;
             if (is_array($group[$k])) {
                 $middleware = array_merge($middleware, $group[$k]);
             }
         }
         return array($pattern, $middleware);
    }

public function pushGroup($group, $middleware = array())
    {
         return array_push($this->routeGroups, array($group => $middleware));
     }


    /**
     * {@inheritdoc}
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }
     

    /**
     * {@inheritdoc}
     */
    public function setBasePath(string $basePath): RouteCollectorProxyInterface
    {
        $this->basePath = $basePath;
        return $this;
    }

   

    /**
     * setNewCollection
     *
     * @param mixed $coll
     * @return void
     */
    public function setNewCollection($coll){
        $this->collectioni = $coll;
        return $this;  
    } 

    /**
     * getNewCollection
     *
     * @return void
     */
    public function getNewCollection(){
        
        return $this->collectioni;  
    } 

    /**
     * setCollection
     *
     * @param array $coll
     * @return void
     */
    public function setCollection(array $coll){
        $this->collection = $coll;
        return $this;  
    }  

    /**
     * getCollection
     *
     * @return void
     */
    public function getCollection(){
        
        return $this->collection;  
    } 

    public function setMatched($matched){
        $this->matched = $matched;
        return $this;  
    }  

    public function getMatched(){
        
        return $this->matched;  
    } 
    /**
     * Set path to fast route cache file. If this is false then route caching is disabled.
     *
     * @param string|false $cacheFile
     *
     * @return self
     */
    public function setCacheFile($cacheFile)
    {
        if (!is_string($cacheFile) && $cacheFile !== false) {
            throw new InvalidArgumentException('Router cacheFile must be a string or false');
        }

        $this->cacheFile = $cacheFile;

        if ($cacheFile !== false && !is_writable(dirname($cacheFile))) {
            throw new RuntimeException('Router cacheFile directory must be writable');
        }


        return $this;
    }


    
    /**
     * Add route
     *
     * @param  string[] $methods Array of HTTP methods
     * @param  string   $pattern The route pattern
     * @param  callable $handler The route callable
     *
     * @return RouteInterface
     *
     * @throws InvalidArgumentException if the route pattern isn't a string
     */


public function get(string $pattern, $callable): RouteInterface
    {
        return $this->map(['GET'], $pattern, $callable);
    }


 public function group(string $pattern, $callable): RouteGroupInterface
    {
        $pattern = $this->basePath . $pattern;

//var_dump($this->getContainer()->get('App\MyApp')->getRouteCollector()->group($pattern, $callable));
        return $this->getContainer()->get('App\MyApp')->getRouteCollector()->group($pattern, $callable);
    }   

    /**
     * delete
     *
     * @param mixed $methods
     * @param mixed $pattern
     * @param mixed $handler
     * @param mixed $name
     * @param mixed $params
     * @return void
     */
    public function delete(string $pattern, $callable): RouteInterface
    {
        return $this->map(['DELETE'], $pattern, $callable);
    }


    /**
     * post
     *
     * @param mixed $methods
     * @param mixed $pattern
     * @param mixed $handler
     * @param mixed $name
     * @param mixed $params
     * @return void
     */
    public function post(string $pattern, $callable): RouteInterface
    {
        return $this->map(['POST'], $pattern, $callable);
    }
    /**
     * any
     *
     * @param mixed $method
     * @param mixed $pattern
     * @param mixed $handler
     * @param mixed $name
     * @param mixed $params
     * @return void
     */
    public function any(string $pattern, $callable): RouteInterface
    {
        return $this->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $pattern, $callable);
    }

    /**
     * crud
     *
     * @param mixed $method
     * @param string $prefixPath
     * @param mixed $callable
     * @param mixed $name
     * @return void
     */
    public function crud($method, string $prefixPath, $callable, $name)
    {

        $group = new RouteGroup($method, $prefixPath, new MiddlewareApp($callable, $this) , $name);

        array_push($this->routeGroups, $group);
        return $group; 
         
        
    }

    public function allRoutes($route){

    
    array_push($this->stock, $route);
    //return $this->stock;    
    }
    
    public function getRouts()
    {
          return $this->routs;
    } 

    public function getContainer(): ?ContainerInterface
    {
        return $this->container;
    }
    

    
  
    public function getRouter()
    {
         return $this->router;

     } 
    


 
    /**
     * @return \FastRoute\Dispatcher
     */
    protected function createDispatcher()
    {
        if ($this->dispatcher) {
            return $this->dispatcher;
        }

        $routeDefinitionCallback = function (RouteCollector $r) {
            foreach ($this->getContainer()->get('App\MyApp')->getRouteCollector()->getRoutes() as $route) {
                $r->addRoute($route->getMethods(), $route->getPattern(), $route->getIdentifier());
            }
        };

        $cacheFile = $this->routeCollector->getCacheFile();
        if ($cacheFile) {
            /** @var FastRouteDispatcher $dispatcher */
            $dispatcher = \FastRoute\cachedDispatcher($routeDefinitionCallback, [
                'dispatcher' => FastRouteDispatcher::class,
                'routeParser' => $this->routeParser,
                'cacheFile' => $cacheFile,
            ]);
        } else {
            /** @var FastRouteDispatcher $dispatcher */
            $dispatcher = \FastRoute\simpleDispatcher($routeDefinitionCallback, [
                'dispatcher' => FastRouteDispatcher::class,
                'routeParser' => $this->routeParser,
            ]);
        }

        $this->dispatcher = $dispatcher;
        return $this->dispatcher;
    }

    

    


    public function setRoutes()
   {
      //$this->parser    =  new RouteParser($this->getContainer()->get('App\MyApp')->getRouteCollector()); 
      $this->routes = $this->getContainer()->get('App\MyApp')->getRouteCollector()->getRoutes();
      return $this;
   }   
    /**
     * Get route objects
     *
     * @return Routy[]
     */
    public function getRoutes()
    {
        
          return $this->routes;
         
    }

    public function getNamedRoutey($name)
    {
        foreach ($this->getRoutes() as $route) {
              
             if ($route !== null && $name == $route->getName()) {
                return $route;
            }


           
        }
        //throw new \RuntimeException('Named route does not exist for name: ' . $name);
    }

    
    /**
     * Get named route object
     *
     * @param string $name        Route name
     *
     * @return Route
     *
     * @throws RuntimeException   If named route does not exist
     */
    public function getNamedRoute()
    {
        return $this->routes;
        //throw new RuntimeException('Named route does not exist for name: ' . $name);
    }
    
    /**
     * Remove named route
     *
     * @param string $name        Route name
     *
     * @throws RuntimeException   If named route does not exist
     */
    public function removeNamedRoute($name)
    {
        $route = $this->getNamedRoute($name);

        // no exception, route exists, now remove by id
        unset($this->routes[$route->getIdentifier()]);
    }

    

    

    /**
     * Removes the last route group from the array
     *
     * @return RouteGroup|bool The RouteGroup if successful, else False
     */
    public function popGroup()
    {
        $group = array_pop($this->routeGroups);
        return $group instanceof RouteGroup ? $group : false;
    }

    /**
     * @param $identifier
     * @return \Slim\Interfaces\RouteInterface
     */
    public function lookupRoute($identifier)
    {
        if (!isset($this->routes[$identifier])) {
            throw new RuntimeException('Route not found, looks like your route cache is stale.');
        }
        return $this->routes[$identifier];
    }

    public function match(ServerRequestInterface $request ):?Route
    {

       $url = $request->getUri()->getPath();
       $app = $this->getContainer()->get('App\MyApp');
$this->routes = $this->getContainer()->get('App\MyApp')->getRouteCollector()->getRoutes();
$uri = '/' . ltrim(rawurldecode($url), '/');
   $res = $this->dispatch($request->getMethod(), $uri);
//var_dump($res->getRouteStatus());
       $routingResults = $this->getContainer()->get('App\MyApp')->getRouteResolver()->computeRoutingResults(
            $request->getUri()->getPath(),
            $request->getMethod()
        );

$routeStatus = $routingResults->getRouteStatus();

foreach($this->routes as $route){ 
 
        if($routeStatus === 1 && $route->getIdentifier() === $routingResults->getRouteIdentifier()){
//var_dump($route->getIdentifier());
//var_dump($route->getCallable());

//var_dump($routi);

return $this->matchedRoutes[] = $route;

//return new Route($routingResults->getAllowedMethods(), $routingResults->getUri());
}elseif($routeStatus === 0){

return null;
}



}
//var_dump($this->matchedRoutes);        
//$methods = array();

//die();
 

         

}




    /**
     * Build the path for a named route excluding the base path
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
public function relativeUrlFor(string $routeName, array $data = [], array $queryParams = []): string
    {

        //$route = $this->getNamedRoutey($routeName);
$route = $this->container->get('App\MyApp')->getRouteCollector()->getNamedRoute($routeName);
        $pattern = $route->getPattern();

        $segments = [];
        $segmentName = '';

        /*
         * $routes is an associative array of expressions representing a route as multiple segments
         * There is an expression for each optional parameter plus one without the optional parameters
         * The most specific is last, hence why we reverse the array before iterating over it
         */
        $expressions = array_reverse($this->parser->parse($pattern));
        foreach ($expressions as $expression) {
            foreach ($expression as $segment) {
                /*
                 * Each $segment is either a string or an array of strings
                 * containing optional parameters of an expression
                 */
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }

                /*
                 * If we don't have a data element for this segment in the provided $data
                 * we cancel testing to move onto the next expression with a less specific item
                 */
                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }

                $segments[] = $data[$segment[0]];
            }

            /*
             * If we get to this logic block we have found all the parameters
             * for the provided $data which means we don't need to continue testing
             * less specific expressions
             */
            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments)) {
            throw new \RuntimeException('Missing data for URL segment: ' . $segmentName);
        }

        $url = implode('', $segments);
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
   

}






    public function relativePathFor($name, array $data = [], array $queryParams = [])
    {

      $route = $this->getNamedRoutey($name);
        $path = $route->getPath();
        if(!empty($data)){

        if(isset($data['id'])){
         $str = str_replace(":slug", $data['slug'], $path, $count);
        $str2 = str_replace(":id", $data['id'], $str, $count);
          $routeDatas = $this->parser->parse($str2);
         $routeDatas = array_reverse($routeDatas);
          }else{
         $str = str_replace(":slag", $data['slag'], $path, $count);
        $routeDatas = $this->parser->parse($str);
        $routeDatas = array_reverse($routeDatas);
        }

        $segments = [];
        foreach ($routeDatas as $routeData) {
            foreach ($routeData as $item) {
                if (is_string($item)) {
                    // this segment is a static string
                    $segments[] = $item;
                    continue;
                }

                // This segment has a parameter: first element is the name
                if (!array_key_exists($item[0], $data)) {
                    // we don't have a data element for this segment: cancel
                    // testing this routeData item, so that we can try a less
                    // specific routeData item.
                    $segments = [];
                    $segmentName = $item[0];
                    break;
                }
                $segments[] = $data[$item[0]];
            }
            if (!empty($segments)) {
                // we found all the parameters for this route data, no need to check
                // less specific ones
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }
        $url = implode('', $segments);

        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

        $routeDatas = $this->parser->parse($path);

        // $routeDatas is an array of all possible routes that can be made. There is
        // one routedata for each optional parameter plus one for no optional parameters.
        //
        // The most specific is last, so we look for that first.
        $routeDatas = array_reverse($routeDatas);

        $segments = [];
        foreach ($routeDatas as $routeData) {
            foreach ($routeData as $item) {
                if (is_string($item)) {
                    // this segment is a static string
                    $segments[] = $item;
                    continue;
                }

                // This segment has a parameter: first element is the name
                if (!array_key_exists($item[0], $data)) {
                    // we don't have a data element for this segment: cancel
                    // testing this routeData item, so that we can try a less
                    // specific routeData item.
                    $segments = [];
                    $segmentName = $item[0];
                    break;
                }
                $segments[] = $data[$item[0]];
            }
            if (!empty($segments)) {
                // we found all the parameters for this route data, no need to check
                // less specific ones
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }
        $url = implode('', $segments);

        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;

//var_dump($route);


    }


    /**
     * Build the path for a named route including the base path
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function pathFor($name, array $data = [], array $queryParams = [])
    {

        $url = $this->relativeUrlFor($name, $data, $queryParams);

        if ($this->basePath) {
            $url = $this->basePath . $url;
        }
        

        return $url;
    }

    /**
     * Build the path for a named route.
     *
     * This method is deprecated. Use pathFor() from now on.
     *
     * @param string $name        Route name
     * @param array  $data        Named argument replacement data
     * @param array  $queryParams Optional query string parameters
     *
     * @return string
     *
     * @throws RuntimeException         If named route does not exist
     * @throws InvalidArgumentException If required data not provided
     */
    public function urlFor($name, $params = array())
    {
        if (!$this->hasNamedRoute($name)) {
            throw new \RuntimeException('Named route not found for name: ' . $name);
        }
         $search = array();
         foreach ($params as $key => $value) {
             $search[] = '#:' . preg_quote($key, '#') . '\+?(?!\w)#';
        }
         $pattern = preg_replace($search, $params, $this->getNamedRoute($name)->getPattern());

         //Remove remnants of unpopulated, trailing optional pattern segments, escaped special characters
         return preg_replace('#\(/?:.+\)|\(|\)|\\\\#', '', $pattern);
     }

    
}
