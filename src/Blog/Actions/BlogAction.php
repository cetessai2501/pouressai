<?php
namespace App\Blog\Actions;

use App\Blog\Table\PostTable;
use Framework\Actions\RouterAwareAction;
use Psr\Container\ContainerInterface;
use App\Renderer\PHPRenderer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Routery;
use League\Plates\Engine;
use Framework\Cache;
use GuzzleHttp\Psr7\Response as Resp;
use Slim\Psr7\Response;
use App\MyApp;
use Framework\Auth;
use Framework\Events\EventManager;
use App\Blog\Actions\PDOEvent;
use GuzzleHttp\Psr7\Stream;
use App\Blog\PostEntity;
use App\Blog\TagEntity;
use App\Blog\CategoryEntity;
use Doctrine\ORM\Tools\Pagination\Paginator;
use App\Repository\PostsRepository;
use App\Validator;
use Slim\Flash\Messages;

class BlogAction 
{
    
    /**
     * @var RendererInterface
     */
    private $renderer;
   
    private $postTable;

    private $response;

    private $cache;

    private $pdo;

    private $container;

    public $auth;
    private $em;
    public $ruws = []; 
    private $app; 
    public $rows = [];
    protected $session;
    protected $flash;

    public function __construct(MyApp $app, PHPRenderer $renderer, ContainerInterface $container,PostTable $postTable)
    {
        
        $this->app = $app;
        $this->container = $container;
        $this->postTable = $postTable;
        $this->renderer = $this->container->get('homeview');
        $this->em = $container->get('em');
        $this->auth = $container->get('auth.service');
        $this->session = $container->get('session');
        $this->flash = $container->get('session.flash'); 
    }

       public function show2(Request $request, int $id): Response 
    {

     $route = $request->getAttribute('Framework\Router\Routy');
            


     try {
        $post = $this->postTable->find($id);
//$posti = $this->postTable->find($request->getAttribute('id'));
     } catch (\Exception $e) {
        //return (new Response())->withStatus(200)->withHeader('location', 'blog');
            $response = new Response(404, [], $e->getMessage());    
            return $response;  
            //return $this->redirect('blog');
     }

         return $this->renderer->render('show', [
            'post' => $post
        ]);

   } 

public function category(Request $request, Response $response): Response 
{
$params = $request->getQueryParams();
$cont = $this->app->getContainer();

$router = $cont->get(\App\Routery::class);
$posts = $this->postTable->findLatestCat(intval($request->getAttribute('id')));
$comments = $this->postTable->findLatestCatCom(intval($request->getAttribute('id')));


if(!empty($posts)){



//return $this->redirect('blogi', ['posts' => $posts,'router' => $router, 'page' => $page, 'total' => $total] );
return $this->renderer->render($response, 'categroup', ['posts' => $posts,'router' => $router, 'comments' => $comments]);


}else{
//$response = new Response();
die('no matched categorie');
}



}


        
         public function index(Request $request, Response $response): Response 
    {
        //return "helo";
$name = isset($_GET['page']) ? $_GET['page'] : 1;
$page = (int)$name;

$postes = $this->postTable->findess();

      



if(!filter_var($name, FILTER_VALIDATE_INT)){
throw new \Exception('pas valide');
}



$categs = $this->postTable->catego();

$cont = $this->app->getContainer();

$router = $cont->get(\App\Routery::class);

//var_dump($router->getCurrentRoute());
//var_dump($router->getMatchedRoutes($request->getMethod(),$request->getUri()));
//$posts = $this->postTable->findAll();
$total = $this->postTable->pagination(6);
$posts = $this->postTable->paginQuery(6, (int)($page), 6);
$tags = $this->postTable->findAllTags();
$plo = array();
$price = array();
        foreach($tags as $res => $v){
if($v['tag_name'] !== null){
$plo[(int)$v['id']][$res] = $v['tag_name'];
}else{

}
}


if(!empty($posts)){


foreach($posts as $post){
//var_dump($post->getTags());
if (array_key_exists($post->id, $plo)) {
    //var_dump($tags[$post->id]);


$post->setTags($plo[$post->id]);
}

}
//var_dump($posts);
//return $this->redirect('blogi', ['posts' => $posts,'router' => $router, 'page' => $page, 'total' => $total] );
return $this->renderer->render($response, 'blog', ['posts' => $posts,'router' => $router, 'page' => $page, 'total' => $total,'categs' => $categs]);


}else{
//$response = new Response();

}

    }
    
       public function homei(Request $request): Response 
    {
        //return "helo";
$router = $this->container->get('\Framework\Routery');
$user = $this->auth->getUser();

$route = $request->getAttribute('Framework\Router\Routy');

return $this->renderer->render('home', ['name' => 'Jonathan', 'router' => $this->router]);


    }
    

    public function redirect(string $path, array $params = [])
    {
$cont = $this->app->getContainer();
$router = $cont->get(\App\Routery::class);

 $redirectUri = $router->pathFor($path, $params, []);

return (new Response())
            ->withStatus(301)
            ->withHeader('location', $redirectUri);

        
    }

public function flatten(array $array)
    {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
    } 



    public function getTag(Request $request, Response $response) 
    {

     $route = $request->getAttribute('route');
$slug = $request->getAttribute('slug');

$tag = $request->getQueryParams('tag')['tag'];
$comments = $this->postTable->commentsByTag($tag);
$params = $request->getQueryParams();
 $cont = $this->app->getContainer();
$router = $cont->get(\App\Routery::class);

//var_dump($router->match($request));
$uri = $request->getUri()->getPath();



try {
        
        $posts = $this->postTable->searchByTag($tag);

//$posti = $this->postTable->find($request->getAttribute('id'));
     } catch (\Exception $e) {
        //return (new Response())->withStatus(200)->withHeader('location', 'blog');
            $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($e->getMessage());

        return (new Resp())
            ->withStatus(404)
            
            ->withBody($body);
            //return $this->redirect('blog');
     }

       $response = $this->renderer->render($response,'search', ['name' => 'Jonathan', 'router' => $router,  'posts' => $posts, 'comments' => $comments]);
return $response; 
    
//$posti = $this->postTable->find($request->getAttribute('id'));
     
    


    }

    public function getChildrenIds($comment)
{
        $ids = [];  
      
        foreach($comment->children as $child){
              $ids[] = intval($child->id);
              if(isset($child->children)){
                  $ids = array_merge($ids, $this->getChildrenIds($child));
                  
              } 
        }
        return $ids; 
    

}

   
    public function show(Request $request)
    {

$route = $request->getAttribute('route');
$cont = $this->app->getContainer();
$router = $cont->get(\App\Routery::class);
$slug = $request->getAttribute('slug');
$user = $this->auth->user();
//$this->rows = $this->postTable->findByTags();

//var_dump($router->match($request));
//var_dump($router->relativeUrlFor('showi', ['slug' => $slug ]));
        $response = new Resp();

//var_dump($slug);
try {


$coms = $this->postTable->threaded($request->getAttribute('slug'));

        $post = $this->postTable->findSlug($request->getAttribute('slug'));

$comments = $this->postTable->findComments($request->getAttribute('slug'));
//var_dump($comments[4]->children[0]->children[0]->children);
//die();
$commentas = $this->postTable->findCommentas($request->getAttribute('slug'));

$commentos = $this->postTable->findCommentsChildren(intval($post->id));
$data = $this->postTable->findParentIdColumComJson();
//$test = $this->postTable->comments_by_id;
//$this->getChildrenIds
$test = array();
foreach($commentos as $com){

//$ids = $this->getChildrenIds($this->postTable->comments_by_id[intval($com->id)]);
//$ids[] = intval($com->id);

//array_push($test, array_flip($ids));

}

$commentis = $this->postTable->comments($request->getAttribute('slug'));
$tagis = $this->postTable->findAllTagsWithIdArray($post->id);
//$newarra = $this->flatten($test);

if(!empty($tagis)){
$price = array();

foreach($tagis as $tag){
$price[intval($tag['id'])][] = $tag['tag_name'];

}
$tags = $this->postTable->findBySlug($request->getAttribute('slug'), $price[$post->id]);
//var_dump($price[$post->id]);
$post->setTags($tags);
}




$response = $this->renderer->render($response, 'blogshow', ['post' => $post, 'router' => $router, 'tags' => $tags, 'comments' => $comments, 'user' => $user, 'commentis' => $commentis, 'commentas' => $commentas, 'data' => $data ]);
        return $response;
//$posti = $this->postTable->find($request->getAttribute('id'));
     } catch (\Exception $e) {
        //return (new Response())->withStatus(200)->withHeader('location', 'blog');
        $body = new Stream(fopen('php://temp', 'r+'));
        $body->write($e->getMessage());

        return (new Resp())
            ->withStatus(404)
            
            ->withBody($body);    

        
              
            //return $this->redirect('blog');
     }


        
       


    
    }

public function setComments(Request $request, Response $resp)
    {
if ($request->getMethod() === 'POST' && $this->auth->user() === null) {
$ramus = $request->getParsedBody();

$ramus['parent_id'] = intval($ramus['parent_id']);

$post = $this->postTable->findSlug($request->getAttribute('slug'));


unset($ramus['_csrf']);
$ramois = array_merge($ramus, [ 'post_id' => $post->getId() ]);
$errors = [];
//$this->validates($request, $this->postTable->getDatabase());

if (empty($errors)) {
$messag = $this->flash->success("ok succeeded");
$id = $this->postTable->insertComments($ramois);
return $this->redirect('showi', ['slug' => $post->slug ]);
}else{
$newerrors = array();
foreach($errors as $k => $error){
$newerrors[$k] = "Erreur, pour le champ " .$k.', '.$error; 
//var_dump($k);
//var_dump($error);
}
$messag = $this->flash->error("pb failed");
$autre = $this->flash->details("details", $newerrors);
return $this->redirect('showi', ['slug' => $post->slug ]);
}


}else{
$ramus = $request->getParsedBody();

$ramus['parent_id'] = intval($ramus['parent_id']);
$ramus['email'] = $this->auth->user()->email;
$ramus['pseudo'] = $this->auth->user()->username;
$post = $this->postTable->findSlug($request->getAttribute('slug'));


unset($ramus['_csrf']);
$ramois = array_merge($ramus, ['user_id' => intval($this->auth->user()->id), 'post_id' => $post->getId() ]);

$id = $this->postTable->insertComments($ramois);
return $this->redirect('showi', ['slug' => $post->slug ]);

}


}

public function deleteComments(Request $request)
{
//si une reply se trouve au milieu de plusieurs autres je veux detruire aussi celle du dessous donc son child
// si un comment est un comment racine je veux destroy aussi toutes les replyes 
$id = intval($request->getAttribute('id'));
$comment = $this->postTable->commentsId($id);
var_dump($comment);
die();
$user = $this->auth->user();

$comment = $this->postTable->commentsId($id);

$coms = $this->postTable->findCommentsChildren(intval($comment->post_id));
$cams = $this->postTable->findCommentsChildrenCom(intval($comment->parent_id));
$post = $this->postTable->find(intval($comment->post_id));
$commentos = $this->postTable->findCommentas($post->slug);
//$ids = $this->getChildrenIds($this->postTable->comments_by_id[intval($comment->id)]);
$array = array();
$bisarray = array();
foreach($commentos as $os){

if($os['parent_id'] != '0'){
$children[intval($os['parent_id'])][] = $os['id'];

}else{
$childrens[intval($os['id'])][] = $os['id'];
}

}
foreach($coms as $com){

if(isset($com->children)){
$ids = $this->getChildrenIds($com);
//var_dump($com->children);

}
$ids[] = intval($com->id);

array_push($array, $ids);
array_push($bisarray, $ids);
$ti[intval($com->id)] = $ids;
$toi[intval($com->parent_id)] = $ids;
}

$newarr = $this->flatten($bisarray);
//var_dump(count($newarr) < 2);
$flip = array_flip($newarr);
//var_dump($ti);

//var_dump($newarr);
//var_dump($ti);
//var_dump(in_array(intval($comment->parent_id), $newarr));
//var_dump(array_slice($ti, 0, $key - 1, true));
//var_dump(array_slice($ti, 1, $key, true));
//var_dump($comment->id);
if(isset($children)){
$newchilds = $this->flatten($children);
}
if(isset($childrens)){
$newchilds = $this->flatten($childrens);
}
$to = $this->recursive_array_search(intval($comment->id),$ti );
$data = $this->postTable->findParentIdColumCom();
$ta = $this->recursive_array_search(intval($comment->parent_id),$ti );
$dat = $this->postTable->findGroupCom();


//var_dump(in_array($id , $newarr));
//var_dump(in_array(intval($comment->parent_id) , $newarr));
//var_dump(in_array(intval($comment->id), array_keys($ti)));

//var_dump(in_array(intval($comment->id), $ti));
//var_dump(array_slice($ti, 0 ,$to[1] - 1 , true));
//var_dump(array_slice($ti, 0, true));
$racine = $to[0];
$childs = $ti[$to[0]];
$parent = intval($comment->parent_id);
//var_dump($childs);
//var_dump($racine);
//var_dump($parent);
//id

//104  racine
//110  racine
//115  racine
//116  parent 115 enfant direct 117 + racine parent
//117  parent 116 enfant direct 118 + racine parent
//118  parent 117 pas d enfant + racine parent
//

//var_dump($children);
//var_dump(array_keys($children));
//var_dump(in_array($comment->id, $newchilds));                 //if false com racine else if true a des parents donc is a reply;
//var_dump($children);
if(in_array($comment->id, $newchilds) === true && in_array($comment->id, $data) === true){
//$cho =  $children[$parent][0];
//var_dump(array_slice($childs, 0, true));
$flipo = array_flip($childs);

//unset($flipo[$parent]);
unset($flipo[intval($comment->id)]);
//var_dump(array_flip($flipo)[1]);
if(isset(array_flip($flipo)[1])){//profondeur inf a 2
$truechild = array_flip($flipo)[1];


}

if(isset(array_flip($flipo)[0]) && isset(array_flip($flipo)[0 + 2])){//profondeur superieure a 2
unset($flipo[$racine]);

$truechild = array_flip($flipo)[0 + 2];

//var_dump(array_flip($flipo)[0]);
}
//var_dump(in_array($id , $newarr));
}
//var_dump(intval($comment->user_id));
//var_dump(intval($user->id));
//var_dump($comment->id);
//var_dump($comment->parent_id);


if(isset($flipo) && isset(array_flip($flipo)[0]) && !isset(array_flip($flipo)[1]) && !isset(array_flip($flipo)[2])){//profondeur inf a 2
$truechildi = array_flip($flipo)[0];


}

if($comment->user_id !== null && intval($user->id) === intval($comment->user_id)){
$cuserid = $user->id;
$pdo = $this->container->get(\PDO::class);
if(count($newarr) < 2 === false && in_array($id , $newarr) && in_array($comment->id, $newchilds) && isset($flipo)){
$tu = $ti[intval($comment->id)];

$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $tu) . "') AND user_id = ?")->execute([$cuserid]);
}elseif(count($newarr) < 2 === false && in_array($id , $newarr) && in_array($comment->id, $newchilds) && isset($flipo) && isset($truechild) && $truechild !== null){

//$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $newarr) . "') AND user_id = ?")->execute([$cuserid]);
//$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
//die();


$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$truechild, $cuserid]);
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);

}elseif(isset($ti[intval($comment->id)]) && $comment->parent_id == '0' && isset($children)){
$tu = $ti[intval($comment->id)];

$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $tu) . "') AND user_id = ?")->execute([$cuserid]);

}elseif(isset($ti[intval($comment->id)]) && $comment->parent_id == '0' && isset($childrens)){


//$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $tu) . "') AND user_id = ?")->execute([$cuserid]);
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
//$pdo->prepare('UPDATE comments SET parent_id = ? WHERE parent_id = ? AND user_id = ?')->execute([intval($comment->parent_id), intval($comment->id), $cuserid]);


}elseif(in_array($comment->id, $data) === false){
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
}else{
$racine = $to[0];
$childs = $ti[$to[0]];
$parent = intval($comment->parent_id);

$flipo = array_flip($childs);
unset($flipo[intval($comment->id)]);
//var_dump(array_flip($flipo)[1]);
if(isset(array_flip($flipo)[1])){//profondeur inf a 2
$truechild = array_flip($flipo)[1];


}
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$truechild, $cuserid]);
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
}

}elseif($comment->user_id !== null && intval($user->id) !== intval($comment->user_id)){//var_dump($comment->user_id);
$cuserid = intval($comment->user_id);

$pdo = $this->container->get(\PDO::class);
if(count($newarr) < 2 === false && in_array($id , $newarr) && in_array($comment->id, $newchilds) && isset($flipo) && $truechild !== null){
//$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $newarr) . "') AND user_id = ?")->execute([$cuserid]);
//$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
//die();


$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$truechild, $cuserid]);
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);

}elseif(isset($ti[intval($comment->id)]) && $comment->parent_id == '0' && isset($children)){
$tu = $ti[intval($comment->id)];
$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $tu) . "') AND user_id = ?")->execute([$cuserid]);

}elseif(isset($ti[intval($comment->id)]) && $comment->parent_id == '0' && isset($childrens)){


//$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $tu) . "') AND user_id = ?")->execute([$cuserid]);
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
//$pdo->prepare('UPDATE comments SET parent_id = ? WHERE parent_id = ? AND user_id = ?')->execute([intval($comment->parent_id), intval($comment->id), $cuserid]);


}elseif(in_array($comment->id, $data) === false){
$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?')->execute([$id, $cuserid]);
}else{
die('p');
}



}else{////////grand elseif

$pdo = $this->container->get(\PDO::class);
if(count($newarr) < 2 === false && in_array($id , $newarr) && in_array($comment->id, $newchilds) === true){
//$pdo->prepare("DELETE FROM comments WHERE id IN ('" . implode("','", $newarr) . "') AND user_id IS NULL");
var_dump($comment->user_id);
}else{
//$pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id IS NULL')->execute([$id]);
//$pdo->prepare('UPDATE comments SET parent_id = ? WHERE parent_id = ? AND user_id IS NULL')->execute([intval($comment->parent_id), intval($comment->id)]);
}
}







return $this->redirect('showi', ['slug' => $post->slug ]);
}



    private function serialize(): array
    {
        return array_map(function (TagEntity $row) {
            return [
                'id'       => $row->getId(),
                'tag_name' => $row->getTagName()
            ];
        }, $this->rows);
    } 


private function validates(Request $request, \App\Database\Database $database, $id = null): array
    {
        $params = $request->getParsedBody();
        return (new Validator($params))
            ->setDatabase($database)
            ->required('content')
            ->required('pseudo')
            ->required('email')
            ->email('email')
            ->minLength('content', 40)
            ->maxLength('content', 500)
            ->getErrors();
    }



public function recursive_array_search($needle, $haystack, $currentKey = "") {
    foreach($haystack as $key=>$value) {
        if (is_array($value)) {
            $nextKey = $this->recursive_array_search($needle,$value, $currentKey .  $key );
            //echo $nextKey; 
            if ($nextKey) {
                return $nextKey;
            }
        }
        else if($value==$needle) {
            //echo $key;  
            //echo $currentKey;
            return array(intval($currentKey), $key);   
            //return is_numeric($key) ? $currentKey . $key : $currentKey;
        }
    }
    return false;
}














}
