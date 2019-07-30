<?php
namespace App;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;
use Dompdf\Dompdf;

class Upload
{

    protected $format = [
        'thumb' => [80, 80]
    ];

    protected $path = '/home/sophie25/palipum/public/uploads/posts';

    

    

    protected $formats = [];

    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }

    /**
     * @param UploadedFileInterface $file
     * @param null|string $oldFile
     * @return null|string
     */
     public function upload(UploadedFileInterface $file, $oldFile = null, ?string $filename = null): ?string
    {
        if ($file->getError() === UPLOAD_ERR_OK) {


            if($oldFile){
            //$this->delete($oldFile->image);
            
$oldpath = $this->path .
                DIRECTORY_SEPARATOR .
                ($filename ?: $oldFile->id.'-'.$file->getClientFilename());
if(file_exists($oldpath)){
$targetPath = $this->addCopySuffix(
                $this->path .
                DIRECTORY_SEPARATOR .
                ($filename ?: $oldFile->id.'-'.$file->getClientFilename())
            ); 
}

            }
            $targetPath = $this->addCopySuffix(
                $this->path .
                DIRECTORY_SEPARATOR .
                ($filename ?: $oldFile->id.'-'.$file->getClientFilename())
            );
          
            $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
            if (!file_exists($dirname)) {
                mkdir($dirname, 777, true);
            }
            $file->moveTo($targetPath);
            $this->generateFormats($targetPath);
            return pathinfo($targetPath)['basename'];
        }
        return null;
    }

    /**
     * addCopySuffix
     *
     * @param string $targetPath
     * @return void
     */
    private function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        return $targetPath;
    }

    /**
     * delete
     *
     * @param ?string $oldFile
     * @return void
     */
    public function delete(?string $oldFile): void
    {
        if ($oldFile) {
            $oldFile = $this->path . DIRECTORY_SEPARATOR . $oldFile;
            if (file_exists($oldFile)) {
                @unlink($oldFile);
            }
            foreach ($this->formats as $format => $_) {
                $oldFileWithFormat = $this->getPathWithSuffix($oldFile, $format);
                if (file_exists($oldFileWithFormat)) {
                    @unlink($oldFileWithFormat);
                }
            }
        }
    }

    /**
     * getPathWithSuffix
     *
     * @param string $path
     * @param string $suffix
     * @return void
     */
    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        return $info['dirname'] . DIRECTORY_SEPARATOR .
            $info['filename'] . '_' . $suffix .'.' . $info['extension'];
    }

    /**
     * generateFormats
     *
     * @param mixed $targetPath
     * @return void
     */
    private function generateFormats($targetPath)
    {
        foreach ($this->formats as $format => $size) {
            $manager = new ImageManager(['driver' => 'gd']);
            $destination = $this->getPathWithSuffix($targetPath, $format);
            [$width , $height] = $size;
            $manager->make($targetPath)->fit($width, $height)->save($destination);
        }
    }
}
