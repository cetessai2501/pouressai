<?php

namespace App;

use App\Auth\Exception\ForbiddenException;
use Framework\Session\FlashService;
use App\Handlers\Error;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Response;
use App\Routery;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use App\Response\RedirectResponse;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Exception\HttpNotFoundException;

class Handler implements MiddlewareInterface
{
    /**
     * @var Messages
     */
    private $flash;

    private $container;

    /**
     * @var Router
     */
    private $router;

    private $routess = [];

    /**
     * @var Error
     */
    private $errorHandler;

    public function __construct(ContainerInterface $container, Routery $router)
    {
        $this->container = $container;
        
        $this->router = $this->container->get('router');
        $this->routess =  $this->container->get('App\MyApp')->getRouteCollector()->getRoutes();
        $this->errorHandler = new Error(true);
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {

try {
$delegate->handle($request);
} catch (HttpNotFoundException $e) {

return $this->errorHandler->error($request, $e);
//return new RedirectResponse($this->router->pathFor('login'));

     }



           
        return $delegate->handle($request);
    }

     public function findRoute($pattern)
    {
        if (!isset($this->routes[$identifier])) {
            throw new RuntimeException('Route not found, looks like your route cache is stale.');
        }
        return $route[$pattern];
    }


        public function redirectLogin(ServerRequestInterface $request): ResponseInterface
    {
        //$this->session->set('auth.redirect', $request->getUri()->getPath());
        $this->flash->error('Vous devez etre connecté et admin pour accéder à cette page !!');
        return new RedirectResponse($this->router->pathFor('login'));
    }

}
