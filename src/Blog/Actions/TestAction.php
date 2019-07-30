<?php
namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Psr\Container\ContainerInterface;
use Framework\Renderer\RenderInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use League\Route\Router;
use League\Plates\Engine;
use GuzzleHttp\Psr7\Response;

class TestAction
{

    private $container;

    private $router;
    /**
     * @var RendererInterface
     */
    private $renderer;

    public function __construct(Router $router)
    {
       
        $this->router = $router;
        
    }

    public function __invoke(Request $request)
    {
       
        
        return $this->home();
    }

    public function home()
    {
       $response = new Response();
        $response->getBody()->write('<h1>Hello, heelo!</h1>');
        //$response->getBody()->write("<h1>".var_dump($posts). "</h1>");  
        return $response;
    }
}
