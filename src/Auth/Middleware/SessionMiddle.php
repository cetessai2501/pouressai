<?php
namespace App\Auth\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use App\Session\Session;


class SessionMiddle
{

    public $session;

public function __construct(Session $session)
    {
        $this->session = $session;
        
    }


public function __invoke(Request $request, RequestHandler $handler)
    {
        if (session_status() == PHP_SESSION_NONE) {
    $this->session->ensureStarted();
}



$response = $handler->handle($request);
return $response; 




    }















}
