<?php
namespace App\Registration;


use Psr\Container\ContainerInterface;
use App\Controllers\AccountController;
use App\Controllers\RegistrationController;
use App\Registration\Plate\RegistrationExtension;
use App\MyApp;
use App\Module;

//use Framework\View\TwigView;
//use Framework\View\ViewInterface;

class RegistrationModule extends Module
{
    public const DEFINITIONS = '../config.php';
    public function __construct(MyApp $app, ContainerInterface $container)
    {
$router = $container->get('router');
        $router
            ->map(['GET', 'POST'], '/inscription', RegistrationController::class. ':register')
            ->setName('registration.signup');
        $router
            ->get('/mon-compte', AccountController::class. ':account')
            ->setName('registration.account')->add($container->get('admin.middleware'));
            
        $router->map(['POST'], '/mon-compte', AccountController::class. ':delete')->add($container->get('admin.middleware'));
        /* @var \Framework\View\ViewInterface */
        //$view->addPath(__DIR__ . '/views', 'registration');
        
    }
}
