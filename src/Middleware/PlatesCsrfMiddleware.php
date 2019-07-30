<?php
namespace App\Middleware;


use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use App\Middleware\CsrfMiddleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class PlatesCsrfMiddleware {

public function __construct(CsrfMiddleware $csrf){
         
         $this->csrf = $csrf;

} 

public function __invoke(RequestInterface $request, RequestHandler $handler) {
         
         $csrf = $this->csrf;
         $user = $csrf->csrf_input();
$response = $handler->handle($request->withAttribute('csrf_status', $user));
        //$response->withRedirect('/nous-contacter/post');
                     
        return  $response;
        


     } 



} 
