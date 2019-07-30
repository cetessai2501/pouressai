<?php
namespace App\Controllers;

use App\Blog\Table\CategoriesTable;
use App\Blog\Table\PostTable;
use App\Controller;
use \Projek\Slim\Plates;
use \Projek\Slim\PlatesExtension;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Spipu\Html2Pdf\Html2Pdf;
use Dompdf\Dompdf;
use App\Upload;
use Slim\Http\UploadedFile;
use Psr\Http\Message\UploadedFileInterface;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\TwitterBootstrap4View;
use \Smalot\PdfParser\Parser;

class BlogController extends Controller
{
    public function index(Request $request, Response $response, $args)
    {

        $page = $request->getQueryParams('page', 1);
var_dump($page);
        $posts = [1 => 'pam', 2 => 'pom'];
        //$posts->addLink('self', $createLinkUrl($page));
        
        //return $this->render('blogindex', compact('posts'));
        $response = $this->render('blogindex', array('posts' => $posts));
        return $response;

    }
    public function category(string $slug, Request $request, PostTable $postTable, CategoriesTable $categoriesTable)
    {
        $category = $categoriesTable->findBySlug($slug);
        if (empty($category)) {
            throw new NoRecordException();
        }
        $page = $request->getParam('page', 1);
        $posts = $postTable->findPaginatedByCategory(12, $page, $slug);
        $view = new TwitterBootstrap4View();
        $options = array('proximity' => 3);
        $categories = $categoriesTable->findall();
$this->render('blogcategory', array('options' => $options,'view' => $view,'category' => $category, 'posts' => $posts, 'page' => $page, 'categories' => $categories));
    }
    public function show(Request $request, Response $response)
    {
        $post = [1 => 'pam'];
        $response = $this->render('blogshow.php', array('post' => $post));
        return $response;
    }

     public function getPDF(string $slug, PostTable $postTable, Plates $view)
    {
        $post = $postTable->findBySlug($slug);
        $posty = html_entity_decode(strip_tags($post->content)); 
        //$html2pdf = new Html2Pdf();
        $dompdf = new Dompdf();
        $dompdf->loadHtml($post->content);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
 
        //$html2pdf->writeHTML("$post->content");
        $response = new Response();
        $response->withHeader( 'Content-type', 'application/pdf' );  
        $response->write($dompdf->stream());
        return $response; 
        //return $view->render('blogpdf', array('pdf' => $html2pdf->output()));
    }

     public function getODT(string $slug, PostTable $postTable, Plates $view)
    {
        $post = $postTable->findBySlug($slug);
        $posty = html_entity_decode(strip_tags($post->content)); 
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText("$posty");
        // (Optional) Setup the paper size and orientation
         
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'ODText');
        
        $objWriter->save('post.odt');
        $fh = fopen('/home/sophie/autre.fr/public/post.odt', 'r');
        $stream = new \Slim\Http\Stream($fh);
        $response = new Response(); 
        return $response
            ->withBody($stream)
            ->withHeader('Content-Type', 'application/vnd.oasis.opendocument.text');
        
       
    }
      public function getDOCX(string $slug, PostTable $postTable, Plates $view, Request $request)
    {
        
        $post = $postTable->findBySlug($slug);
        $posty = html_entity_decode(strip_tags($post->content)); 
        
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText("$posty");
        // (Optional) Setup the paper size and orientation
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save('post.docx');
        $fh = fopen('/home/sophie/autre.fr/public/post.docx', 'r');
        $stream = new \Slim\Http\Stream($fh);
        $response = new Response();
        return $response
            ->withBody($stream)
            ->withHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        
        
    }

      public function PDFtoText(string $slug, PostTable $postTable, Plates $view, Request $request)
    {
        
        $post = $postTable->findBySlug($slug);
        $posty = html_entity_decode(strip_tags($post->content)); 
      
        $parser = new \Smalot\PdfParser\Parser();
        //die();
        $pdf  = $parser->parseFile('/home/sophie/autre.fr/public/uploads/posts/'.$post->image2); 
        //$pdf  = $parser->parseFile($post->image2);
        $text = $pdf->getText();
        //$pages  = $pdf->getPages();
        $fh = fopen('/home/sophie/autre.fr/public/post.txt', 'w');
        fwrite($fh,  $text);
        fclose($fh);
        $fp = fopen('/home/sophie/autre.fr/public/post.txt', 'r');
        //$phpWord = new \PhpOffice\PhpWord\PhpWord();
        //$section = $phpWord->addSection();
        //$section->addText("$posty");
        // (Optional) Setup the paper size and orientation
        //$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        //$objWriter->save('post.docx');
        //$fh = fopen('/home/sophie/autre.fr/public/post.docx', 'r');
        $stream = new \Slim\Http\Stream($fp);
        $response = new Response();
        return $response
               ->withBody($stream)
               ->withHeader('Content-Type', 'text/plain');
                         
        
        
    }
}
