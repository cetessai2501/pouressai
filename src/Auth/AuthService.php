<?php
namespace App\Auth;
use App\Auth\Entity\User;
use App\Auth\Table\UserTable;
use App\Session\Session;
use \Firebase\JWT\JWT;


class AuthService 
{
    /**
     * @var UserTable
     */
    private $userTable;
    /**
     * @var SessionInterface
     */
    private $session;
    /**
     * @var User
     */
    private $user = null;
    public function __construct(UserTable $userTable, Session $session)
    {
        $this->userTable = $userTable;
        $this->session = $session;
    }
    /**
     * Permet d'identifier un utilisateur.
     *
     * @param string $username
     * @param string $password
     *
     * @return User|bool
     */
    public function login($username, $password)
    {
        $message = $_SESSION['slimFlash'];
        // On valide les informations
        if (empty($username) || empty($password)) {
            return null;
        }
        // On valide l'utilisateur
        $user = $this->userTable->findByUsername($username);
        //$message = $_SESSION['slimFlash']; 
        
        if ($user && $user->checkPassword($password)) {
            //$this->flashy('Vous etes connecté');
            $secret_key = "demo"; 
            $issuer_claim = "THE_ISSUER"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 60;
$token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $user->id,
                "username" => $user->username,
                "email" => $user->email
        ));



$jwt = JWT::encode($token, $secret_key);

$user->token = $jwt;

            $this->session->set('auth.user', $user->id);
            $this->session->set('auth.username', $user->username);
            $this->session->set('auth.role', $user->role);
            $this->session->set('auth.email', $user->email); 
            $this->session->set('auth.token', $user->token); 
            //$this->session->set('slimFlash', $message); 
            return $user;
        }
        return null;
    }
    /**
     * Récupère un utilisateur depuis la session.
     *
     * @return User|bool
     */
    public function user(): ?User
    {
        if ($this->user) {
            return $this->user;
        }
        $user_id = $this->session->get('auth.user');
        $user_token = $this->session->get('auth.token'); 
        if ($user_id) {
            $user = $this->userTable->find($user_id);
            if ($user) {
                $user->token = $user_token;
                $this->user = $user;
            } else {
                $this->session->delete('auth.user');
            }
        }
        return $this->user;
    }
    /**
     * Déconnecte un utilisateur de l'application.
     */
    public function logout()
    {
        $this->session->delete('auth.user');
        $this->session->delete('auth.username');
        $this->session->delete('auth.role');
        $this->user = null;
    }
}
