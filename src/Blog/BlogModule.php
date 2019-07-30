<?php
namespace App\Blog;

use App\Auth\Middleware\RoleMiddleware;
use App\Controllers\Admin\BlogController as AdminBlogController;
use App\Controllers\Admin\CategoriesController as AdminCategoriesController;
use App\Blog\Actions\BlogAction;
//use Framework\App;
use App\Module;
use App\MyApp;
use App\Controllers\BlogController;
use App\Renderer\PHPRenderer;
use Psr\Container\ContainerInterface;
use Slim\Routing\RouteCollectorProxy;
use App\Blog\Table\PostTable;

class BlogModule extends Module
{
    public const MIGRATIONS = __DIR__ . '/db/migrations';
    public const SEEDS = __DIR__ . '/db/seeds';
    public const DEFINITIONS = __DIR__ . '/config.php';

    const NAME = 'blog';   

    public function __construct(string $path, string $prefix, MyApp $app,PHPRenderer $renderer, ContainerInterface $container, PostTable $table) {
        // Ajout du dossier des vues
        //$renderer->addPath(__DIR__ . '/views', 'blog');
        // Gestion des routes
$router = $container->get('router');
        $router->get('/blog', BlogAction::class. ':index')->setName('blogi');

        //die();
        $router->get('/blog/{slug}', BlogAction::class. ':show')->setName('showi');
        $router->post('/blog/{slug}', BlogAction::class. ':setComments')->setName('showi.comments');
        $router->get('/blog/delete/{id:[0-9]+}', BlogAction::class. ':deleteComments')->setName('delete.comments');
        $router->get('/blog/tag/{slug}', BlogAction::class. ':getTag')->setName('taggi');
        
        $router->get('/blog/pdf/{slug}', [BlogController::class, 'getPDF'])->setName('blog.pdf');
        $router->get('/blog/odt/{slug}', [BlogController::class, 'getODT'])->setName('blog.odt');
        $router->get('/blog/word/{slug}', [BlogController::class, 'getDOCX'])->setName('blog.docx');
        $router->get('/blog/txt/{slug}', [BlogController::class, 'PDFtoText'])->setName('blog.txt');
        $router->get('/blog/category/{id:[0-9]+}', BlogAction::class. ':category')->setName('blog.category');
        // Pour le backend
        //var_dump($router);
            $router->group($app->getContainer()->get('admin.prefix'), function ($router) {
                // Gestion des articles
                //$container = $app->getContainer();
                $router->get('/blog', AdminBlogController::class. ':index')->setName('blogadminindex');
                $router
                    ->map(['GET', 'POST'], '/blog/new', AdminBlogController::class. ':create')
                    ->setName('blog.admin.create');
                $router
                    ->map(['GET', 'POST'], '/blog/{id:[0-9]+}', AdminBlogController::class. ':edit')
                    ->setName('blog.admin.edit');
                
                // Gestion des categories
                $router
                    ->get('/categories', AdminCategoriesController::class. ':index')
                    ->setName('blog.admin.category.index');
                $router
                    ->map(['GET', 'POST'], '/categories/new', AdminCategoriesController::class. ':create')
                    ->setName('blog.admin.category.create');
                $router
                    ->map(['GET', 'POST'], '/categories/{id:[0-9]+}', AdminCategoriesController::class. ':edit')
                    ->setName('blog.admin.category.edit');
                
            })->add($container->get('admin.middleware'));

      
        $router->group('/api', function ($router) {
$router->get('/items/{slug}/{limit}/{offset}/{page}', \App\Controllers\ControllerApi::class. ':fetchiSlug' )->setName('api.tryddslug');
$router->get('/posts/{id:[0-9]+}', \App\Controllers\ControllerApi::class. ':index')->setName('api.index');
$router->get('/photos/{slug}[?p={1}&limit={1}&offset={1}]', 'App\Controllers\ControllerApi:fetcholl')->setName('api.fetch');

$router->get('/commentis/{slug}', 'App\Controllers\ControllerApi:commentisBySlug')->setName('api.comslug');
$router->get('/comments', 'App\Controllers\ControllerApi:comments')->setName('api.comments');
$router->get('/comments/{slug}', 'App\Controllers\ControllerApi:commentsSlug')->setName('api.combyslug');
$router->get('/items', 'App\Controllers\ControllerApi:commentsAdd')->setName('api.addtryslug');
$router->post('/comments/delete', 'App\Controllers\ControllerApi:commentsById')->setName('api.combyid');
$router->post('/comments/edit/{id:[0-9]+}', 'App\Controllers\ControllerApi:commentsEdit')->setName('api.comedit');
$router->post('/commentis', 'App\Controllers\ControllerApi:commentsAdd')->setName('api.commentisadd');
$router->post('/commentis/delete', 'App\Controllers\ControllerApi:commentsById')->setName('api.combyiddelete');
$router->post('/commentis/edit', 'App\Controllers\ControllerApi:commentsEdit')->setName('api.tisedit');

        });
        // Gestion du widget
        //if ($container->has('admin.widgets')) {
            //$container->get('admin.widgets')->add($container->get(BlogWidget::class));
        //}
    }
}
