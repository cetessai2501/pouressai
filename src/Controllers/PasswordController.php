<?php
namespace App\Controllers;

use App\Auth\Table\UserTable;
use App\Controller;
use App\Mail;
use \Swift_Message;
use \Swift_Mailer;
use \Swift_SmtpTransport;
use App\Validator;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Flash\Messages;

class PasswordController extends Controller
{
    /**
     * Affiche le formulaire de demande de mot de passe.
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function formReset()
    {
        $messages = $this->getFlash()->getMessages(); 
        $this->render('authpasswordreset', compact('messages'));
    }
    /**
     * Envoie l'email de rappel de mot de passe.
     *
     * @param ServerRequestInterface $request
     * @param UserTable              $userTable
     * @param Mail                   $mail
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function reset(ServerRequestInterface $request, UserTable $userTable, Mail $mail)
    {
        
        $params = $request->getParsedBody();
        //var_dump($params);
        //die();
        $errors = (new Validator($params))
            ->required('email')
            ->email('email')
            ->getErrors();
        if (empty($errors)) {
            $user = $userTable->findByEmail($params['email']);
            if ($user) {
                $token = bin2hex(random_bytes(20));
                //var_dump($token);
                //die();
                $userTable->update($user->id, [
                    'password_reset_token' => $token,
                    'password_reset_at'    => date('Y-m-d H:i:s')
                ]);
                //$tpt = new Swift_SmtpTransport('smtp.laposte.net', 587);
                //$tpt->setUsername('iguane25@laposte.net');
                //$tpt->setPassword('froggy25A');
                //$mailer = new Swift_Mailer($tpt);
                //$message = new Swift_Message;
                //$message->setFrom(array('iguane25@laposte.net' => 'iguane25'));
                $name = $params['email'];
                //$message->setTo("$name");
                //$message->setBody("Pour récupérer votre mot de passe click on : http://autre.fr/password/recover/$user->id/$token");
  
                //die();
                //$mailer->send($mail);
                //var_dump($result);
                //die();
                $mail->to($params['email'])
                     ->sujet("récupération de votre mot de passe http://autre.fr")
                     ->body("Pour récupérer votre mot de passe click on : http://autre.fr/password/recover/$user->id/$token")
                     ->envoi("Pour récupérer votre mot de passe click on : http://autre.fr/password/recover/$user->id/$token")
                     ->send();
            }
            $response = new Response();
            $this->flash('success', 'La procédure de réinitialisation de mot de passe a été envoyée');
            return $response->withRedirect('/password/reset');
        }
        return $this->render('authpasswordreset', compact('errors', 'params'));
    }
    /**
     * Permet de réinitialiser son mot de passe.
     *
     * @param int                    $id
     * @param string                 $token
     * @param ServerRequestInterface $request
     * @param UserTable              $userTable
     *
     * @return \Psr\Http\Message\ResponseInterface|string
     */
    public function recover($id, $token, ServerRequestInterface $request, UserTable $userTable)
    {
        /* @var $user \App\Auth\Entity\User */
        
        $user = $userTable->find($id);
        if ($user && $user->isTokenValid($token)) {
            if ($request->getMethod() === 'POST') {
                $params = $request->getParsedBody();
                $errors = (new Validator($params))
                    ->required('password')
                    ->confirm('password')
                    ->getErrors();
                if (empty($errors)) {
                    $userTable->update($id, [
                        'password_reset_token' => null,
                        'password_reset_at'    => null,
                        'password'             => password_hash($params['password'], PASSWORD_DEFAULT)
                    ]);
                    $messages = $this->getFlash()->getMessages(); 
                    $response = new Response();
                    $this->flash('success', 'Votre mot de passe a bien été réinitialisé');
                    return $response->withRedirect('/login');
                }
            }
            //var_dump($this);
            //die();
            return $this->render('authpasswordrecover', compact('errors'));
        }
        $response = new Response();
        $this->flash('error', 'Ce token ne semble pas valide');
        return $response->withRedirect('/password/reset');
    }
}
