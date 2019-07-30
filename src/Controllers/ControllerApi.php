<?php
namespace App\Controllers;

use App\Blog\Table\CategoriesTable;
use App\Blog\Table\PostTable;
use App\Auth\Table\UserTable;
use App\Blog\Table\MessageTable;
use App\Controller;
use \Projek\Slim\Plates;
use \Projek\Slim\PlatesExtension;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Database\Database;
use App\Database\Table;
use PDO;
use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;
use App\Auth\AuthService;
use App\Session\Session;
use App\Repository\ConversationRepository;
use App\Auth\Entity\User;
use Slim\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use App\Blog\PostEntity;
use Psr\Container\ContainerInterface;
use DateTime;
use DateTimeZone;

class ControllerApi extends Controller
{
    private $pdo;

    public $auth;

    private $em;

    protected $container;

    public $session;

    public function __construct(Database $pdo, AuthService $auth, Session $session, ContainerInterface $container)
    {
      $this->pdo = $pdo; 
      $this->auth = $auth;
      $this->session = $session;
      $this->container = $container;
      $this->em = $container->get('em'); 
    }

    public function page(RequestInterface $request, Response $response):Response
    {
$name = (isset($_GET['po']) && $_GET['po'] > 0) ? $_GET['po'] : 1;
$page = (int)$name;
$res = array("page1" => 1, "page2" => 2);
    
return $response->withJson($res, null , JSON_NUMERIC_CHECK);

    }  


    public function index(RequestInterface $request, Response $response):Response
    {
        
       $id = intval($request->getAttribute('id')); 
        $sq = "SELECT 
              posts.*, 
              categories.name as category_name, categories.slug as category_slug, DATETIME(posts.created_at, 'localtime') as time
              FROM posts LEFT JOIN categories ON categories.id = posts.category_id WHERE posts.id = ?";  
        
        $st = $this->pdo->query($sq);
        $st->execute([$id]); 
        //$result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        //$st->setFetchMode(\PDO::FETCH_CLASS, PostEntity::class);
        

$res = $st->fetch(\PDO::FETCH_ASSOC);






return $response->withJson($res, null , JSON_NUMERIC_CHECK);
 
 

    }

/// vue js ///////////////////////////////////////////////// add comment
    public function commentsAdd(RequestInterface $request, Response $response):Response
    {

$params = $request->getParsedBody();
unset($params['_csrf']);


$query = implode(', ', array_map(function ($field) {
            return "'$field'";
        }, $params));

        $fields = array_keys($params);
        

        $st = $this->pdo->query("INSERT INTO comments (" .
            join(',', $fields) .
            ") VALUES (" . $query . 
            ")");
    

$idi =  $this->pdo->lastInsertId();
 $req2 = $this->pdo->query('SELECT * FROM comments WHERE id = ?');
$req2->execute([$idi]);
$tabi = $req2->fetch(\PDO::FETCH_ASSOC);

return $response->withJson($tabi, null , JSON_NUMERIC_CHECK)->withHeader('X-Requested-With', 'XMLHttpRequest');


    }







    public function commentsEdit(RequestInterface $request, Response $response):Response
    {

$ramus = $request->getParsedBody();
$id = intval($ramus['element_id']);

$content = $ramus['contenti'];

$req2 = $this->pdo->query('UPDATE comments SET content = ? WHERE id = ?');
$req2->execute([ $content, $id]);
$response = array("data" => $id, 'data1' => $content);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode($response));
return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody($body);


    }




    /// blogshow.php
    public function commentsSlug(RequestInterface $request, Response $response):Response
    {

$slug = $request->getAttribute('slug');
$sq = "SELECT DISTINCT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?
ORDER BY comment_time DESC

";
$st = $this->pdo->query($sq);
$st->execute([$slug]);
$comments = [];

$records = $st->fetchAll();


if(!empty($records)){
$comments_by_id = [];
foreach ($records as $comment){
$comments_by_id[intval($comment->id)] = $comment;
}
foreach ($records as $k => $com){
if($com->parent_id){
$comments_by_id[intval($com->parent_id)]->children[] = $com;
unset($comments[$k]);
}

}
return $response->withJson($records, null ,JSON_NUMERIC_CHECK);

}




$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([]));
return (new Response())
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody($body);





}
/// vue js ///////////////////////////////////////////////// delete comment
public function commentsById(RequestInterface $request, Response $response):Response
{
$ramus = $request->getParsedBody();

$id = intval($ramus['parentid']);
$reso = $this->pdo->query("SELECT * FROM comments
WHERE comments.id = ?
");

$reso->execute([$id]);
$comment = $reso->fetch();

if(isset($comment->parent_id) && isset($comment->id)){//enfts et parent_id !== 0
$req2 = $this->pdo->query('UPDATE comments SET parent_id = ? WHERE parent_id = ?');
$req2->execute([intval($comment->parent_id), intval($comment->id)]);
$req = $this->pdo->query('DELETE FROM comments WHERE id = ?');
$req->execute([$id]);
$resa = $this->pdo->query("SELECT * FROM comments
WHERE comments.id = ?
");
$resa->execute([intval($comment->parent_id)]);
$parent = $resa->fetch();
}
$req = $this->pdo->query('DELETE FROM comments WHERE id = ?');
$req->execute([$id]);

$response = array("id" => $id, "parent" => $parent);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response]));
return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody($body);







}

    public function redirect(string $path, array $params = [])
    {
        $router = $this->container->get(\App\Routery::class);

 $redirectUri = $router->relativePathFor($path, $params, []);

return (new Response())
            ->withStatus(301)
            ->withHeader('location', $redirectUri);

        
    }




    public function comments(RequestInterface $request, Response $response):Response
    {
//$slug = $request->getAttribute('slug');

$sq = "SELECT DISTINCT comments.*,
posts.id as pid, posts.slug as pslug, posts.name as pname, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
GROUP BY posts.id
ORDER BY comment_time DESC
LIMIT 3
";
$st = $this->pdo->query($sq);
//$st->execute([$slug]);
$comments = $st->fetchAll(\PDO::FETCH_ASSOC);

if(!empty($comments)){
foreach($comments as $com){
$children[intval($com['parent_id'])][] = $com;
} 
return $response->withJson($comments, null ,JSON_NUMERIC_CHECK); 


}
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([]));
return (new Response())
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody($body); 


    }


public function fetchiSlug(RequestInterface $request, Response $response, $args):Response
    {
$usera = isset($this->auth->user()->id) ? $this->auth->user()->id : 0;
$name = (isset($_GET['page']) && $_GET['page'] > 0) ? $_GET['page'] : 1;
$page = (int)$name;
$limite = isset($_GET['limite']) ? $_GET['limite'] : 6;
 $pageo     = (int)$args['page'];

//$token = isset($_GET[$this->session['_csrf']]);
$slug = $request->getAttribute('slug');
$limit = intval($request->getAttribute('limit'));
$offset = intval($request->getAttribute('offset'));
//$offsat = (--$pageo) * $limit;

$resz = $this->pdo->query('SELECT count(comments.id) as counta, posts.id as pid, posts.slug as pslug FROM comments INNER JOIN posts ON pid = comments.post_id WHERE pslug = ?');
$resz->execute([$slug]);
$counti = $resz->fetch(); 
$offsit = (--$name) * $limit;

$counto = (int)$counti->counta; 
$offsat = ($pageo -1 ) * $limit;  

//var_dump($res->fetch());
//$count = (int)$this->pdo->query('SELECT count(id) FROM posts')->fetch(\PDO::FETCH_NUM)[0];
$pagesnumber = ceil($counto / $limit);




$session = $this->session->getSession();
$rand_keys = array_rand($session['csrf.tokens'], 1);
$tokenval = $session['csrf.tokens'][$rand_keys];

$sq = "SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?  
ORDER BY comment_time DESC

LIMIT $offsat, $limit
";


$st = $this->pdo->query($sq);
$st->execute([$slug]);
$comments = [];

$records = $st->fetchAll();
$timezone  = -2; 

if(!empty($records)){
$comments_by_id = [];
foreach ($records as $comment){
$paris = new DateTimeZone('Europe/Paris');
$gmt = new DateTimeZone('GMT');
$comment->essai = new DateTime($comment->comment_time);

$comments_by_id[intval($comment->id)] = $comment;
}
foreach ($records as $k => $com){
if($com->parent_id){
$paris = new DateTimeZone('Europe/Paris');
$gmt = new DateTimeZone('GMT');
$com->essai = new DateTime($com->comment_time);

$comments_by_id[intval($com->parent_id)]->children[] = $com;
unset($comments[$k]);
}

}

$response = array("user" => intval($usera), "comments" => $records, "count" => count($records), "counto" => $counto, "offsat" => $offsat, "pageo" => $pageo, "limit" => $limit);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response],JSON_NUMERIC_CHECK ));

return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withBody($body);

}
$st = $this->pdo->query("SELECT * FROM posts WHERE posts.slug = ?");
$st->execute([$slug]);
$post = $st->fetch();
$response = array("user" => intval($usera), "post" => $post);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response]));
return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withBody($body);



    }
    /// vue js /////////////////////////////////////////////////
    public function commentisBySlug(RequestInterface $request, Response $response):Response
    {



$usera = isset($this->auth->user()->id) ? $this->auth->user()->id : 0; 
//$token = isset($_GET[$this->session['_csrf']]);
$session = $this->session->getSession();
$slug = $request->getAttribute('slug');
$rand_keys = array_rand($session['csrf.tokens'], 1);
$tokenval = $session['csrf.tokens'][$rand_keys];



$sq = "SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?  
ORDER BY comment_time DESC

";


$st = $this->pdo->query($sq);
$st->execute([$slug]);
$comments = [];

$records = $st->fetchAll();
$timezone  = -2; 

if(!empty($records)){
$comments_by_id = [];
foreach ($records as $comment){
$paris = new DateTimeZone('Europe/Paris');
$gmt = new DateTimeZone('GMT');
$comment->essai = new DateTime($comment->comment_time);

$comments_by_id[intval($comment->id)] = $comment;
}
foreach ($records as $k => $com){
if($com->parent_id){
$paris = new DateTimeZone('Europe/Paris');
$gmt = new DateTimeZone('GMT');
$com->essai = new DateTime($com->comment_time);

$comments_by_id[intval($com->parent_id)]->children[] = $com;
unset($comments[$k]);
}

}

$response = array("user" => intval($usera), "comments" => $records, "count" => count($records));
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response],JSON_NUMERIC_CHECK ));

return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withBody($body);

}
$st = $this->pdo->query("SELECT * FROM posts WHERE posts.slug = ?");
$st->execute([$slug]);
$post = $st->fetch();
$response = array("user" => intval($usera), "post" => $post);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response]));
return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withBody($body);






}



    public function commentis(RequestInterface $request, Response $response):Response
    {

$sq = "SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE comments.post_id = posts.id
ORDER BY comment_time DESC
";


$st = $this->pdo->query($sq);
$comments = $st->fetchAll();

if(!empty($comments)){
$comments_by_id = [];
foreach ($comments as $comment){
$comments_by_id[intval($comment->id)] = $comment;
}
foreach ($comments as $k => $com){
if(intval($com->parent_id) !== 0 ){
$comments_by_id[intval($com->parent_id)]->children[] = $com;
unset($comments[$k]);
} 

}
$this->comments_by_id = $comments_by_id;
return $response->withJson($comments, null ,JSON_NUMERIC_CHECK); 
}


$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([]));
return (new Response())
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withBody($body);





}










    public function fetcholl(RequestInterface $request, Response $response):Response
    {

$slug = $request->getAttribute('slug');
$resz = $this->pdo->query('SELECT count(comments.id) as counta, posts.id as pid, posts.slug as pslug FROM comments INNER JOIN posts ON pid = comments.post_id WHERE pslug = ?');
$resz->execute([$slug]);
$counti = $resz->fetch(); 
$usera = isset($this->auth->user()->id) ? $this->auth->user()->id : 0; 

$counto = (int)$counti->counta; 

$name = (isset($_GET['p']) && $_GET['p'] > 0) ? $_GET['p'] : 1;
$page = (int)$name;    
$lim = (isset($_GET['limit']) && $_GET['limit'] > 0) ? $_GET['limit'] : $counto;
$limite = (int)$lim;  
$off = (isset($_GET['offset']) && $_GET['offset'] > 0) ? $_GET['offset'] : 0;
$pagesnumber = ceil($counto / $limite);
$offset = (int)$off; 

$offsat = ($page -1 ) * $limite; 


$sq = "SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ? 
GROUP BY comments.id 
ORDER BY comment_time DESC

LIMIT $offsat, $limite
";


$st = $this->pdo->query($sq);
$st->execute([$slug]);
$comments = [];

$records = $st->fetchAll();
$timezone  = -2; 

if(!empty($records)){
$comments_by_id = [];
foreach ($records as $comment){
$paris = new DateTimeZone('Europe/Paris');
$gmt = new DateTimeZone('GMT');
$comment->essai = new DateTime($comment->comment_time);

$comments_by_id[intval($comment->id)] = $comment;
}
foreach ($records as $k => $com){
if($com->parent_id){
$paris = new DateTimeZone('Europe/Paris');
$gmt = new DateTimeZone('GMT');
$com->essai = new DateTime($com->comment_time);

$comments_by_id[intval($com->parent_id)]->children[] = $com;
unset($comments[$k]);
}

}
$response = array("user" => intval($usera), "comments" => $records, "count" => count($records), "counto" => $counto, "offsat" => $offsat, "page" => $page, "nb" => $pagesnumber);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response],JSON_NUMERIC_CHECK ));

return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withBody($body);

}
$st = $this->pdo->query("SELECT * FROM posts WHERE posts.slug = ?");
$st->execute([$slug]);
$post = $st->fetch();
$response = array("user" => intval($usera), "post" => $post);
$body = new Stream(fopen('php://temp', 'r+'));
$body->write(json_encode([$response]));
return (new Response())
            ->withStatus(201)
            ->withHeader('Content-Type', 'application/json;charset=utf-8')
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->withBody($body);

    }




      public function convs($user, Request $request, UserTable $userTable, ConversationRepository $cr, MessageTable $messTable,Response $response)
    {
        $item = $userTable->findOrFail($user);
        //var_dump($item);
        //die();
        //$user->username;
        //$user->id;
        if ($item) {
             //$_SERVER['PHP_AUTH_USER'] = [$user->id, $user->username];
             //$conversations = $cr->getConversations();
             return $response->withJson($item);
             
        } 
        //$f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
        //return;  
    }

     public function users(Request $request, UserTable $userTable, MessageTable $messTable,Response $response)
    {
        $a= new \PDO("mysql:host=localhost;dbname=puf;","root","froggy25");
        $a->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);  
        //$page = $request->getParam('page', 1);
       
        $sql = 'SELECT id, username FROM users';
        //$sq = 'SELECT * FROM posts';  
        $stmt = $a->prepare($sql);
        $result = $stmt->setFetchMode(\PDO::FETCH_ASSOC);
        $stmt->execute();
        $users = $stmt->fetchAll(); 
        $messages = [];
        foreach($users as $user){
         $messages = $messTable->messages($user['id'], $user['id']);
         //echo json_encode($messages);   
         $fruits[] = $user;
         $pom = array(array_merge($fruits, $messages));
         return $response->withJson($messages);
        }
        $messages;       
        //die();
        //$pom = array(array_merge($fruits, $messages));
        //$f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
        
        //die();
        //var_dump($messages);
        //die();
        //echo json_encode($pom);
        //return; 
        //$result = array_merge($id, $username);
        //var_dump($result['id']);
        //foreach($id as $d)
        //echo $d;
        //foreach($username as $u)
        //echo $u;   
        //die();
        
        //echo json_encode($result);
        //exit; 

    }
  
     public function newconvs(Request $request, AuthService $auth, SessionInterface $session, ConversationRepository $cr,Response $response)
    {
        //$a= new \PDO("mysql:host=localhost;dbname=puf;","root","froggy25");
        //$a->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);  
        //$user = $session->get('auth.infos');
        //$user = $this->user;
        
        $params = $request->getParsedBody();
        //$_SERVER['QUERY_STRING']; 
        $userId = $request->getParam('userId');
        $token = $request->getParam('token');
        $username = $request->getParam('username');
        $user = $auth->logToken($userId, $token, $username);
        //$password = $request->getParam('password');
        //$params = json_decode($request->getBody()); 
        //$redirect = $session->get('auth.redirect') ?: '/';
        //$message = $_SESSION['slimFlash'];
        if ($user) {
             $_SERVER['PHP_AUTH_USER'] = [$user->id, $user->username];
             $conversations = $cr->getConversations();
             $unread = $cr->unreadCount($userId);
        
        }
        foreach($conversations as $conversation) {
           $messages = $cr->getMessagesFor($userId, $conversation->id);
           
             //$count = null;
        } 
        $f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
        return $response->withJson(array('userId' => $userId, 'conversations' => $conversations, 'messages'=> array_reverse($messages)));
        //return $response->withJson(array('userId' => $userId, 'conversations' => $conversations));
     }




     public function conversations(Request $request, AuthService $auth, SessionInterface $session, ConversationRepository $cr,Response $response)
    {
        //$a= new \PDO("mysql:host=localhost;dbname=puf;","root","froggy25");
        //$a->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);  
        //$user = $session->get('auth.infos');
        //$user = $this->user;
        
        $params = $request->getParsedBody();
        //$_SERVER['QUERY_STRING']; 
        $userId = $request->getParam('userid');
        $token = $request->getParam('token');
        $username = $request->getParam('username');
        $user = $auth->logToken($userId, $token, $username);
        //$password = $request->getParam('password');
        //$params = json_decode($request->getBody()); 
        //$redirect = $session->get('auth.redirect') ?: '/';
        //$message = $_SESSION['slimFlash'];
        if ($user) {
             $_SERVER['PHP_AUTH_USER'] = [$user->id, $user->username];
             $conversations = $cr->getConversations();
             $unread = $cr->unreadCount($userId);
        
        } 
        
        $f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
        //return $response->withJson($request->getParam('userId'));
        return $response->withJson(array('userId' => $userId, 'conversations' => $conversations));
        //die();
        //return ;
        //echo json_encode($_SESSION);
        //$f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
        //return $response->withJson($_SESSION);
        //$messages = [];
        //foreach($users as $user){
         //$messages = $messTable->messages($user['id'], $user['id']);
            //return $response->withJson($user); 
         //$fruits[] = $user;
         //$pom = array(array_merge($fruits, $messages));
         //return $response->withJson($messages);
        //}
          
        //die();
        //$pom = array(array_merge($fruits, $messages));
        //$f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
        
        //die();
        //var_dump($messages);
        //die();
        //echo json_encode($pom);
        //return; 
        //$result = array_merge($id, $username);
        //var_dump($result['id']);
        //foreach($id as $d)
        //echo $d;
        //foreach($username as $u)
        //echo $u;   
        //die();
        
        //echo json_encode($result);
        //exit; 

    }



    public function category($slug, Request $request, PostTable $postTable, CategoriesTable $categoriesTable)
    {
        $category = $categoriesTable->findBySlug($slug);
        if (empty($category)) {
            throw new NoRecordException();
        }
        $page = $request->getParam('page', 1);
        $posts = $postTable->findPaginatedByCategory(12, $page, $slug);
        $categories = $categoriesTable->findall();
$this->render('blogcategory', array('category' => $category, 'posts' => $posts, 'page' => $page, 'categories' => $categories));
    }
    public function show($slug, PostTable $postTable, Plates $view)
    {
        $post = $postTable->findBySlug($slug);
        return $view->render('blogshow', array('post' => $post));
    }

     public function update($id, Request $request,PostTable $postTable)
    {
             //$params = $request->getParsedBody();
          
             $item = $postTable->findOrFail($id);
             $id = $item->id;
             $content = $request->getParam('myNode'); 
             //$postTable->updat($id, $content);
             //die();
             //$id = $item->id;
             //$content = $request->getParam('myNode');
             //$content = $request->getParam('main-content');
             //$var="User', email='test";
             //return;
             $a= new \PDO("mysql:host=localhost;dbname=puf;","root","froggy25");
             $b=$a->prepare("UPDATE `posts` SET content=:content WHERE id=:id");
             $b->bindParam(":content",$content);
             $b->bindParam(":id",$id);
             $b->execute();   
             return;
             //$sql = "UPDATE posts SET content = '".$content."' WHERE id = '".$id."'"; 
             //$stmt = $this->pdo->query($sql);
             //$stmt->setFetchMode(\PDO::FETCH_ASSOC);
             
        //var_dump($help->textarea($fieldname,$name));
    } 

     public function before(Request $request,UserTable $userTable, MessageTable $messTable,Response $response)
    {
             
             $today = date("Y-m-d H:i:s");
             $pom = $request->getAttribute('routeInfo');
             foreach($pom as $po){
                if(isset($po['user'])){
                   if ($request->getParam('before')) {
                   $messagesQuery = $messTable->where($today, intval($po['user']));
                   return $response->withJson(array('user' => intval($po['user']), 'messages' => $messagesQuery)); 
                   //die();
                   }   

                   
                } 
             } 
             foreach($po as $p){
               //var_dump($p);
             }
             //die();  
             //$id = $item->id;
             //$content = $request->getParam('myNode'); 
             //$postTable->updat($id, $content);
             //die();
             //$id = $item->id;
             //$content = $request->getParam('myNode');
             //$content = $request->getParam('main-content');
             //$var="User', email='test";
             $f = array("width" => 150, "height" => 150, "src" => "uuu", "alt" =>"data-mce-src" );
               
             //return;
             //$sql = "UPDATE posts SET content = '".$content."' WHERE id = '".$id."'"; 
             //$stmt = $this->pdo->query($sql);
             //$stmt->setFetchMode(\PDO::FETCH_ASSOC);
             
        //var_dump($help->textarea($fieldname,$name));
    } 

  public function tokeny(Request $request,AuthService $auth, SessionInterface $session, UserTable $userTable, MessageTable $messTable,Response $response)
    {
       //var_dump($request->getAttribute('user'));
       //die();
       $userId = $this->auth->user()->id;  
       $item = $userTable->findOrFail($userId);
       //var_dump($item->token);
       //die();      
       return $response->withJson(array('userId' => intval($item->id), 'token' => $item->token, 'username' =>$item->username));
    } 


     public function load($id, Request $request,PostTable $postTable)
    {
             //$params = $request->getParsedBody();
             $item = $postTable->findOrFail($id);
             $id = $item->id;
             $content = $request->getParam('myNode');
             $a= new \PDO("mysql:host=localhost;dbname=puf;","root","froggy25");
             $sql = "SELECT content FROM posts WHERE id=$id";
             $pom = $a->prepare($sql);
             $result = $pom->setFetchMode(\PDO::FETCH_ASSOC);  
             $pom->execute();
             $post = $pom->fetch();
             //$content = $request->getParam('main-content');
             //$var="User', email='test";
             //$a= new \PDO("mysql:host=localhost;dbname=puf;","root","froggy25");
             //$b=$a->prepare("SELECT content FROM `posts` WHERE id=:id");
             //$b->bindParam(":content",$content);
             //$b->bindParam(":id",$id);
             //$b->execute();   
             foreach($post as $p)
             echo $p;   
             return;
             //$sql = "UPDATE posts SET content = '".$content."' WHERE id = '".$id."'"; 
             //$stmt = $this->pdo->query($sql);
             //$stmt->setFetchMode(\PDO::FETCH_ASSOC);
             
        //var_dump($help->textarea($fieldname,$name));
    } 

     public function upload(Request $request)
    {
          if (isset($_FILES['myFile'])) {
          
          //move_uploaded_file($_FILES['myFile']['tmp_name'], "uploads/posts/" . $_FILES['myFile']['name']);  
          //$remote_file_path = '/home/sophie/autre.fr/public/uploads/posts/'; 
          //$local_file_path = "uploads/posts/" . $_FILES['myFile']['name']; 
          
          $local_path = $_SERVER['DOCUMENT_ROOT']. '/uploads/posts';
          // Create the directory if it doesn't exist
                if ( ! is_dir( $local_path ) ) {
                    mkdir( $local_path );
         }
// Make the directory writable
        if ( ! is_writable( $local_path ) ) {
             chmod( $local_path, 0777 );
          }
          foreach ( $_FILES as $ul_file ) 
    //$ul_file = base64_decode($ul_file);
    if ( $ul_file['error'] )
        die( "Your file upload failed with error code " . $ul_file['error'] );
    // Set a new file name for the temporary file
    $new_file_name = $local_path . '/' . $ul_file['name'];
    $new = 'https://'.$_SERVER['HTTP_HOST']. '/' .'uploads/posts/'.$ul_file['name'];
    //die();
    // Move the temporary file away from the temporary directory
    if ( ! move_uploaded_file( $ul_file['tmp_name'], $new_file_name ) )
        die( "Failed moving the uploaded file" );
    // Store the local file paths to an array
    //$local_file_paths[] = $new_file_name; 
    $fruits = array("width" => 150, "height" => 150, "src" => $new, "alt" =>"data-mce-src" );
    echo json_encode($fruits);
    //return; 

           //$ftp_server     = "localhost";
         //$ftp_username   = "sophie";
          //$ftp_password   = "froggy25";
          //$conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
              //if ( @ftp_login($conn_id, $ftp_username, $ftp_password) ) {
             //echo "<p>Connected as $ftp_username @ $ftp_server</p>";
                 //} else {
                    //die( "Could not log in as $ftp_username\n" );
             //}

// Loop through the local filepaths array we created
              //foreach ( $local_file_paths as $local_file_path ) {
    // The remote file path on the FTP server is your string + 
    // the base name of the local file
                 //$remote_file_path = "autre.fr/public/uploads/posts/" . basename( $local_file_path );
                 //var_dump($remote_file_path);
                 //die(); 
    // Put the file on the server
                //ftp_put( $conn_id, $remote_file_path, $local_file_path, FTP_BINARY );
                  //echo "<p>Uploaded a file to $remote_file_path</p>";
          //}
// Close the connection
                //ftp_close( $conn_id );

           //echo "<p>Connection closed. Your images are here:</p>"; 
            //foreach ( $local_file_paths as $local_file_path ) {
              // $remote_file_path = "http://autre.fr/uploads/posts/" . basename( $local_file_path );
               //$remote = "http://autre.fr/uploads/posts/" . basename( $local_file_path );
               //var_dump(base64_decode($ul_file));
               //die();
               //echo "<img src='$remote_file_path' alt='Your uploaded file' />";
               //$fruits = array("width" => 150, "height" => 150, "src" => $remote_file_path, "alt" =>"data-mce-src" );
               //echo json_encode($fruits);
            //}
            
            //move_uploaded_file($_FILES['myFile']['tmp_name'], "uploads/posts/" . $_FILES['myFile']['name']); 
            
            exit;
        }
    }  


       public function mind(Request $request)
    {
          if (isset($_FILES['image'])) {
          $res[] = $_FILES['image'];
          //var_dump($res);
          //die();
          $local_path = $_SERVER['DOCUMENT_ROOT']. '/uploads/posts';
          $new_file_name = $local_path . '/' . $res[0]['name'];
          move_uploaded_file( $res[0]['tmp_name'], $new_file_name);
          
          } 
          return;
         
          

    }  
       public function save(Request $request)
    {
          if (isset($_FILES['image'])) {
          $local_path = $_SERVER['DOCUMENT_ROOT']. '/uploads/posts'; 
          $new_file_name = $local_path . '/' . $_FILES['image']['name'];
          if (file_exists($new_file_name )){
                
                
          $ftp_server     = "localhost";
         $ftp_username   = "sophie";
         $ftp_password   = "froggy25";
          $conn_id = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
              if ( @ftp_login($conn_id, $ftp_username, $ftp_password) ) {
             echo "<p>Connected as $ftp_username @ $ftp_server</p>";
                 } else {
                   die( "Could not log in as $ftp_username\n" );
             }
          
          $remote_file_path = "http://autre.fr/uploads/posts/" . $_FILES['image']['name'];
          ftp_put( $conn_id, $remote_file_path,$new_file_name, FTP_BINARY );
          ftp_close( $conn_id );  
       }   
          
     }
          
           
          return;
         
          

    }   
     
     
}
