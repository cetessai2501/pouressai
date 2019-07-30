<?php
namespace App\Controllers\Admin;

use App\Admin\CrudController;
use App\Blog\PostUpload;
use App\Blog\Table\CategoriesTable;
use App\Blog\Table\PostTable;
use App\Database\Database;
use App\Validator;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Middleware\CsrfMiddleware;

class BlogController extends CrudController
{
    protected $namespace = 'blog';
    protected $files = ['image', 'image2'];
    /**
     * @var CategoriesTable
     */
    protected $uploader;  
    private $categoriesTable;
    public function __construct(
        ContainerInterface $container,
        PostTable $table,
        CategoriesTable $categoriesTable,
        PostUpload $uploader
    ) {
        parent::__construct($container, $table,$categoriesTable, $uploader );
        $this->categoriesTable = $categoriesTable;
        $this->uploader = $uploader;
    }
    public function preForm(ServerRequestInterface $request)
    {
        return [
            'categories' => $this->categoriesTable->findList('name')
        ];
    }
    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    protected function getParams(ServerRequestInterface $request): array
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'content', 'slug','image' ,'category_id'], true);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getParamsContent(ServerRequestInterface $request): array
     {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['content'], true);
        }, ARRAY_FILTER_USE_KEY);
    }
    protected function getParamsImage(ServerRequestInterface $request): array
     {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['image'], true);
        }, ARRAY_FILTER_USE_KEY);
    }
    /**
     * Valide les données.
     *
     * @param ServerRequestInterface $request
     * @param Database               $databasea
     * @param int|null               $postId
     *
     * @return array
     */
   //protected function validates(ServerRequestInterface $request, Database $databasea, ?int $id = null): array
    //{
        //return [];
    //}
}
