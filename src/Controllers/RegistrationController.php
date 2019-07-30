<?php
namespace App\Controllers;

use App\Auth\Table\UserTable;
use App\Controller;
use App\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Response;

class RegistrationController extends Controller
{

    protected $view;

    

    protected $userTable;

    public function __construct(ContainerInterface $container, UserTable $userTable)
    {
        
        $this->container = $container;
        $this->view = $container->get('homeview');
        $this->userTable =  $userTable;
        
    }  


    public function register(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $message = $_SESSION['slimFlash'];
            $messages = $this->getFlash()->getMessages();
            $params = $request->getParsedBody();
            $errors = $this->validates($params, $this->userTable);
            if (empty($errors)) {
                $this->userTable->insert([
                    'username' => $params['username'],
                    'password' => password_hash($params['password'], PASSWORD_DEFAULT),
                    'email'    => $params['email']
                ]);
                $messag = $this->getFlash()->addMessage('success',"votre compte a été crée vous pouvez vous loggez ici");
                //$this->flashy('compte cree');
                
                //$reponse = new Response();
                //$es = $response->write('some'); 
                return $this->redirect('auth.login', array('messag' => $messag)); 
                //return $es->redirect('auth.login');
                //return $response->write('some');
            }
            $user = $params;
        }
        $response = new Response();
        $this->view->render($response,'registrationregister', compact('errors', 'user','messages','messag','message'));
        return $response;

        
    }
    private function validates(array $params, UserTable $userTable)
    {
        return (new Validator($params))
            ->setDatabase($this->userTable->getDatabase())
            ->required('email', 'username', 'password', 'password_confirm')
            ->email('email')
            ->unique('email', $this->userTable->getTable())
            ->unique('username', $this->userTable->getTable())
            ->minLength('username', 4)
            ->maxLength('username', 20)
            ->confirm('password')
            ->minLength('password', 4)
            ->getErrors();
    }

     protected function getSuccessUpdateMessage()
    {
        return "L'élément a bien été modifié";
    }  
}
