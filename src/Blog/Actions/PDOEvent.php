<?php
namespace App\Blog\Actions;

use Framework\Events\Event;
use App\Blog\Entity\Post;
use Psr\Container\ContainerInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as Capsule;

class PDOEvent extends Event
{

    private $container;  
 
    public $pdo;

    public $capsule; 

    public function __construct(array $query, ContainerInterface $container, Capsule $capsule)
    {
        $this->container = $container;
        $this->capsule = new Capsule;
        $this->pdo = $this->container->get(\PDO::class);
        $this->setName('database.event');
        $this->setTarget($query);
        $this->capsule->addConnection([
    'driver'    => 'sqlite',
    'host'      => 'localhost',
    'database' => '/home/sophie/dang/monsupersite.sqlite3',
    'username'  => null,
    'password'  => null,
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]); 
      $this->capsule->setAsGlobal();
       
       $this->capsule->bootEloquent(); 
    }

    public function boot()
    {
        $man = $this->capsule->getDatabaseManager();
        $users = Capsule::table('posts')->get();
        return $man;
    }

}
