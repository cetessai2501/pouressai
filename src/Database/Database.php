<?php
namespace App\Database;
class Database
{
    public $database;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $host;
    /**
     * @var \PDO
     */
    private $pdo;
    public function __construct()
    {
        
    }
    /**
     * Lazy load la connexion PDO au besoin.
     *
     * @return \PDO
     */
    public function getPDO(): \PDO
    {
        if (!$this->pdo) {
$this->pdo = new \PDO('sqlite:/home/sophie25/palipum/pom.sqlite');
$this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_OBJ);
$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
   //$this->pdo = new \PDO("mysql:host={$this->host};dbname={$this->database};charset=UTF8", $this->username,$this->password, [ \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ ]  );
        }
        return $this->pdo;
    }
    /**
     * Permet de récupérer un enregistrement depuis la base de données.
     *
     * @param string      $query
     * @param array       $params
     * @param string|null $entity
     *
     * @return mixed
     */
    public function fetch(string $query, array $params = [], string $entity = null)
    {
        return $this->query($query, $params, $entity)->fetch();
    }
    /**
     * Récupère plusieurs enregistrements depuis la base de données.
     *
     * @param string      $query
     * @param array       $params
     * @param string|null $entity
     *
     * @return array
     */
    public function fetchAll(string $query, array $params = [], string $entity = null): array
    {
        return $this->query($query, $params, $entity)->fetchAll();
    }
    /**
     * Récupère une colonne.
     *
     * @param string      $query
     * @param array       $params
     * @param string|null $entity
     *
     * @return string
     */
    public function fetchColumn(string $query, array $params = [], string $entity = null): string
    {
        return $this->query($query, $params, $entity)->fetchColumn();
    }
    /**
     * Génère une requête PDO.
     *
     * @param string      $query
     * @param array       $params
     * @param string|null $entity
     *
     * @return \PDOStatement
     */
    public function query(string $query, array $params = [], string $entity = null): \PDOStatement
    {
        if (count($params) === 0) {
            $query = $this->getPDO()->query($query);
        } else {
            $query = $this->getPDO()->prepare($query);
            //var_dump($query);
            //die(); 
            $query->execute($params);
        }
        if ($entity) {
            $query->setFetchMode(\PDO::FETCH_CLASS, $entity);
        }
        return $query;
    }
    /**
     * Renvoie le dernier id inséré.
     *
     * @return int|null
     */
    public function lastInsertId(): ?int
    {
        return $this->getPDO()->lastInsertId();
    }
}
