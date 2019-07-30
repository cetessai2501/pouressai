<?php
namespace App\Admin;
use App\Controller;
use App\Database\Database;
use App\Database\Table;
use App\Blog\Table\CategoriesTable;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\Response;
use App\Middleware\CsrfMiddleware;
use App\Upload;
use App\Auth\AuthService; 

class CrudController extends Controller 
{
    protected $view; 
    /**
     * @var Table
     */
    protected $table;

    protected $Ctable;

    public $auth;  
    /**
     * Vue / Route namespace.
     *
     * @var string
     */
    protected $namespace;
    /**
     * @var Upload|null
     */
    protected $uploader;
    /**
     * Champs qui sont des fichiers.
     *
     * @var array
     */
    protected $files = [];

    protected $format = []; 

    protected $formats = [
        'thumb' => [318, 180]
    ];
 
    public function __construct(ContainerInterface $container, Table $table, CategoriesTable $Ctable, Upload $uploader)
    {
        
        $this->container = $container;
        $this->table = $table;
        $this->Ctable = $Ctable;
        $this->view = $container->get('homeview');
        $this->auth = $container->get('auth.service');
    }
    public function index(ServerRequestInterface $request)
    {
//var_dump($request);
$name = isset($_GET['page']) ? $_GET['page'] : 1;
$page = (int)$name;


if(!filter_var($name, FILTER_VALIDATE_INT)){
throw new \Exception('pas valide');
}

        //$params = $request->getParsedBody();
        
        //$items = $this->table->findAlli();
$router = $this->container->get(\App\Routery::class);
$total = $this->table->pagination(6);
$items = $this->table->paginQuery(6, (int)($page), 6);

        $router = $this->container->get(\App\Routery::class); 
        $response = new Response(); 
        //var_dump($router);
        //die();    
        //$messages = $this->getFlash()->getMessages(); 
        $this->view->render($response, $this->namespace . 'adminindex', compact('items','total','page','router'));
        return $response;
    }
    public function preForm(ServerRequestInterface $request)
    {
        return [];
    }
    public function create(ServerRequestInterface $request)
    {

        if ($request->getMethod() === 'GET') {
                $response = new Response(); 
        //var_dump($router);
        $liste = $this->Ctable->findList('name');  
$tags = $this->table->findAllTagos(); 


        //$messages = $this->getFlash()->getMessages(); 
        $this->view->render($response, $this->namespace . 'admincreate', array_merge(
            compact('item', 'liste' ,'errors', 'tags'),
            $this->preForm($request)
        ));
        return $response;


        }
        if ($request->getMethod() === 'POST') {
            $item = $this->getParams($request);
$ramus = $request->getParsedBody();
$ramas = $request->getUploadedFiles()['image'][0];
            //$paramis = $request->getParsedBody();
unset($ramus['_csrf']);
unset($ramus['tag_name']);
$tagos = $request->getParsedBody()['tag_name'];
$ramois = array_merge($ramus, ['user_id' => $this->auth->user()->id ]);




            //$item['created_at'] = date('Y-m-d H:i:s');
            $errors = $this->validates($request, $this->table->getDatabase());
            if (empty($errors)) {

                
                $id = $this->table->insert($ramois);
                $itom = $this->table->find(intval($id));
                //$newimage = $itom->id.'-'.$request->getUploadedFiles()['image'][0];
                $image = $this->uploader->upload($request->getUploadedFiles()['image'][0], $itom);
                $this->table->update($id, ['image' => $itom->id.'-'.$request->getUploadedFiles()['image'][0]->getClientFilename()]); 
                $itom->setTags($tagos);
                $this->postPersist($request, $id);
                $res = $this->table->findTagsForPost($tagos);
                foreach($res as $re){

$int = intval($re->id);
$this->table->attachTags($id, $int);
}
                //$this->getFlash()->addMessage('success',"L'élément a bien été crée");
                //var_dump($route);
                //die();
                return $this->redirect('blogadminindex', []);
                
            }
        }
        
        
    }
    public function edit(ServerRequestInterface $request)
    {

       $id = $request->getAttribute('id');
   $idi = $request->getQueryParams('id');  
$tagis = $this->table->findAllTagsWithId(intval($id));

$bag = array();
foreach($tagis as $key => $row)
   {
    $bag[$key] = $row->tag_name;
    //$row->setTags($price);
   }

$tags = $this->table->findAllTagos();
       if ($request->getMethod() === 'GET') {
                $response = new Response(); 
        //var_dump($router);
        $item = $this->table->find(intval($id));
if(!empty($tagis)){
foreach($tagis as $ta){
$item->setTagName($ta->tag_name);

}
}
//var_dump($item);
        //$itam = $this->csrf_input(); 
        $liste = $this->Ctable->findList('name');   
        //$messages = $this->getFlash()->getMessages(); 
        $this->view->render($response, $this->namespace . 'adminedit', array_merge(
            compact('item', 'tagis', 'tags' , 'bag' ,'liste' ,'errors'),
            $this->preForm($request)
        ));
        return $response;


        }


        $item = $this->table->findOrFail(intval($id));

        if ($request->getMethod() === 'POST' && $request->getParsedBody()['_METHOD'] === 'PUT') {
            //$image = $this->getParamsImage($request);
            $item = $this->table->find(intval($id));


if($request->getUploadedFiles()['image'][0]->getSize() !== 0){
$paramis = array_merge($request->getParsedBody(), $request->getUploadedFiles());

if($item->image !== $paramis['image'][0]->getClientFilename()){
$ramus = $request->getParsedBody();
unset($ramus['_csrf']);
unset($ramus['_METHOD']);
unset($ramus['tag_name']);
$tagis = $this->table->findAllTagsWithId(intval($id));
$tagos = $request->getParsedBody()['tag_name'];
$newimage = $item->id.'-'.$paramis['image'][0]->getClientFilename();
$ramas = array_merge($ramus, ['image' => $newimage]); 
$res = $this->table->findTagsForPost($tagos);

}

$ramus = $request->getParsedBody();
unset($ramus['_csrf']);
unset($ramus['_METHOD']);
unset($ramus['tag_name']);
$tagis = $this->table->findAllTagsWithId(intval($id));
$tagos = $request->getParsedBody()['tag_name'];
$ramos = array_merge($ramus, ['image' => $paramis['image'][0]->getClientFilename()]);     
$res = $this->table->findTagsForPost($tagos);

$price = array();
}elseif($request->getUploadedFiles()['image'][0]->getSize() === 0 && isset($request->getParsedBody()['tag_name'])){// si pas d' upload d image
$paramis = $request->getParsedBody();
$ramus = array_merge($request->getParsedBody(),['image' => $item->image]);
unset($ramus['_csrf']);
unset($ramus['_METHOD']);
unset($ramus['tag_name']);

$tagis = $this->table->findAllTagsWithId(intval($id));
$tagos = $request->getParsedBody()['tag_name'];
$res = $this->table->findTagsForPost($tagos);


$ramos = $ramus;
}elseif(!isset($request->getParsedBody()['tag_name']) && $request->getUploadedFiles()['image'][0]->getSize() === 0){
$ramus = array_merge($request->getParsedBody(),['image' => $item->image]);
unset($ramus['_csrf']);
unset($ramus['_METHOD']);
$ramos = $ramus;
}

 
            //$pdf = $this->getParamsImage($request);
            //$pdfi = $itemEntity->image2;
            //var_dump($itemEntity->image2);
            //$parser = new \Smalot\PdfParser\Parser();
            //$pdf  = $parser->parseFile('/home/sophie/autre.fr/public/uploads/posts/'.$pdfi);
            //$text = $pdf->getText();
            //$item['content'] = $text;
            //die();
            //var_dump($item['content'] );
            //die();
            $errors = $this->validates($request, $this->table->getDatabase(), $id);
            if (false === true) {
             echo "failed check csrf";
             die();
             } else {
            if (empty($errors)) {
                /* @var UploadedFileInterface $file */
                if($request->getUploadedFiles()['image'][0]->getSize() !== 0){ 
                $image = $this->uploader->upload($paramis['image'][0], $item);
                  if($item->image !== $paramis['image'][0]->getClientFilename()){
                       $this->table->update($id, $ramas);
                    }
                }else{
                $this->table->update($id, $ramos);
                }
                $this->postPersist($request, $id);
if(isset($request->getParsedBody()['tag_name'])){
                // On met à jour la table
                $tagas = $this->table->findAllTagos();
                $resi = $this->table->findTagsForPost($tagos);
                
if(!empty($tagis)){// il y a 1 ou des tags deja enregistres 
foreach($tagis as $key => $row)
   {
    $price[$key] = $row->tag_name;
    
   }

if(in_array($res[0]->name, $tagos)){


//on detache ceux la
foreach($tagos as $k){

//$this->table->attachTagsByName($id, $k);//on attache les nouveaux (les tagos)
if(in_array($k, $price)){
//var_dump($v->tag_name);
//var_dump($tagis);


$tagname = $this->table->findTagByName(intval($id), $k)->name;
$tagid = $this->table->findTagByName(intval($id), $k)->id;
$this->table->detachTag(intval($id), intval($tagid));

//$this->table->attachTagsByName($id, $k);
//$this->table->detachTag(intval($id), $res[0]->id);
}else{
//die();
$this->table->attachTagsByName($id, $k);
}
//$this->table->attachTagsByName($id, $v->tag_name);
}
}else{
foreach($tagos as $k){
$this->table->attachTagsByName($id, $k);//on attache les nouveaux (les tagos)


}

//$this->table->attachTagsByName($id, $k);//on attache les nouveaux (les tagos)

}
//var_dump($tagos);
//var_dump(array_unique($this->flatten($tagas)));

}else{// il n y a pas de tags deja enregistres 
                $resi = $this->table->findTagsForPost($tagos);
                foreach($resi as $re){

$int = intval($re->id);
$this->table->attachTags($id, $int);
}

} 

}
                //$flasher = $this->container->get(Messages::class);

                //$this->flash('success', $this->getSuccessUpdateMessage());
                //$this->flash('success', $this->getSuccessUpdateMessage());
                //$response = new Response(); 

                //$this->getFlash()->addMessage('success',"L'élément a bien été modifié");

                //$response->flash('success', $this->getSuccessUpdateMessage());
                //return $response->withRedirect('/admin/blog');
                //return $this->redirect('blog.index');
                //return $this->redirect($this->namespace . '.admin.index');
                return $this->redirect($this->namespace . 'adminindex');
           
        }
      }
}


      if ($request->getMethod() === 'POST' && $request->getParsedBody()['_METHOD'] === 'DELETE') {
              $this->destroy($request);
              return $this->redirect('blogadminindex'); 
        }  


            $response = new Response(); 
            $this->view->render($response, $this->namespace . 'adminedit', array_merge(
            compact('item', 'liste' ,'errors'),
            $this->preForm($request)
        ));
         return $response;
    }

    public function csrf_input()
    {
        return '<input type="hidden" ' .
            'name="' . $this->csrfMiddleware->getFormKey() . '" ' .
            'value="' . $this->csrfMiddleware->generateToken() . '"/>';
    }



    public function flatten(array $array) {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
}




    public function destroy(ServerRequestInterface $request): ResponseInterface
    {
$id = $request->getAttribute('id');

$item = $this->table->findOrFail(intval($id));
@unlink($item->delImage());
@unlink($item->delThumb());
$this->table->delete($id);

return $this->redirect($this->namespace . 'adminindex');



        
    }

    private function getFilenameForFormat(string $name, string $format): string
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($name);
        return $filename . '_' . $format . '.' . $extension;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return array
     */
    
    /**
     * Valide les données.
     *
     * @param ServerRequestInterface $request
     * @param Database               $databasea
     * @param int|null               $id
     *
     * @return array
     */
    protected function validates(ServerRequestInterface $request, Database $databasea, ?int $id = null): array
    {
        return [];
    }
    protected function getSuccessCreateMessage()
    {
        return "L'élément a bien été modifié";
    }
    protected function getSuccessUpdateMessage()
    {
        return "L'élément a bien été modifié";
    }
    protected function getSuccessDeleteMessage()
    {
        return "L'élément a bien été supprimé";
    }
    protected function postPersist(ServerRequestInterface $request, int $id)
    {
    }
}
