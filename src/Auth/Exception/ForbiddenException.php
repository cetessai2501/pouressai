<?php
namespace App\Auth\Exception;
use Psr\Http\Message\RequestInterface;
class ForbiddenException extends \Exception
{
    /**
     * @var RequestInterface
     */
    public $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        //var_dump($request);
        //die(); 
        parent::__construct('Accès à ' . $request->getUri() . ' interdit');
    }
}
