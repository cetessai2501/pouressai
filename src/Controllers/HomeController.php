<?php

namespace App\Controllers;

use App\Controller;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Mail;
use Slim\Flash\Messages;
use App\Validator;
use \Slim\Csrf\Guard;
use App\Auth\AuthService;
use App\Session\Session;


class HomeController extends Controller{
         //affiche le home
        public function home(Request $request, AuthService $auth, Session $session){
            $messag = $this->getFlash()->getMessages(); 
            $message = $this->getFlash()->getMessages(); 
            $user = $session->get('auth.user');
            var_dump($user);
            die();
            $this->render('home', array('messag'=> $messag, 'user' => $user, 'message' => $message));  
        }
        public function getContact(Request $request){
             $messages = $this->getFlash()->getMessages();  
             $message = $this->getFlash()->getMessages();  
             //$valueKey = $guard->getTokenValueKey();
             //$value = $request->getAttribute('csrf_value');
             //$name = $request->getAttribute('csrf_name');
             //var_dump($nameKey);
             //die();
             //$guard->setFailureCallable(function ($request, $response, $next) {
                   //$request = $request->withAttribute("csrf_result", 'FAILED');
                   //return $next($request, $response);
             //});
             //if (false === $request->getAttribute('csrf_result')) {
                    //$response->write("CSRF check failed."); //return $this->redirect('contact'); // Deal with error here and update $response as appropriate
             //} 
             //var_dump($request->getAttribute('csrf_name'));
             //die();

            return $this->render('contact', array('messages'=> $messages, 'message' => $message));           

        }
        
        
        public function postContact(Request $request, Mail $mail){
         
          $params = $request->getParsedBody();
          $email = $request->getParam('email');
          //var_dump($email);
          //die();
          $nom = $request->getParam('nom');
          $content = $request->getParam('content');
          //$name = $request->getAttribute('csrf_name');
          //$value = $request->getAttribute('csrf_value');
          //var_dump($name);
          //die();
          //$guard->validateToken($name, $value);
          //var_dump($request->getAttribute('csrf_value'));
          //die();
         if (false === $request->getAttribute('csrf_status')) {
             echo "failed check csrf";
         } else {
              // successfully passed CSRF check
              $errors = (new Validator($params))
                  ->required('email')
                  ->email('email')
                  ->getErrors();
                        
          if(empty($errors)){
              $mail->to("$email")
                   ->sujet("demande de contact depuis autre.fr")
                   ->body("$nom, $content")
                   ->send();
              $this->flash('success', 'email envoyé');
              //return $this->redirect('contact');  
           }else{
              $this->flash('failed', 'mal rempli');
              
          }
          //die('ok');
          return $this->redirect('contact');  


         } 
        // successfully passed CSRF check
        //$response->write("Passed CSRF check.");
        
    


          
          //$nameKey = $this->csrf->getTokenNameKey();
          //$valueKey = $this->csrf->getTokenValueKey();
          //var_dump($valueKey, $nameKey);
          //die();  
          //$params = [$email, $name, $content];
          //var_dump($params);
          //die();

          
         
          
           
        }


}
