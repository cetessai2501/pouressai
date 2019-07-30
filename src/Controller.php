<?php

namespace App;
use DI\Container;
use \Slim\Views\PhpRenderer;
use \Projek\Slim\PlatesExtension;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use App\Messages;
use Slim\Psr7\Response;
use App\MyApp;
use \Firebase\JWT\JWT;
/**
 * Class Controller.
 */
class Controller
{
    /**
     * @var Container
     */
    protected $container;

    protected $view;

    protected $router;  

    
    /**
     * Controller constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
         
        $this->container = $container;
        $this->view = $container->get('homeview');
        $this->auth = $container->get('auth.service'); 
        
    }
    /**
     * Permet de rendre une vue.
     *
     * @param string $filename Nom de la vue à rendre
     * @param array  $data     Données à envoyer à la vue
     *
     * @return ResponseInterface|string
     */
    public function render(string $filename, array $data = [])
    {

        $response = new Response(); 
        $response  =  $this->view->render($response, $filename, $data);
        return $response;
    }
    /**
     * Renvoie une réponse de redirection.
     *
     * @param string $path
     * @param array  $params
     *
     * @return ResponseInterface
     */
    public function redirect(string $path, array $params = [])
    {
        $router = $this->container->get(\App\Routery::class);

 $redirectUri = $router->pathFor($path, $params, []);

return (new Response())
            ->withStatus(301)
            ->withHeader('location', $redirectUri);

        
    }
        
public function getContainer() 
    { 
     return $this->container; 
    } 

        //var_dump($params);
        //die();
        //return $response->withHeader('Location', $router->pathFor($path, $params, []));
    

    public function flashy($message, $type  = 'failed') {
        if (!isset($_SESSION['slimFlash'])) {
            $_SESSION['slimFlash'] = [];
        }
        return $_SESSION['slimFlash'][$type] = $message;
    }

    public function flashi($message, $type = 'success') {
        if (!isset($_SESSION['slimFlash'])) {
            $_SESSION['slimFlash'] = [];
        }
        return $_SESSION['slimFlash'][$type] = $message;
    }
    /**
     * Envoie un message flash.
     *
     * @param string $type
     * @param string $message
     */
    protected function flash(string $type, string $message): void
    {
        $this->getFlash()->addMessage($type, $message);
    }
    /**
     * Récupère le système de message flash.
     *
     * @return Messages
     */
    protected function getFlash()
    {
        return $this->container->get(Messages::class);
    }
}
