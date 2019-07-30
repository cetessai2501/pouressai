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
use Framework\App;
use Framework\Auth;
use Framework\Routery;

class HomeAction
{

    protected $container;

    private $renderer;
   
    private $postTable;

    public $router;  
    
    /**
     * @var RendererInterface
     */
    private $auth;

    public function __construct(Routery $router, ContainerInterface $container, RenderInterface $renderer, Auth $auth)
    {
       $this->container = $container;
       $this->renderer             = $this->container->get('home');  
       $this->auth = $auth;
       $this->router = $this->container->get('\Framework\Routery');   
    }

    


           public function groupindex(Request $request): Response 
    {
        //return "helo";
$router = $this->container->get('\Framework\Routery');

   $user = $this->auth->getUser();
//var_dump($user); 
return $this->renderer->render('home', ['name' => 'Jonathan', 'router' => $this->router]);
    } 

        public function group(Request $request): Response 
    {
        //return "helo";
$router = $this->container->get('\Framework\Routery');

   $user = $this->auth->getUser();
//var_dump($user); 
return $this->renderer->render('home', ['name' => 'Jonathan', 'router' => $this->router]);
    }
   

    public function index(Request $request): Response 
    {
        //return "helo";
$router = $this->container->get('\Framework\Routery');
$route = $request->getAttribute('Framework\Router\Routy');
   $user = $this->auth->getUser();
//var_dump($user); 
return $this->renderer->render('home', ['name' => 'Jonathan', 'router' => $this->router]);
    }
    
    public function bout(Request $request): Response 
    {
$router = $this->container->get('\Framework\Routery');
$route = $request->getAttribute('Framework\Router\Routy');
$user = $this->auth->getUser();
//var_dump($user);
 //$router->pathFor('index', []); 
        //return "helo";
return $this->renderer->render('home', ['name' => 'Jonathan', 'router' => $this->router]);

    }

    public function home(Request $request): Response 
    {
        //return "helo";
$router = $this->container->get('\Framework\Routery');
$user = $this->auth->getUser();

$route = $request->getAttribute('Framework\Router\Routy');

return $this->renderer->render('home', ['name' => 'Jonathan', 'router' => $this->router]);


    }
}
