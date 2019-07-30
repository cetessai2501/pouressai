<?php
namespace App\Blog;
use DateTime;
use DateTimeZone;
use App\Blog\TagEntity;
use App\Annotations\MyAnnotation;
use DI\Annotation\Inject;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 * 
 */
class PostEntity implements \ArrayAccess
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
     * @ORM\Column(type="text", nullable=false)
     */
    public $content;
    /**
     * @ORM\Column(type="datetime")
     */
    public $created_at;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    public $image;
    
    public $categoryName;
    public $image2;
    public $tag_name;
    /**
     * @ORM\ManyToOne(targetEntity="App\Blog\CategoryEntity", inversedBy="post_id")
     * 
     */
    public $category;  
    public $categories;
    public $category_id; 
    public $comment_titre;
    public $comment_content; 
    public $comment_time; 
    public $userId;
    public $time;
    public $path;
    public $tags = [];
    public $category_name;
    public $suffix;
    public $tagos;
    
    public function __construct(array $donnees = array())
    {
        if (!empty($donnees))
        {
                $this->hydrate($donnees);
        }
        $this->path = '/home/sophie25/palipum/public/uploads/posts';
        $this->suffix = '_copy';
        $this->categories = new ArrayCollection();  
        $this->tagos = new ArrayCollection();
    }

    public function hydrate(array $donnees)
    {
            foreach ($donnees as $attribut => $valeur)
            {
                 //var_dump($valeur);
                $methode = self::getSetter($attribut);
                
                 
                 if (method_exists($this, $methode))
      {
        $this->$methode($valeur);
      }
            }
    }


    public function getThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);

        return '/uploads/posts/'.  $filename . '_thumb.' . $extension;
    }
    public function setImage($image)
    {
         $this->image = $image;
         return $this;
    } 

    public function delImage()
    {
        return '/home/sophie25/palipum/public/uploads/posts/' . $this->image;
    } 

    public function delThumb()
    {
        ['filename' => $filename, 'extension' => $extension] = pathinfo($this->image);
        return '/home/sophie25/palipum/public/uploads/posts/' . $filename . '_thumb.' . $extension;
    } 

    public function getPathWithSuffix(string $path, string $suffix): ?string
    {
        $info = pathinfo($this->path .'/'. $this->image);
        if(file_exists($this->path .'/'. $info['filename']. $this->suffix .'_thumb' .'.' . $info['extension'])){
        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . $this->suffix .'.' . $info['extension'];
        }else{
          return null;
        } 
    }

    public function getImageUrl()
    {
        return '/uploads/posts/' . $this->image;
    }
    public function getCreatedAt()
    {
        return new DateTime($this->created_at);
    } 
    public function getCreated()
    {
        return new DateTime($this->time);
    } 

    public function excerpt ()
    {
        if(mb_strlen($this->content) <= 60) {
              return $this->content;
        }
        $lastSpace = mb_strpos($this->content, ' ', 60);
        return substr($this->content, 0, $lastSpace) . '...';  
    } 
    
    public function setTags (array $arraytag): void
    {

        $this->tags = $arraytag;
        

    } 

    public function setTagName($tagname)
    {
         $this->tag_name = $tagname;
         return $this;
    } 

    public function getCategoryId()
    {

       return $this->category_id;
    }

    public function getId()
    {

       return $this->id;
    }

    public function setId($id)
    {
        $this->id = intval($id);
        return $this;
    }

    public function setCategoryId($category_id): void
    {
        $this->category_id = intval($category_id);
    } 

    public function setUserId($userId)
    {
        $this->userId = intval($userId);
          
    } 

    public function setCategoryName($categoryName): void
    {
        $this->categoryName = $categoryName;
        
    } 
    /**
     * Get the value of category_id
     */ 
    public function getCategory()
    {
        return $this->category;
    }

    public function getCommentTitre()
    {
        return $this->comment_titre;
    } 

    public function setCommentTime($time): void
    {
        $this->comment_time = $time;
        
    }  

    public function getCommentTime()
    {
        return new DateTime($this->comment_time);
    } 

    public function setCommentTitre($titre): void
    {
        $this->comment_titre = $titre;
        
    } 

    public function getCommentContent()
    {
        return $this->comment_content;
    } 

    public function setCommentContent($content): void
    {
        $this->comment_content = $content;
        
    }
    /**
     * Get the value of userId
     */ 
    public function getUserId()
    {
        return $this->userId;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }  
    /**
     * Get the value of name
     */ 
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @return  self
     */ 
    public function setName($name)
    {
        $this->name = $name;

        
    }
    public function setSlug($slug)
    {
        $this->slug = $slug;
    } 
 
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getTime()
    {
        return $this->time->format('d F Y H:i:s');
    }

    public function getDay()
    {
        return $this->time->format('d F Y');
    }  

    public function setTime($time)
    {
        $this->time = new DateTime($time);
    }

    public function offsetGet($var)
    {
            if (isset($this->$var) && is_callable(array($this, $var)))
            {
                return $this->$var();
            }
    } 

    public function offsetSet($var, $value)
        {
            $method = 'set'.ucfirst($var);
             
            if (isset($this->$var) && is_callable(array($this, $method)))
            {
                $this->$method($value);
            }
        }
         
        public function offsetExists($var)
        {
            return isset($this->$var) && is_callable(array($this, $var));
        }
         
        public function offsetUnset($var)
        {
            throw new \Exception('Impossible de supprimer une quelconque valeur');
        }

    public function addTag(TagEntity $tag): self
    {
        if (!$this->tagos->contains($tag)) {
            $this->tagos[] = $tag;
            $tag->setPost($this);
        }

        return $this;
    } 
    /**
     * Get the value of tags
     */ 
    public function getTags()
    {
        return $this->tags;
    }
    private function getProperty(string $fieldName): string
    {
        return join('', array_map('ucfirst', explode('_', $fieldName)));
    }


    private function getSetter(string $fieldName): string
    {
          return 'set' . self::getProperty($fieldName);
    }


    /**
     * Add categories
     *
     * @param \App\Blog\CategoryEntity $categories
     * @return CategoryEntity
     */
    public function addCategorie(\App\Blog\CategoryEntity $categories)
    {
        $this->categories[] = $categories;
        return $this;
    }


    public function getClass(): string
    {

        return get_class($this);

    } 







 
}

