<?php
namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Psr\Container\ContainerInterface;
use Framework\Renderer\RenderInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Routery;
use League\Plates\Engine;
use App\Blog\PostUpload;
use App\Blog\Entity\Post;
use Framework\Validator;
use Framework\Events\EventManager;
use App\Blog\Actions\OnDeleteEvent;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class AdminBlogAction
{

    private $router;

    private $postUpload;
    /**
     * @var RendererInterface
     */
    private $renderer;

    private $postTable;

    private $container;

    private $session;

    use RouterAwareAction;

    public function __construct(Routery $router, RenderInterface $renderer, ContainerInterface $container, PostTable $postTable, \Framework\Session\FlashService $flash, PostUpload $postUpload)
    {
        
        
        $this->container = $container;
        $this->postTable = $postTable;
        $this->flash = $flash;
        $this->postUpload = $postUpload;
        $this->renderer = $container->get('badmin');
        $this->router = $container->get('\Framework\Routery'); 
    }

    public function __invoke(Request $request)
    {
        if ($request->getMethod() === 'DELETE') {
            return $this->delete($request);
        }
        if (substr((string)$request->getUri(), -3) === 'new') {
            return $this->create($request);
        }
        if ($request->getAttribute('id')) {
            return $this->edit($request);
        }
        return $this->index($request);
    }

    public function homi(): string
    {
        return $this->container->render('admin', ['name' => 'Jonathan', 'router' => $this->router]);
    }

    public function adminindex(Request $request): Response
    {
        //$session = $this->session;
        $params = $request->getQueryParams();
        $items = $this->postTable->findPaginated(12, $params['page'] ?? 1);


        return $this->renderer->render('adminindex', ['items' => $items,'router' => $this->router]);
    }
     /**
     * Edite un article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function adminedit(Request $request): Response
    {

        try {
    $error = 'Altays throw this error';
    
    $item = $this->postTable->find($request->getAttribute('id'));

    // Code following an exception is not executed.
    //echo 'Never executed';

} catch (\Exception $error) { 
    $response = new Response(404, [], $error->getMessage());    
    return $response;      
}
        if ($request->getMethod() === 'POST') {
            $paramis = array_merge($request->getParsedBody(), $request->getUploadedFiles());
            $validation = $this->getValidator($request, $paramis);

            if ($validation->isValid()) {
                $image = $this->postUpload->upload($paramis['image'][0], $this->getNewEntity()->image);
                if ($image) {
                    $paramis['image'] = $image;
                } else {
                    unset($paramis['image']);
                }
                $params = array_filter($paramis, function ($key) {
                    return in_array($key, ['name', 'slug', 'content', 'updated_at', 'image']);
                }, ARRAY_FILTER_USE_KEY);


            
                $this->postTable->update($item->id, $params);
                $this->flash->success('article bien modifié');
                return $this->redirect('adminindex');
            }
            if ($validation->getErrors()) {
                 $errors = $validation->getErrors();

                 //return $this->redirect('blog.admin.create');
                 return $this->renderer->render('adminedit', compact('item', 'errors'));
            }
        }
         return $this->renderer->render('adminedit', compact('item'));
    }

    protected function getNewEntity()
    {
        $post = new Post();
        $post->created_at = new \DateTime();
        return $post;
    }
     /**
     * Crée un nouvel article
     * @param Request $request
     * @return ResponseInterface|string
     */
    public function admincreate(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $paramis = array_merge($request->getParsedBody(), $request->getUploadedFiles());
  
            $validation = $this->getValidateur($request, $paramis);

            if ($validation->isValid()) {
                $image = $this->postUpload->upload($paramis['image'][0], $this->getNewEntity()->image);
                if ($image) {
                    $paramis['image'] = $image;
                } else {
                    unset($paramis['image']);
                }
                $params = array_filter($paramis, function ($key) {
                    return in_array($key, ['name', 'slug', 'content', 'created_at', 'image']);
                }, ARRAY_FILTER_USE_KEY);




                $this->postTable->insert($params);
                $this->flash->success('article bien crée');
            }
            if ($validation->getErrors()) {
                 $errors = $validation->getErrors();

                 //return $this->redirect('blog.admin.create');
                 return $this->renderer->render('admincreate', compact('errors'));
            }

        //$validation = $this->getValidator($request, $paramis);

        
             
             return $this->redirect('adminindex');
        }
        //var_dump($this->getValidator($request, $paramis));

//var_dump(array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]));
//die();
        //return array_merge($params, ['updated_at' => date('Y-m-d H:i:s')]);

            
        
        return $this->renderer->render('admincreate', compact('item'));
    }

    public function getThumby()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $filename . '_thumb.' . $extension;
    }

    public function uploads(Request $request)
    {
$route = $request->getAttribute('Framework\Router\Routy'); 
$dir = '/home/sophie/dang/public/uploads/originals/';
//$files = glob($dir,GLOB_BRACE);
    
//var_dump($route->getPath());

if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "fichier : $file : type : " . filetype($dir . $file) . "\n";
            $files = [];
            //if (!file_exists($file)) {
            
            //var_dump($files);
            if($file != "." && $file != ".."){
             //header('Content-Type: image/jpeg'); 
             array_push($files, $file);
             foreach($files as $fil){
              //header('Content-Type: image/jpeg');
              //<p><img src="http://localhost:8000/uploads/originals/menines.jpg" alt="" /></p>
              //echo $dir . $fil. "</br>"; 
              echo '<img src="/uploads/originals/' . $fil . '"/>' ;
              //echo '<img src="' $dir . $fil'" alt="some" />';
              //echo '<img src="' . $dir . $fil . '"/>' ;
             }
                	
             //echo file_get_contents($dir . $file);
            }
            
            //Content-Type: multipart/form-data; boundary=unique-string-that-is-hopefully-not-used-in-the-real-content-because-it-separates-the-content-pa
            //$data = file_get_contents($dir . $file);
            //echo $data;  
            //readfile($dir.$file);
            //echo base64_decode(readfile($dir.$file));    
            
        }
        
    }
//die();
    }
    public function delete(Request $request)
    {

        $manager = new EventManager();
         $manager->attach('database.post.deleted', function ($event) use ($manager) {
             @unlink($event->getTarget()->getImage());
             @unlink($event->getTarget()->getThumbToDelete());
         });
        $post = $this->postTable->find($request->getAttribute('id'));
        $manager->trigger(new OnDeleteEvent($post));
        $this->postTable->delete($request->getAttribute('id'));

        return $this->redirect('adminindex');
    }

    private function getParams(Request $request)
    {
        return array_filter($request->getParsedBody(), function ($key) {
            return in_array($key, ['name', 'slug', 'content']);
        }, ARRAY_FILTER_USE_KEY);
    }

    protected function getValidateur(Request $request, array $params)
    {
        $validator = (new Validator($params))
           ->required('content', 'name', 'slug', 'created_at')
           ->length('content', 10)
           ->length('name', 2, 250)
           ->length('slug', 2, 50)
           //->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
           ->dateTime('created_at')
           ->extension('image', ['jpg', 'png'])
           ->slug('slug');
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }




    protected function getValidator(Request $request, array $params)
    {
        $validator = (new Validator($params))
            ->required('content', 'name', 'slug', 'updated_at')
            ->length('content', 10)
            ->length('name', 2, 250)
            ->length('slug', 2, 50)
            //->exists('category_id', $this->categoryTable->getTable(), $this->categoryTable->getPdo())
            //->dateTime('created_at')
            ->extension('image', ['jpg', 'png'])
            ->slug('slug');
        if (is_null($request->getAttribute('id'))) {
            $validator->uploaded('image');
        }
        return $validator;
    }
}
