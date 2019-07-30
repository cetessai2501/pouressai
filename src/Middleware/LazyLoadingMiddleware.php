<?php
namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Exception\InvalidMiddlewareException;
use Framework\MiddlewareContainer;

class LazyLoadingMiddleware implements MiddlewareInterface
{
    /**
     * @var MiddlewareContainer
     */
    private $container;
    /**
     * @var string
     */
    private $middlewareName;

    public function __construct(MiddlewareContainer $container, string $middlewareName)
    {
        $this->container = $container;
        $this->middlewareName = $middlewareName;
    }
    /**
     * @throws InvalidMiddlewareException for invalid middleware types pulled
     *     from the container.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $middleware = $this->container->get($this->middlewareName);
        var_dump(call_user_func_array($middleware, [$request, $middleware]));
        if (is_callable($middleware)) {
            //var_dump(call_user_func_array($middleware, [$request, $middleware]));
            //die();
 //die();
        }

        return $middleware($request, $handler);
    }

    public function getMiddlewareName()
    {
         return $this->middlewareName;
    }
}
