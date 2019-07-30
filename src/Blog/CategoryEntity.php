<?php
namespace App\Blog;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class CategoryEntity
{
    /**
    * @var int
    * 
    * @ORM\Column(name="id", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    */
    public $id;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $name;
    /**
     * @ORM\Column(type="text", nullable=false)
     */
    public $slug;
    /**
     * @ORM\Column(type="datetime")
     */
    public $created_at;
    /**
     * @ORM\OneToMany(targetEntity="App\Blog\PostEntity", mappedBy="category")
     */  
    public $post_id;
    
    public $post_name;
    public $post_content;
    public $post_time; 

    public function getName()
    {
        return $this->name;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getPostId()
    {
        return $this->post_id;
    }

    public function getTime()
    {
        return new DateTime($this->post_time);
    }






}
