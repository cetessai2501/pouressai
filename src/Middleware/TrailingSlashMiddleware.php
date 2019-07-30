<?php
namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Psr7\Response as Repons;

class TrailingSlashMiddleware
{

    public function __invoke(ServerRequestInterface $request,  RequestHandlerInterface $handler): Response
    {
        $response = $handler->handle($request);
        $uri = $request->getUri();
$path = $uri->getPath();

if ($path != '/' && substr($path, -1) == '/') {
$uri = $uri->withPath(substr($path, 0, -1));


 if($request->getMethod() == 'GET') {
            return (new Repons())
                ->withStatus(301)
                ->withHeader('Location', $uri);


            //return $response->withRedirect((string)$uri, 301);
        }
        else {
            
             $response = $handler->handle($request->withUri($uri));
             return $response;
        }



        
    }
    return $response;


}



}











