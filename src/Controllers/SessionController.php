<?php
namespace App\Controllers;

use App\Auth\AuthService;
use App\Controller;
use App\Session\SessionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Slim\Flash\Messages;

class SessionController extends Controller
{
    protected $view;

    protected $service;

    protected $session;

    public function __construct(ContainerInterface $container)
    {
        
        $this->container = $container;
        $this->view = $container->get('homeview');
        $this->service = $container->get('auth.service'); 
        $this->session = $container->get('session');
    }  


    public function create(ServerRequestInterface $request)
    {
        $params = $request->getAttribute('params');
        $redirectMessages = $this->getFlash()->getMessage('redirect');
        $messages = $this->getFlash()->getMessages(); 
        $messag = $this->getFlash()->getMessages(); 
        $user = $this->getFlash()->getMessages();  
        //unset($_SESSION);
        
         
        $response = new Response();
        $this->view->render($response, 'authlogin', array('messages' => $messages, 'messag' => $messag, 'user' => $user));
        return $response;
        //$redirect = count($redirectMessages) > 0 ? $redirectMessages[0] : null;
        
    }
    public function store(ServerRequestInterface $request)
    {
        $username = $request->getParsedBody()['username'];
        $password = $request->getParsedBody()['password'];
        $redirect = $this->session->get('auth.redirect') ?: '/';
        //$message = $_SESSION['slimFlash'];
        //var_dump($redirect);  
        //die();
        $user = $this->service->login($username, $password);

        if ($user) {
            //var_dump($user);
            $this->flashy('Vous etes connecté');
            ///$user->setToken($user->token);
 
            //$this->flashy('Vous etes connecté');
            //$messag = $_SESSION['slimFlash'];          
            $messag = $this->getFlash()->addMessage('success',"vous etes bien connecté désormais");
            //$messag = $this->flashy('Vous êtes bien connecté désormais');
            //var_dump($message);
            //die();
            return $this->redirect('home', array('messag' => $messag, 'user' => $user));
            //$this->render('home', array('message' => $message));
            //$this->redirect('home');
        }else{
        //$message = $_SESSION['slimFlash'];
        //$this->flashy("Mot de passe ou identifiant incorrect");
        //var_dump($message);
        //die();
        //return $this->redirect('auth.login', array('message' => $message));
        $messag = $this->getFlash()->addMessage('failed',"Mot de passe ou identifiant incorrect");
        //$params = $request->getParams();
        //die();
        $response = new Response();
        $this->view->render($response,'authlogin', array('messagei' => $messag));
        return $response; 
        //$messag = $this->getFlash()->addMessage('error',"Mot de passe ou identifiant incorrect"); 
        //$this->flash('error', 'Mot de passe ou identifiant incorrect');
        //var_dump($messag);
        //die();
        }
        //return $this->redirect('auth.login', array('message' => $message));
    }
    public function destroy(ServerRequestInterface $request)
    {
        $this->service->logout();
        //$message = $_SESSION['slimFlash']; 
        $message = $this->getFlash()->addMessage('success',"vous etes bien déconnecté désormais");
        //$this->flashy('Vous êtes bien déconnecté désormais');
        return $this->redirect('home', array('message' => $message));
        //$this->redirect('home');
    }
}
