<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class NotFoundMiddleware
{

    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return Response
     */
    public function __invoke(ServerRequestInterface $request, callable $next)
    {
         
        //var_dump($request); die();
        return new Response(404, [], 'Erreur 404');
    }

    public function notfound(ServerRequestInterface $request)
    {
         
        //var_dump($request); die();
        return new Response(404, [], 'Erreur 404');
    }

}
