<?php
namespace App\Auth\Entity;

class User
{
    public $password;
    public $username;
    public $id;
    public $email;
    public $password_reset_token;
    public $password_reset_at;
    public $stripe_customer_id;
    public $token; 


    public function setToken($token)
    {
         $this->token = $token;
         return $this;
    }

    public function checkPassword($password)
    {
        return password_verify($password, $this->password);
    }
    public function hasRole($role)
    {
        return in_array($role, [$this->role], true);
    }
    public function isTokenValid($token)
    {
        if ($this->password_reset_token !== $token) {
            return false;
        }
        $resetAt = new \DateTime($this->password_reset_at);
        $diff = (time() - $resetAt->getTimestamp()) / 3600;
        if ($diff > 1) {
            return false;
        }
        return true;
    }
}
