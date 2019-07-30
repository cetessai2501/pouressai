<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use App\Upload;

class UploadTest extends TestCase
{


    private $strig;
    /**
     * @var Upload
     */
    private $upload;

    public function setUp(): void
    {
        $this->upload = new Upload('/tmp');
        $this->strig = 1;
    }


     public function testMe ($a)
    {
        if ($a == 1)
        {
            throw new Exception ('YAY');
        }

        return true;
    }







}
