<?php
namespace App\Blog;
use App\Upload;
use \Slim\Psr7\UploadedFile;

class PostUpload extends Upload
{
    protected $path = '/home/sophie25/palipum/public/uploads/posts';
    /**
     * Liste les formats à générer.
     *
     * @var array
     */
    protected $formats = [
        'thumb' => [80, 80]
    ];
  
    public function getPath()
    {
         return $this->path;
    }
   
}
