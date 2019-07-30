<?php
namespace App\Admin;

use App\Auth\Middleware\RoleMiddleware;
use \App\MyApp;
use \Projek\Slim\Plates;
use \Projek\Slim\PlatesExtension;

class AdminModule 
{
    //public const DEFINITIONS = __DIR__ . '/config.php';
    public function __construct(\App\MyApp $app, Plates $view, string $prefix, RoleMiddleware $roleMiddleware)
    {
        // Gestion des vues
        $view->addPath(__DIR__ . '/views', 'admin');
        // Gestion des routes
        //$app->group($app->getContainer()->get('admin.prefix'), function () {
            //$this->get('', [AdminController::class, 'index'])->setName('admin.index');
        //})->add($roleMiddleware);
    }
}
