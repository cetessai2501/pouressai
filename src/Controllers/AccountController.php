<?php
namespace App\Controllers;

use App\Auth\Table\UserTable;
use App\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Psr\Container\ContainerInterface;
use App\Auth\AuthService;

class AccountController extends Controller
{

    protected $view;
    protected $service;
    
    protected $userTable;
    

    public function __construct(ContainerInterface $container, UserTable $userTable, AuthService $service)
    {
        
        $this->container = $container;
        $this->view = $container->get('homeview');
        $this->userTable =  $userTable;
        $this->service =  $service;
    }
    /**
     * Permet de consulter son compte.
     *
     * @param ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function account(ServerRequestInterface $request)
    {
        $response = new Response();
        $this->view->render($response, 'registrationaccount', [
            'user' => $request->getAttribute('user')
        ]);
        return $response;
    }
    /**
     * Permet de supprimer son compte.
     *
     * @param ServerRequestInterface $request
     * @param UserTable              $userTable
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function delete(ServerRequestInterface $request)
    {
        $user = $request->getAttribute('user');
        $this->service->logout();
        $this->userTable->delete($user->id);
        $this->getFlash()->addMessage('success',"Votre compte a bien été supprimé'");
       
        //unset($_SESSION['auth.role']);
        //unset($_SESSION['auth.username']);
        //die('ok');
        return $this->redirect('home');
    }
}
