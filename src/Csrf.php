<?php
namespace App;

use Slim\Views\PhpRenderer;
use Psr\Container\ContainerInterface;
use App\Middleware\CsrfMiddleware;

class Csrf extends PhpRenderer
{
    /**
     * @var CsrfMiddleware
     */
    private $csrfMiddleware;

    protected $container;

    public function __construct(ContainerInterface $container, CsrfMiddleware $csrfMiddleware)
    {
        $this->container = $container;
        $this->csrfMiddleware = $csrfMiddleware;
    }

    public function register(PhpRenderer $engine)
    {
        $engine->registerFunction('csrf_input', [$this, 'csrf_input']);
    }

    public function csrf_input()
    {
        return '<input type="hidden" ' .
            'name="' . $this->csrfMiddleware->getFormKey() . '" ' .
            'value="' . $this->csrfMiddleware->generateToken() . '"/>';
    }
}
