<?php
namespace App\Blog;
use DateTime;
use App\Blog\PostEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="tags")
 * 
 */
class TagEntity
{

    /**
    * @var int
    *
    * @ORM\Column(name="id", type="integer", nullable=false)
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="IDENTITY")
    */
    public $id;
    public $name;
    public $post;
     
    public function getPost(): ?PostEntity
    {
        return $this->post;
    }

    public function setPost(?PostEntity $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getId()
    {

       return $this->id;
    }

    public function getTagName()
    {

       return $this->name;
    }

}
