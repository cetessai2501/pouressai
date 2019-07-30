<?php

namespace App;


use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Psr\Http\Message\ResponseFactoryInterface;
use Slim\Psr7\Response;
use Slim\Routing\RouteResolver;
use Slim\Routing\RouteRunner;
use Slim\MiddlewareDispatcher;
use Slim\CallableResolver;
use DI\Container;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Routing\RouteCollector;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\ResponseEmitter;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\PDOCollector;
use Barryvdh\Debugbar\LaravelDebugbar;
use DebugBar\StandardDebugBar;
use \Firebase\JWT\JWT;

class MyApp  
{

protected $container;

protected $routeResolver;

protected $middlewareDispatcher;

protected $callableResolver;

protected $debugBarRenderer;

protected $responseFactory;

protected $routeCollector;

private $modules;

private $definitions;

public $debugbar;


public function __construct(
        
        ResponseFactoryInterface $responseFactory,
        ContainerInterface $container = null,
        CallableResolverInterface $callableResolver = null,
        
        RouteCollector $routeCollector = null,
        RouteResolverInterface $routeResolver = null,
        $definitions = [],
        array $modules = []     
    ) {
        
        if (is_string($definitions)) {
            $definitions = [$definitions];
        }
        if (!$this->isSequential($definitions)) {
            $definitions = [$definitions];
        }
        $this->definitions = $definitions; 
        $this->modules = $modules;
        $this->responseFactory = $responseFactory;
        $this->callableResolver = new CallableResolver($this->getContainer());
        $this->routeCollector = new RouteCollector($responseFactory, $this->callableResolver);
        $this->routeResolver = new RouteResolver($this->routeCollector);
        $routeRunner = new RouteRunner($this->routeResolver);

                             
                             
        $this->middlewareDispatcher = new MiddlewareDispatcher($routeRunner, $this->getContainer());
        foreach ($modules as $module) {
            //var_dump($this->getContainer()->get($module)); 
            $this->getContainer()->get($module);
            $this->addModule($module);
        }
    }

public function addModule(string $module): self
    {
        
        $this->modules[] = $module;
        return $this;
    }

public function getRouteResolver()
    {
        return $this->routeResolver;
    }

public function getmiddlewareDispatcher()
    {
        return $this->middlewareDispatcher;
    }

public function getCallableResolver()
{
    return $this->callableResolver;

}

public function getFactory()
{
    return $this->responseFactory;

}


public function add($middleware): self
    {
        $this->middlewareDispatcher->add($middleware);
        return $this;
    }







    public function run(ServerRequestInterface $request = null): void
    {
        if (!$request) {
            $serverRequestCreator = ServerRequestCreatorFactory::create();
            $request = $serverRequestCreator->createServerRequestFromGlobals();
        }
$uri = $request->getUri()->getPath();
$router = $this->getContainer()->get('router');
//$ris = $router->getMatchedRoutes($request->getMethod(),$request->getUri());
$route = $router->match($request);



$auth = $this->getContainer()->get('auth.service');

if($auth->user() !== null){


//var_dump($response);



$token = $auth->user()->token;
try {
   $decoded = JWT::decode($token, "demo", array('HS256'));
$response = $this->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response->withHeader('Authorization', 'Basic : ' .$token));

}catch (\Firebase\JWT\ExpiredException $e){

$response = $this->handle($request);
$responseEmitter = new ResponseEmitter();

$responseEmitter->emit($response);

}




}else{
$response = $this->handle($request);
$responseEmitter = new ResponseEmitter();
$responseEmitter->emit($response);

}


    }


public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //$this->debugbar->boot(); 
        $response = $this->middlewareDispatcher->handle($request);

        //$this->attachDebugBarToResponse($response); 
        /**
         * This is to be in compliance with RFC 2616, Section 9.
         * If the incoming request method is HEAD, we need to ensure that the response body
         * is empty as the request may fall back on a GET route handler due to FastRoute's
         * routing logic which could potentially append content to the response body
         * https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.4
         */
        $method = strtoupper($request->getMethod());
        if ($method === 'HEAD') {
            $emptyBody = $this->responseFactory->createResponse()->getBody();
            return $response->withBody($emptyBody);
        }

        return $response;
    }







private function isSequential(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }

public function getRouteCollector()
    {
        return $this->routeCollector;
    }



public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new \DI\ContainerBuilder();
            //$builder->enableDefinitionCache();
            //$builder->useAnnotations(false);
            //$builder->setDefinitions(new StandardDebugBar());
            $builder->addDefinitions(dirname(__DIR__) .'/config.php');
            $builder->useAnnotations(true); 
            //$builder->addDefinitions($this->definitions);
        $builder->addDefinitions([
            'app'            => $this,
            get_class($this) => $this
        ]);

        
        

            //$builder->addDefinitions($this->definitions[0]);
            
            
            
            
            
            $this->container = $builder->build();

        }
        return $this->container;
    }



   public function getModules(): array
    {
        return $this->modules;
    }


}
