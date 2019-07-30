<?php
namespace App\Blog\Actions;

use Framework\Events\Event;
use App\Blog\Entity\Post;

class OnDeleteEvent extends Event
{

      


    public function __construct(Post $deleted)
    {
        $this->setName('database.post.deleted');
        $this->setTarget($deleted);
    }
}
