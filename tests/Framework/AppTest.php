<?php
namespace Tests\Framework;

use App\Routery;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use App\MyApp;
use App\Blog\BlogModule;
use Slim\Factory\AppFactory;
use Slim\Routing\RoutingResults;
use App\HomeController;
use Slim\Psr7\Response;
use Slim\Routing\Route;

class AppTest extends TestCase
{
    
    private $appi; 
    /**
     * @var Router
     */
    private $router;

    public function setUp(): void
    {
        $this->appi = new MyApp(AppFactory::determineResponseFactory(), null, null, null, null, dirname(__DIR__) .'/config.php');
        $this->router = $this->appi->getContainer()->get('router'); 
    }

    public function testGetMethod()
    {
        
       
          
        
        $response = new Response();
        $request = new ServerRequest('GET', '/bookiys');
//$tro = $this->appi->get('/bookis', HomeController::class . ':home')->setName('bookis');
$try = $this->router->map(['GET'], '/bookiys', HomeController::class . ':home')->setName('bookiys');



        $this->assertInstanceOf(Route::class, $try);

        $this->appi->run($request); 
        
        
    }

    
}
