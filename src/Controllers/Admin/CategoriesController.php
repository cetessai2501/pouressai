<?php
namespace App\Controllers\Admin;

use App\Blog\Table\CategoriesTable;
use App\Controller;
use App\Database\Database;
use App\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Psr7\Response;

class CategoriesController extends Controller
{

    protected $view; 
    /**
     * @var Table
     */
    public $categoriesTable;

    public function __construct(ContainerInterface $container, CategoriesTable $categoriesTable)
    {
        
        $this->container = $container;
        $this->categoriesTable = $categoriesTable;
        $this->view = $container->get('homeview');
    } 

    public function index(ServerRequestInterface $request)
    {
        //$page = $request->getParam('page', 1);
        $categories = $this->categoriesTable->findAll();
        //var_dump($categories);
        //die();
$router = $this->container->get(\App\Routery::class);
        $response = new Response(); 
        //var_dump($router);
        //die();    
        //$messages = $this->getFlash()->getMessages(); 
        $this->view->render($response, 'blogadmincategoriesindex', compact('categories','router'));
        return $response;  

        
        
    }
    public function create(ServerRequestInterface $request)
    {
        if ($request->getMethod() === 'POST') {
            $category = $this->getParams($request);
            $errors = $this->validates($request, $this->categoriesTable->getDatabase());
            if (empty($errors)) {
                $this->categoriesTable->insert($category);
                
                return $this->redirect('blog.admin.category.index');
            }
        }
        $response = new Response();
        $this->view->render($response,'blogadmincategoriescreate', compact('category', 'errors'));
        return $response;
    }
    public function edit(ServerRequestInterface $request)
    {

        $id = $request->getAttribute('id');  
        $category = $this->categoriesTable->findOrFail(intval($id));
        if ($request->getMethod() === 'POST' && $request->getParsedBody()['_METHOD'] === 'PUT') {
            $CategorieEntity = $category; 
            $category = $this->getParams($request);
            
            $errors = $this->validates($request, $this->categoriesTable->getDatabase(), intval($id));
            //if (empty($errors)) {
                $this->categoriesTable->update(intval($id), $category);
                
                //$this->flash('success', 'La catégorie a bien été modifiée');
                //return $this->redirect('blog.admin.category.index');
                return $this->redirect('blog.admin.category.index');
                //return $response->withRedirect('/admin/categories');
            //}
        }
        if ($request->getMethod() === 'POST' && $request->getParsedBody()['_METHOD'] === 'DELETE') {
              $this->destroy($request);
              return $this->redirect('blog.admin.category.index'); 
        } 
        $response = new Response();
        $this->view->render($response, 'blogadmincategoriesedit', compact('category', 'errors'));
        return $response;
    }
    public function destroy(ServerRequestInterface $request): ResponseInterface
    {
        $id = $request->getAttribute('id');  
        $this->categoriesTable->delete(intval($id));
        return $this->redirect('blog.admin.category.index');  
        //$response = new Response();
        //$this->flash('La catégorie a bien été supprimée');
        
    }
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    private function getParams(ServerRequestInterface $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug'], true);
        }, ARRAY_FILTER_USE_KEY);
    }
    /**
     * Valide les données.
     *
     * @param ServerRequestInterface $request
     * @param Database               $database
     * @param int|null               $categoryId
     *
     * @return array|bool
     */
    private function validates(ServerRequestInterface $request, Database $database, ?int $categoryId = null): array
    {
        $params = $request->getParsedBody();
        return (new Validator($params))
            ->setDatabase($database)
            ->required('name', 'slug')
            ->slug('slug')
            ->unique('slug', 'categories', $categoryId)
            ->minLength('name', 4)
            ->getErrors();
    }
}
