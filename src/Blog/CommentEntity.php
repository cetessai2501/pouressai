<?php
namespace App\Blog;
use DateTime;


class CommentEntity 
{

      public $id;
      public $post_id;
      public $pseudo;
      public $content;
      public $email;
      public $created_at;
      public $user_id;
      public $comment_time;
      public $path;

    public function __construct()
    {
         $this->path = '/home/sophie25/palipum/public/uploads/posts';
    }

     public function getId()
    {

       return $this->id;
    }

      public function getCreatedAt()
    {
        return new DateTime($this->created_at);
    } 

     public function getCommentTime()
    {
        return new DateTime($this->comment_time);
    } 

    public function getTitre()
    {
        return $this->titre;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setUserId($userId)
    {
        $this->user_id = intval($userId);
          
    } 

    public function setPostId($post_id)
    {
        $this->post_id = intval($post_id);
          
    }

     

}
