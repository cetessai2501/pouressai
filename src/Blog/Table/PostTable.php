<?php
namespace App\Blog\Table;

use App\Blog\PostEntity;
use App\Blog\CategoryEntity;
use App\Database\Table;
use App\Database\PaginatedQuery;
use App\Blog\TagEntity;
/**
 * Permet de récupérer les articles depuis la base de données.
 */
class PostTable extends Table
{
    public const TABLE = 'posts';
    public $comments_by_id;
    public const ENTITY = PostEntity::class;
    public function findLatest()
    {
        return $this->database->fetchAll('SELECT 
              posts.*,
              categories.name as category_name, categories.slug as category_slug
              FROM posts 
              LEFT JOIN categories ON categories.id = posts.category_id
              ORDER BY created_at DESC
              LIMIT 8', [], PostEntity::class);
    }

    public function findPublic()
    {
            $st = $this->pdo->query("SELECT * FROM posts");
         $st->execute();
        //$statement->execute([$offset, $limit]);
        $red = $st->fetchAll();
        return $red; 
       
 

    }
public function findess()
{
$st = $this->pdo->query("SELECT * FROM userinfo");
$st->execute();
$red = $st->fetchAll(\PDO::FETCH_ASSOC);
        return $red; 
}

    public function findLatestCat($id)
    {
        $reso =  $this->pdo->query('SELECT 
              categories.*,
              posts.name as post_name, posts.slug as post_slug, posts.content as post_content, posts.id as post_id, posts.created_at as post_time
              FROM categories
              JOIN posts ON posts.category_id = categories.id
              
              WHERE categories.id = ?  
              ORDER BY post_time DESC
              ', \PDO::FETCH_CLASS , CategoryEntity::class);
        $reso->execute([$id]);
        $res = $reso->fetchAll();
        return $res;
    }

    public function findLatestCatCom($id)
    {
        $reso =  $this->pdo->query('SELECT 
              categories.*,
              posts.name as post_name, posts.slug as post_slug, posts.content as post_content, posts.id as pid, posts.created_at as post_time
              FROM categories
              INNER JOIN posts ON posts.category_id = categories.id
              INNER JOIN comments ON comments.post_id = pid
              WHERE categories.id = ?  
              ORDER BY post_time DESC
              ', \PDO::FETCH_CLASS , CategoryEntity::class);
        $reso->execute([$id]);
        $res = $reso->fetchAll();
        return $res;
    }   



    public function pagination($number)
    {
$res = $this->pdo->query('SELECT count(id) FROM posts',\PDO::FETCH_NUM);
$res->execute();
$count = (int)$res->fetch()[0]; 
//var_dump($res->fetch());
//$count = (int)$this->pdo->query('SELECT count(id) FROM posts')->fetch(\PDO::FETCH_NUM)[0];
$pages = ceil($count / $number);
return $pages;


    }

    public function paginQuery(int $perpage, int $currentPage, int $number)
{
$offset = $number * ((int)$currentPage - 1);
$per = intval($perpage);
$tags = $this->findAllTags();
$implode = implode("','", array_keys($tags));


$reso = $this->pdo->query("SELECT 
              posts.*, 
              categories.name as category_name, categories.slug as category_slug, DATETIME(posts.created_at, 'localtime') as time
              FROM posts
              LEFT JOIN categories ON categories.id = posts.category_id
              
              
              GROUP BY posts.id
              ORDER BY created_at DESC
              LIMIT $per OFFSET $offset", \PDO::FETCH_ASSOC);
$reso->execute();
$res = $reso->fetchAll();

$price = array();
foreach($res as $key => $row){

$price[$key] = new PostEntity($row);

}

return $price;


}

public function findCommentsChildren ($commentid)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE comments.post_id = ?
ORDER BY comment_time DESC
", \PDO::FETCH_CLASS, \App\Blog\CommentEntity::class);
$reso->execute([$commentid]);
$comments = $reso->fetchAll();
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
return $comments;
}
return [];

}

public function findCommentsChildrenCom ($commentid)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE comments.id = ?
ORDER BY comment_time DESC
");
$reso->execute([$commentid]);
$comments = $reso->fetchAll(\PDO::FETCH_ASSOC);
if(!empty($comments)){
$comments_by_id = [];
foreach ($comments as $comment){
$comments_by_id[intval($comment['id'])] = $comment['id'];
}
foreach ($comments as $k => $com){
if(intval($com['parent_id']) !== 0 ){
$comments_by_id[intval($com['parent_id'])]->children[] = $com['id'];
//unset($comments[$k]);
} 

}
//$this->comments_by_id = $comments_by_id;
return $comments_by_id;
}
return [];

}

public function findComments ($slug)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?
ORDER BY comment_time DESC
", \PDO::FETCH_CLASS, \App\Blog\CommentEntity::class);
$reso->execute([$slug]);
$comments = $reso->fetchAll();
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
return $comments;
}
return [];

}

public function findCommentas ($slug)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?
ORDER BY comment_time DESC
");
$reso->execute([$slug]);
$comments = $reso->fetchAll(\PDO::FETCH_ASSOC);
if(!empty($comments)){


return $comments;
}
return [];


}

public function findParentIdColumComJson()
{
$data = $this->pdo->query('SELECT parent_id FROM comments')->fetchAll(\PDO::FETCH_COLUMN);
$out = array_values($data);

return json_encode($out, JSON_NUMERIC_CHECK);
}




public function findParentIdColumCom ()
{
$data = $this->pdo->query('SELECT parent_id FROM comments')->fetchAll(\PDO::FETCH_COLUMN);
return $data;
}

public function findGroupCom()
{
$data = $this->pdo->query('SELECT parent_id,id FROM comments')->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_COLUMN);

return $data;
}


public function threaded($slug)
    {

$reso = $this->pdo->query("SELECT comments.*,
comments.parent_id as par, posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?

ORDER BY comment_time DESC
");
$reso->execute([$slug]);
$comments = $reso->fetchAll(\PDO::FETCH_GROUP|\PDO::FETCH_UNIQUE);

        return $comments;
    }



public function comments ($slug)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE pslug = ?
ORDER BY comments.parent_id ASC
", \PDO::FETCH_CLASS, \App\Blog\CommentEntity::class);
$reso->execute([$slug]);
$comments = $reso->fetchAll();
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

return $comments;
}
return [];


}

public function commentsByTag ($tag)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, tags.name as tag_name, DATETIME(comments.created_at, 'localtime') as comment_time
FROM comments
INNER JOIN posts ON pid = comments.post_id
INNER JOIN posts_tags ON posts_tags.post_id = comments.post_id
INNER JOIN tags ON tags.id = posts_tags.tag_id 
          
          
WHERE tag_name = ?
ORDER BY comment_time DESC
", \PDO::FETCH_CLASS, \App\Blog\CommentEntity::class);
$reso->execute([$tag]);
$res = $reso->fetchAll();
return $res;

}

public function commentsId ($id)
{
$reso = $this->pdo->query("SELECT comments.*,
posts.id as pid, posts.slug as pslug, DATETIME(comments.created_at, 'localtime') as comment_time, comments.parent_id as parent
FROM comments
INNER JOIN posts ON pid = comments.post_id
WHERE comments.id = ?

", \PDO::FETCH_CLASS, \App\Blog\CommentEntity::class);
$reso->execute([$id]);
$comment = $reso->fetch();
return $comment;
//$this->pdo->prepare('DELETE FROM comments WHERE id = ?')->execute([$id]);
//$this->pdo->prepare('UPDATE comments SET parent_id = ? WHERE parent_id = ?')->execute([intval($comment->parent_id), intval($comment->id)]);


}

public function catego ()
{
$reso = $this->pdo->query('SELECT *
FROM categories

ORDER BY categories.name ASC

', \PDO::FETCH_CLASS, CategoryEntity::class);
$reso->execute();
$res = $reso->fetchAll();
return $res;

}


    public function findAllTags()
    {
      $error = 'errur';
        $resoi = $this->pdo->query('
          SELECT 
            posts.id, tags.name as tag_name
          FROM posts 
          
          LEFT JOIN posts_tags ON posts_tags.post_id = posts.id
          LEFT JOIN tags ON tags.id = posts_tags.tag_id 
          
          
        ');

//$result = $resoi->fetchAll(\PDO::FETCH_ASSOC);


       
        return $resoi->fetchAll(\PDO::FETCH_ASSOC);

    
    }










    /**
     * Récupère les données paginées.
     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function findPaginated($perPage = 10, $currentPage = 1)
    {
        $count = $this->database->fetchColumn('SELECT COUNT(id) FROM posts');
        return (new PaginatedQuery(
            $this->database,
            'SELECT  
            posts.*,
            categories.name as category_name, categories.slug as category_slug
            FROM posts 
            
            LEFT JOIN categories ON categories.id = posts.category_id
            
            ORDER BY created_at DESC',
            [],
            $count,
            PostEntity::class
        ))
            ->getPaginator()
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($perPage);
    }
    /**
     * Récupère les données paginées.
     *
     * @param int $perPage
     * @param int $currentPage
     *
     * @return \Pagerfanta\Pagerfanta
     */
    public function findPaginatedByCategory($perPage, $currentPage, string $categorySlug)
    {
        $count = $this->database->fetchColumn('
          SELECT COUNT(posts.id) 
          FROM posts INNER JOIN categories ON categories.id = posts.category_id
          WHERE categories.slug = ?', [$categorySlug]);
        return (new PaginatedQuery(
            $this->database,
            'SELECT 
              posts.*,
              categories.name as category_name, categories.slug as category_slug
            FROM posts 
            LEFT JOIN categories ON categories.id = posts.category_id
            WHERE categories.slug = ?
            ORDER BY created_at DESC',
            [$categorySlug],
            $count,
            PostEntity::class
        ))
            ->getPaginator()
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($perPage);
    }
    /**
     * Récupère un enregistrement à partir de son slug.
     *
     * @param string $slug
     *
     * @throws NoRecordException
     *
     * @return mixed
     */
    public function findTags(string $slug)
    {
        $res = $this->pdo->query('
          SELECT 
            posts.*, tags.name as tag_name
          FROM posts 
          
          LEFT JOIN posts_tags ON posts_tags.post_id = posts.id
          LEFT JOIN tags ON tags.id = posts_tags.tag_id 
          
          WHERE posts.slug = ? 
        ',  \PDO::FETCH_CLASS, PostEntity::class, [$slug]);
     //$res->execute();
$resu = $res->fetchAll();
        if ($resu[0]->tag_name === null) {
            return [];
        }
        return $resu;
    
    }

    public function searchByTag(string $tag):?array
    {

$result = $this->pdo->query('
          SELECT 
            posts.*, tags.name as tag_name
          FROM posts 
          
          LEFT JOIN posts_tags ON posts_tags.post_id = posts.id
          LEFT JOIN tags ON tags.id = posts_tags.tag_id 
          
          WHERE tags.name = ?
          
        ', \PDO::FETCH_CLASS, PostEntity::class);
$pom = $result->execute([$tag]);

$res = $result->fetchAll();

        if (!empty($res)) {
            return $res;
        }
        $error = 'No record found !!';
        throw new \Exception($error);
        

    }



    public function findByTags()
    {
$res1 = $this->pdo->query("SELECT DISTINCT
            tags.*, 
            posts.id as postid, posts.slug, posts_tags.id as tagid, 'App\Blog\PostEntity' as class_name, DATETIME(posts.created_at, 'localtime') as time
          FROM tags
          INNER JOIN posts ON posts.id = posts_tags.post_id 
          INNER JOIN posts_tags ON posts_tags.post_id = posts.id
          
          
          
          WHERE posts_tags.tag_id = tags.id
           
          ", \PDO::FETCH_CLASS, TagEntity::class);
$res1->execute();
$reso = $res1->fetchAll();
return $reso;

    }

    public function flatten(array $array)
    {
    $return = array();
    array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
    return $return;
    } 


    public function insertComments(array $params)
    {
$query = implode(', ', array_map(function ($field) {
            return "'$field'";
        }, $params));

        $fields = array_keys($params);
        

        $statement = $this->pdo->prepare("INSERT INTO comments (" .
            join(',', $fields) .
            ") VALUES (" . $query . 
            ")");
        $statement->execute();
        return $this->pdo->lastInsertId();
    }




    public function findBySlug(string $slug, $tags)
    {

        $res1 = $this->pdo->query("SELECT 
            tags.*, 
            posts.id as postid, posts.slug, tags.name as tag_name, DATETIME(posts.created_at, 'localtime') as time
          FROM tags
          
          LEFT JOIN posts_tags ON posts_tags.post_id = posts.id
          LEFT JOIN posts ON posts.id = posts_tags.post_id 
          WHERE posts_tags.post_id = posts.id AND tags.name IN ('" . implode("','", $tags) . "')
          
          AND posts.slug = ?", \PDO::FETCH_ASSOC);
        
        //$records = $result->fetchAll(\PDO::FETCH_ASSOC);
        $res1->execute([$slug]);
        $tabi = $res1->fetchAll();
        $newarray = array();
        foreach($tabi as $tab => $row){
         $newarray[$tab][$row['postid']] = $row['name']; 
         
        }
        //var_dump(array_unique($this->flatten($newarray)));
        return array_unique($this->flatten($newarray));

       
       
        
    }
}
