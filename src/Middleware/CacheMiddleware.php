<?php
namespace App\Action;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class CacheMiddleware implements MiddlewareInterface
{
    protected $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $url  = str_replace('/', '_', $request->getUri()->getPath());
        $file = $this->config['path'] . $url . '.html';
        if ($this->config['enabled'] && file_exists($file) &&
            (time() - filemtime($file)) < $this->config['lifetime']) {
            return new Response(file_get_contents($file));
        }

        $response = $handler->process($request);
        if ($response instanceof Response && $this->config['enabled']) {
            file_put_contents($file, $response->getBody());
        }
        return $response;
    }
}
