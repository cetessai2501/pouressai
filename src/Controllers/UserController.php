<?php
namespace App\Controllers;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use \Projek\Slim\Plates;
use \Projek\Slim\PlatesExtension;
use DI\Container;
use Psr\Container\ContainerInterface;

class UserController
{
    protected $cont;

    public function __construct(Plates $cont)
    {
        $this->cont = $cont;
    }

    public function delete(RequestInterface $request, ResponseInterface $response)
    {
        

        $response->getBody()->write('User deleted');
        return $response;
    }
}

