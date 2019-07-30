<?php
namespace App\Auth;

use App\Controllers\PasswordController;
use App\Controllers\SessionController;
use App\Auth\Middleware\LoggedinMiddleware;
use App\Controllers\Admin\BlogController as AdminBlogController;
use App\Controllers\Admin\CategoriesController as AdminCategoriesController;
use App\Blog\Actions\BlogAction;
use App\Auth\AuthService;
use Psr\Container\ContainerInterface;
use App\MyApp;
use App\Module;


class AuthModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/seeds';
    public const DEFINITIONS = '../config.php';
    public function __construct(AuthService $authService, MyApp $app, ContainerInterface $container)
    {
        //var_dump($container->get(
        // Gestion des views
$router = $container->get('router');
        //$view->addPath(__DIR__ . '/views', 'auth');
        // Gestion des routes
        $router->get('/login', SessionController::class. ':create')->setName('auth.login');
            //->add(new FlashMiddleware($view)); 
        $router->post('/login', SessionController::class. ':store')->setName('auth.store');
            //->add(new FlashMiddleware($view)); 
        $router
            ->map(['GET', 'POST'], '/logout', SessionController::class. ':destroy')
            ->setName('auth.logout');
            //->add(new LoggedinMiddleware($authService))
            //->add(new FlashMiddleware($view)); 
        $router->get('/password/reset', [PasswordController::class, 'formReset'])->setName('auth.password_reset');
        $router->post('/password/reset', [PasswordController::class, 'reset']);
        $router->get('/password/recover/{id}/{token}', [PasswordController::class, 'recover'])
            ->setName('auth.password_recover');
        $router->post('/password/recover/{id}/{token}', [PasswordController::class, 'recover']);



         
       








    }
}
