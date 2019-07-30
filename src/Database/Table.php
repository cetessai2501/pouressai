<?php
namespace App\Database;
use App\Blog\PostEntity;
use Psr\Container\ContainerInterface;
/**
 * Représente une table en base de données.
 */
class Table
{
    /**
     * Annotation et PHPDoc fonctionne ensemble.
     *
     * @Inject
     * @var Database
     */
    protected $database;


    protected $container; 
    /**
     * Nom de la table en abse de données.
     */
    public const TABLE = null;
    /**
     * Permet de définir dans quel entité sauvegarder les résultats.
     */
    public const ENTITY = null;
    

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container; 
        $this->pdo = $this->container->get(\PDO::class);
        //$this->pdo2 = $pdo2;
    }
   
    public static function hydrate(array $array, $object)
   {
        
        if (is_string($object)) {

            $instance = new $object();
        } else {
            $instance = $object;
        }
        foreach ($array as $key => $value) {
            $method = self::getSetter($key);
//var_dump($method);
            if (method_exists($instance, $method)) {
                $instance->$method($value);
            } else {
                $property = lcfirst(self::getProperty($key));
                $instance->$property = $value;
            }
        }
        return $instance;
    }

    private static function getSetter(string $fieldName): string
    {
          return 'set' . self::getProperty($fieldName);
    }

    private static function getProperty(string $fieldName): string
    {
        return join('', array_map('ucfirst', explode('_', $fieldName)));
    } 

    /**
     * Récupère un enregistrement en se basant sur l'ID.
     *
     * @param int $id
     *
     * @return \stdClass
     */
    public function find(int $id)
    {
        return $this->database->fetch('SELECT * FROM ' . static::TABLE . ' WHERE id = ?', [$id], static::ENTITY);
    }

    public function findSlug(string $slug)
    {
        $query = $this->pdo->query('SELECT posts.*,categories.name as category_name, categories.slug as category_slug, comments.pseudo as comment_titre, comments.content as comment_content ,DATETIME(posts.created_at, "localtime") as time, DATETIME(comments.created_at, "localtime") as comment_time FROM ' . static::TABLE . ' LEFT JOIN categories ON categories.id = posts.category_id LEFT JOIN comments ON comments.post_id = posts.id WHERE posts.slug = ?', \PDO::FETCH_ASSOC);
$pom = $query->execute([$slug]); 
        $tabi = $query->fetch();

        if($tabi !== false){
        $ent = new PostEntity($tabi);

        return $ent;
        }else{
           $error = 'No record found !!';
           throw new \Exception($error);
       
        } 

        
    }



  

    public function findAllTagsWithIdArray($id)
    {
      $error = 'erreur';
        $result = $this->pdo->query('
          SELECT 
            posts.*, tags.name as tag_name
          FROM posts 
          
          LEFT JOIN posts_tags ON posts_tags.post_id = posts.id
          LEFT JOIN tags ON tags.id = posts_tags.tag_id 
          
          WHERE posts.id = ? 
        ', \PDO::FETCH_ASSOC);
        $result->execute([$id]);
        $res = $result->fetchAll();
        if ($res === false) {
            throw new NoRecordException();
        }
        
        return $res;

    
    }



    public function findAllTagsWithId($id)
    {
      $error = 'erreur';
        $reso = $this->pdo->query('
          SELECT 
            posts.*, tags.name as tag_name
          FROM posts 
          
          LEFT JOIN posts_tags ON posts_tags.post_id = posts.id
          LEFT JOIN tags ON tags.id = posts_tags.tag_id 
          
          WHERE posts.id = ? 
        ', \PDO::FETCH_CLASS, PostEntity::class);
$reso->execute([$id]);
$result = $reso->fetchAll();
        if ($result[0]->tag_name === null) {
            return [];
        }
        return $result;

    
    }
    /**
     * écupère un enregistrement en se basant sur l'ID et renvoie une exception si l'entité n'existe pas.
     *
     * @param $id
     *
     * @throws NoRecordException
     *
     * @return \stdClass
     */
    public function findOrFail(int $id)
    {
        $record = $this->find($id);
        if (!$record) {
            throw new NoRecordException('Aucun enregistrement ' . static::TABLE . '::' . $id);
        }
        return $record;
    }
    public function findList(string $field)
    {
        $records = $this->pdo->query('SELECT id, ' . $field . ' FROM ' . static::TABLE, \PDO::FETCH_CLASS, static::ENTITY);

$records->execute([]);
$reso = $records->fetchAll();

        $results = [];
        foreach ($reso as $record) {
            $results[$record->id] = $record->$field;
        }
        return $results;
    }
    public function findAll(string $suffix = '', array $params = [])
    {
        //return $this->database->getPDO()
        return $this->database->fetchAll('SELECT * FROM ' . static::TABLE . ' ' . $suffix, $params, static::ENTITY);
    }

    public function findAlli(string $suffix = '', array $params = [])
    {
        //return $this->database->getPDO()
        return $this->database->fetchAll('SELECT * FROM ' . static::TABLE . ' ' . $suffix .'ORDER BY created_at DESC', $params, static::ENTITY);
    }
    /**
     * Supprime un enregistrement.
     *
     * @param int $id
     *
     * @return \PDOStatement
     */
    public function delete(int $id): \PDOStatement
    {
        return $this->database->query('DELETE FROM ' . static::TABLE . ' WHERE id = ?', [$id]);
    }
    /**
     * Met à jour un enregistrement
     * Attention, les clefs ne sont pas échapées !
     *
     * @param int   $id
     * @param array $params
     *
     * @return \PDOStatement
     */
    public function update(int $id, array $params): \PDOStatement
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $params['id'] = $id;
        return $this->database->query('UPDATE ' . static::TABLE . ' SET ' . $query . ' WHERE id = :id', $params);
    }
    /**
     * Crée un nouvel enregistrement.
     *
     * @param array $params
     *
     * @return int|null
     */
public function createlite(array $params): ?int
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $this->database->query('INSERT INTO ' . static::TABLE . ' VALUES ' . $query, $params);
        return $this->database->lastInsertId();
    }


public function findAllTagos()
{
$resoi = $this->pdo->query('
          SELECT tags.name as tag_name 
          FROM tags 
          
          
          
        ', \PDO::FETCH_ASSOC);
$resoi->execute();
$res = $resoi->fetchAll();
return $res;

}


public function insert(array $params)
    {
$query = implode(', ', array_map(function ($field) {
            return "'$field'";
        }, $params));

        $fields = array_keys($params);
        

        $statement = $this->pdo->prepare("INSERT INTO " . static::TABLE . "(" .
            join(',', $fields) .
            ") VALUES (" . $query . 
            ")");
        $statement->execute();
        return $this->pdo->lastInsertId();
    }





    public function create(array $params): ?int
    {
        $query = implode(', ', array_map(function ($field) {
            return "$field = :$field";
        }, array_keys($params)));
        $this->database->query('INSERT INTO ' . static::TABLE . ' SET ' . $query, $params);
        return $this->database->lastInsertId();
    }

    public function findTagsForPost(array $tags)
    {

 $res = $this->database->fetchAll("SELECT tags.id,tags.name FROM `tags` WHERE `name` IN ('" . implode("','", $tags) . "')"); 
//$res = $sql->fetchAll();
return $res;




    } 

public function attachTagsByName($val, $val2)
{
$sql1 = $this->database->fetch('SELECT tags.id, tags.name FROM tags WHERE tags.name = ?', [$val2] );
//$sql2 = "INSERT INTO posts_tags (post_id, tag_id) VALUES (?,?)";
//$stmt= $this->database->query($sql, [$val, $val2] );
$tagid = intval($sql1->id);
$sql2 = "INSERT INTO posts_tags (post_id, tag_id) VALUES (?,?)";
$stmt= $this->database->query($sql2, [$val, $tagid] );

return $this->database->lastInsertId();
}


public function attachTags($val, $val2)
{
$sql = "INSERT INTO posts_tags (post_id, tag_id) VALUES (?,?)";
$stmt= $this->database->query($sql, [$val, $val2] );

return $this->database->lastInsertId();
}

public function detachTag($val, $val2)
{
$statement = $this->database->query('DELETE FROM posts_tags WHERE post_id = ? AND tag_id = ?', [$val, $val2]);
        

}
public function findTagByName($val, $name)
{
//$statement = $this->database->query('DELETE FROM posts_tags WHERE post_id = ? AND tag_id = ?', [$val, $val2]);
$sql1 = $this->database->fetch('SELECT tags.id, tags.name FROM tags WHERE tags.name = ?', [$name] );        
return $sql1;
}

        public function pagination($number)
    {
$res = $this->pdo->query('SELECT count(id) FROM '. static::TABLE,\PDO::FETCH_NUM);
$res->execute();
$count = (int)$res->fetch()[0]; 
//var_dump($res->fetch());
//$count = (int)$this->pdo->query('SELECT count(id) FROM posts')->fetch(\PDO::FETCH_NUM)[0];
$pages = ceil($count / $number);
return $pages;


    }


    /**
     * Compte le nombre d'enregistrement.
     *
     * @return int
     */
    public function count(): int
    {
        return $this->database->fetchColumn('SELECT COUNT(id) FROM ' . static::TABLE);
    }
    /**
     * Récupère l'instance de la base de données.
     *
     * @return Database
     */
    public function getDatabase(): Database
    {
        return $this->database;
    }
    /**
     * Renvoie la table utilisée.
     *
     * @return null|string
     */
    public function getTable(): ?string
    {
        return static::TABLE;
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
        $table = static::TABLE;
        $count = $this->database->fetchColumn('SELECT COUNT(id) FROM ' . $table);
        return (new PaginatedQuery(
            $this->database,
            'SELECT * FROM ' . $table . ' ORDER BY id DESC',
            [],
            $count,
            static::ENTITY
        ))
            ->getPaginator()
            ->setCurrentPage($currentPage)
            ->setMaxPerPage($perPage);
    }
}
