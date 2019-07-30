<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Session\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    /**
     * @var array|\ArrayAccess
     */
    private $session;
    /**
     * @var string
     */
    private $sessionKey;
    /**
     * @var string
     */
    private $formKey;
    /**
     * @var int
     */
    private $limit;
    /**
     * CsrfMiddleware constructor.
     *
     * @param array|\ArrayAccess $session
     * @param int                $limit      Limit the number of token to store in the session
     * @param string             $sessionKey
     * @param string             $formKey
     */
    public function __construct(Session 
        $session,
        int $limit = 50,
        string $sessionKey = 'csrf.tokens',
        string $formKey = '_csrf'
    ) {
        $this->testSession($session);
        $this->session = $session;
        $this->sessionKey = $sessionKey;
        $this->formKey = $formKey;
        $this->limit = $limit;
        
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws NoException
     * @internal param DelegateInterface $delegate
     *
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

       if (in_array($request->getMethod(), ['GET'], true)) {
//var_dump($request->getCookieParams()['XSRF-TOKEN']);
 if ($request->getHeader('X-Xsrf-Token')) {
$tokenval =  $request->getCookieParams()['XSRF-TOKEN'];
return $handler->handle($request->withAttribute('_csrf', $tokenval));
}

       }elseif(in_array($request->getMethod(), ['PUT', 'POST', 'DELETE'], true)) {
            

            if ($request->getHeader('X-Xsrf-Token')) {

                  $tokenkey = '_csrf';
                 $tokenval =  $request->getCookieParams()['XSRF-TOKEN'];
                    $groups = explode(",", $tokenkey);
                    $functions = explode(",", $tokenval);
                    $params = array_combine($groups, $functions);
                    if (!array_key_exists($this->formKey, $params)) {
                            //throw new \Exception('pb car pas de csrf');
                            return $handler->handle($request);
                    }
                    if (!in_array($params[$this->formKey], $this->session[$this->sessionKey] ?? [], true)) {
                           //throw new \Exception('arguments invalides');
                           return $handler->handle($request);
                    }
                    $this->removeToken($params[$this->formKey]);



            }else{
              $params = $request->getParsedBody() ?: [];

            if (!array_key_exists($this->formKey, $params)) {
                throw new \Exception('pb car pas de csrf');
                //return $handler->handle($request);
            }
            if (!in_array($params[$this->formKey], $this->session[$this->sessionKey] ?? [], true)) {
                throw new \Exception('arguments invalides');
                //return $handler->handle($request);
            }
            $this->removeToken($params[$this->formKey]);

            }
            
        }
        return $handler->handle($request);
    }
    /**
     * Generate and store a random token.
     *
     * @return string
     */
    public function generateToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $tokens = $_SESSION[$this->sessionKey] ?? [];
        $tokens[] = $token;
        $_SESSION[$this->sessionKey] = $this->limitTokens($tokens);
        return $token;
    }
    /**
     * Test if the session acts as an array.
     *
     * @param $session
     *
     * @throws \TypeError
     */
    private function testSession($session): void
    {
        if (session_status() == PHP_SESSION_NONE) {
    $session->ensureStarted();
}
        
        if (!isset($_SESSION)) {
            throw new \RuntimeException('CSRF middleware failed. Session not found.');
        }
        //var_dump($_SESSION);
        if (!is_array($session) && !$session instanceof \ArrayAccess) {
            throw new \TypeError('session is not an array');
        }
    }
    /**
     * Remove a token from session.
     *
     * @param string $token
     */
    private function removeToken(string $token): void
    {
        $_SESSION[$this->sessionKey] = array_filter(
            $_SESSION[$this->sessionKey] ?? [],
            function ($t) use ($token) {
                return $token !== $t;
            }
        );
        
    }

    public function csrf_input()
    {
        return '<input type="hidden" ' .
            'name="' . $this->getFormKey() . '" ' .
            'value="' . $this->generateToken() . '"/>';
    }

    private function validSession($session)
    {
           
                //throw new TypeError('pas un tableau');
var_dump($session->getSession());
            


    } 
    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }
    /**
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->formKey;
    }

    
    /**
     * Limit the number of tokens.
     *
     * @param array $tokens
     *
     * @return array
     */
    private function limitTokens(array $tokens): array
    {
        if (count($tokens) > $this->limit) {
            array_shift($tokens);
        }
        return $tokens;
    }
}
