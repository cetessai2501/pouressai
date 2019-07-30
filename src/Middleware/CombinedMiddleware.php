<?php
namespace Framework\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use League\Route\Router;
use App\Blog\Actions\TestAction;
use Framework\Middleware\NotFoundMiddleware;
use DebugBar\JavascriptRenderer as DebugBarRenderer;
use DebugBar\StandardDebugBar;
use Barryvdh\Debugbar\LaravelDebugbar;

class CombinedMiddleware implements MiddlewareInterface
{
    private $debugBarRenderer;
    /**
     * @var ContainerInterface
     */
    private $container;

    public $debugbar;  
    /**
     * @var array
     */
    private $middlewares;

    public function __construct(ContainerInterface $container, array $middlewares, DebugBarRenderer $debugbarRenderer, StandardDebugBar $debugbar)
    {
        $this->container = $container;
        $this->middlewares = $middlewares;
        $this->debugBarRenderer = $debugbarRenderer;
        $this->debugbar = $this->container->get('debugbar');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {

        $delegate = new CombinedMiddlewareDelegate($this->container, $this->middlewares, $delegate,$this->debugBarRenderer,$this->debugbar);
        return $delegate->process($request);
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
$route = $request->getAttributes(\Framework\Router\RouteGroup::class)['Framework\Router\RouteGroup'];
$middleware = $route->getMiddleware();

$callback = $route->getCallable()->getCallback();
if(isset($this->middlewares[1])){
return call_user_func_array([$this->middlewares[1], 'handle'], [$request]);
}
return  call_user_func_array([$callback[0], $route->getName()], [$request]);


//die();
//return  call_user_func_array([$route->call()[0], $route->getName()], [$request]);
         
    }
}


