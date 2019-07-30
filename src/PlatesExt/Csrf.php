<?php
namespace App\PlatesExt;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use App\Middleware\CsrfMiddleware;

class Csrf implements ExtensionInterface
{
    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;


    public function __construct(CsrfMiddleware $csrfMiddleware)
    {
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function register(Engine $engine)
    {
        $engine->registerFunction('csrf_input', [$this, 'csrf_input']);
        $engine->registerFunction('array_pluck', [$this, 'array_pluck']);
        $engine->registerFunction('flatten', [$this, 'flatten']);
        $engine->registerFunction('array_equal', [$this, 'array_equal']);
        $engine->registerFunction('gravatar', [$this, 'gravatar']);
        $engine->registerFunction('csrf_value', [$this, 'csrf_value']);
    }

    public function csrf_value()
    {
        return $this->csrfMiddleware->getSessionKey();
    } 

    public function csrf_input()
    {
        return '<input type="hidden" ' .
            'name="' . $this->csrfMiddleware->getFormKey() . '" ' .
            'value="' . $this->csrfMiddleware->generateToken() . '"/>';
    }

    public function array_pluck($key, $input) {
    if (is_array($key) || !is_array($input)) return array();
    $array = array();
    foreach($input as $v) {
        if(array_key_exists($key, $v)) $array[]=$v[$key];
    }
    return $array;
    } 

    public function array_equal($a, $b) {
    return (
         is_array($a) 
         && is_array($b) 
         && count($a) == count($b) 
         && array_diff($a, $b) === array_diff($b, $a)
    );
    }

    public function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
    }

    public function gravatar($email = '', $rating = 'pg') {
   
    $email2 = $email; 
    
    $email = md5(strtolower(trim($email)));
    
    $gravatar = "http://www.gravatar.com/avatar/$email?d=404";  
    
    $src2 = "http://www.gravatar.com/avatar";
$headers = get_headers($gravatar,1);
if (strpos($headers[0],'200')) echo "<img src='$gravatar' style='border-radius:50%;border: 2px solid red;' width='40' height='40' title='$email2' alt='Avatar'>"; // OK
else if (strpos($headers[0],'404')) echo '<img src="'.$src2.'" width="40" height="40" style="border-radius:50%;border: 2px solid red;" alt="Avatar" title="Gravatar">'; // Not Found
}


}
