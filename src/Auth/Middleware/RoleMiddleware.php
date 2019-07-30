<?php
namespace App\Auth\Middleware;

use App\Auth\AuthService;
use App\Auth\Exception\ForbiddenException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Container\ContainerInterface;
use App\Controller;

class RoleMiddleware extends Controller
{
    /**
     * @var string
     */
    private $role;
    /**
     * @var AuthService
     */
    private $auth;

    protected $container;

    public function __construct(AuthService $auth, string $role, ContainerInterface $container)
    {
        $this->role = $role;
        $this->auth = $auth;
        $this->container = $container;
    }
    public function __invoke(RequestInterface $request, RequestHandler $handler): ResponseInterface
    {
        $user = $this->auth->user();



        
        if ($user && $user->role === 'admin') {
 $response = $handler->handle($request->withAttribute('user', $user));
        //$existingContent = (string) $response->getBody();
    
        
        //$response->getBody()->write('BEFORE' . $existingContent);
            return $response;
        }
$router = $this->container->get(\App\Routery::class);

 $redirectUri = $router->pathFor('auth.login');

return (new Response())
            ->withStatus(301)
            ->withHeader('location', $redirectUri);
        
        
    }
}
