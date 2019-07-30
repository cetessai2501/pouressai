<?php
namespace App\Auth\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\JavascriptRenderer as DebugBarRenderer;
use DebugBar\StandardDebugBar;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;
use GuzzleHttp\Psr7\Stream;

class DebugMiddle implements MiddlewareInterface
{
    public const FORCE_KEY = 'X-Enable-Debug-Bar';

    public $debugbar; 
    protected $cont; 
    public $debugBarRenderer;
    private $streamFactory;

public function __construct(ContainerInterface $cont, StandardDebugBar $debugbar, DebugBarRenderer $debugbarRenderer,ResponseFactoryInterface $streamFactory )
    {

          
        $this->cont = $cont;
        $this->debugbar = $cont->get('debugbar');
        $this->debugBarRenderer = $this->debugbar->getJavascriptRenderer();
        $this->streamFactory = $cont->get('App\MyApp')->getFactory();                           
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        if ($staticFile = $this->getStaticFile($request->getUri())) {
            return $staticFile;
        }
        $data = array('foo' => 'bar');
$this->debugbar->addCollector(new \DebugBar\DataCollector\ConfigCollector($data));

$pdo = new \DebugBar\DataCollector\PDO\TraceablePDO($this->cont->get(\PDO::class));
$pol = new \DebugBar\DataCollector\PDO\PDOCollector($pdo);

//$debugbar->addCollector(new \Filisko\DebugBar\DataCollector\RedBeanCollector($container->get(\RedBeanPHP\Logger::class)));
$this->debugbar->addCollector($pol); 

        $response = $handler->handle($request);
        $forceHeaderValue = $request->getHeaderLine(self::FORCE_KEY);
        $forceCookieValue = $request->getCookieParams()[self::FORCE_KEY] ?? '';
        $forceAttibuteValue = $request->getAttribute(self::FORCE_KEY, '');
        $isForceEnable = in_array('true', [$forceHeaderValue, $forceCookieValue, $forceAttibuteValue], true);
        $isForceDisable = in_array('false', [$forceHeaderValue, $forceCookieValue, $forceAttibuteValue], true);
        if ($isForceDisable || (!$isForceEnable && ($this->isRedirect($response) || !$this->isHtmlAccepted($request)))) {
            return $response;
        }
        if ($this->isHtmlResponse($response)) {
            return $this->attachDebugBarToResponse($response);
        }
        return $this->prepareHtmlResponseWithDebugBar($response);
    }




private function attachDebugBarToResponse($response)
    {
        $head = $this->debugBarRenderer->renderHead();
        $body = $this->debugBarRenderer->render();
        $responseBody = $response->getBody();
        if (! $responseBody->eof() && $responseBody->isSeekable()) {
            $responseBody->seek(0, SEEK_END);
        }
        $responseBody->write($head . $body);
        return $response;
    }



private function getStaticFile(\Slim\Psr7\Uri $uri): ?Response
    {
        
        $path = $this->extractPath($uri);
        if (strpos($path, $this->debugBarRenderer->getBaseUrl()) !== 0) {
            return null;
        }
        $pathToFile = substr($path, strlen($this->debugBarRenderer->getBaseUrl()));
        $fullPathToFile = $this->debugBarRenderer->getBasePath() . $pathToFile;
        if (!file_exists($fullPathToFile)) {
            return null;
        }
        $contentType = $this->getContentTypeByFileName($fullPathToFile);
        $stream = $this->streamFactory->createStreamFromResource(fopen($fullPathToFile, 'r'));
        return $this->responseFactory->createResponse(200)
            ->withBody($stream)
            ->withAddedHeader('Content-type', $contentType);



       
    }

    private function getContentTypeByFileName(string $filename): string
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $map = [
            'css' => 'text/css',
            'js' => 'text/javascript',
            'otf' => 'font/opentype',
            'eot' => 'application/vnd.ms-fontobject',
            'svg' => 'image/svg+xml',
            'ttf' => 'application/font-sfnt',
            'woff' => 'application/font-woff',
            'woff2' => 'application/font-woff2',
        ];
        return isset($map[$ext]) ? $map[$ext] : 'text/plain';
    }







    private function extractPath(\Slim\Psr7\Uri $uri): string
    {
        // Slim3 compatibility
        if ($uri instanceof SlimUri) {
            $basePath = $uri->getBasePath();
            if (!empty($basePath)) {
                return $basePath;
            }
        }
        return $uri->getPath();
    }

        private function isRedirect(ResponseInterface $response): bool
    {
        $statusCode = $response->getStatusCode();
        return ($statusCode >= 300 || $statusCode < 400) && $response->getHeaderLine('Location') !== '';
    }

        private function isRedirectGuzzle(Response $response): bool
    {
        $statusCode = $response->getStatusCode();
        return ($statusCode >= 300 || $statusCode < 400) && $response->getHeaderLine('Location') !== '';
    }




        private function isHtmlAccepted(Request $request): bool
    {
        return $this->hasHeaderContains($request, 'Accept', 'text/html');
    }

    private function hasHeaderContains(MessageInterface $message, string $headerName, string $value): bool
    {
        return strpos($message->getHeaderLine($headerName), $value) !== false;
    } 

    private function isHtmlResponse($response): bool
    {
        if($response instanceof ResponseInterface){
        return true;
        } 
        return false; 

    }

     private function prepareHtmlResponseWithDebugBar(\Slim\Psr7\Response $response): Response
    {
        $head = $this->debugBarRenderer->renderHead();
        $body = $this->debugBarRenderer->render();
        $outResponseBody = $this->serializeResponse($response);
        $template = '<html><head>%s</head><body><h1>DebugBar</h1><p>Response:</p><pre>%s</pre>%s</body></html>';
        $escapedOutResponseBody = htmlspecialchars($outResponseBody);
        $result = sprintf($template, $head, $escapedOutResponseBody, $body);
        $bodi = new Stream(fopen('php://temp', 'r+'));
        $bodi->write($outResponseBody);
        return (new Response())
            ->withStatus(200)
            
            ->withBody($bodi);
    }

    private function serializeResponse(\Slim\Psr7\Response $response) : string
    {
        $reasonPhrase = $response->getReasonPhrase();
        $headers      = $this->serializeHeaders($response->getHeaders());
        $body         = (string) $response->getBody();
        $format       = 'HTTP/%s %d%s%s%s';
        if (! empty($headers)) {
            $headers = "\r\n" . $headers;
        }
        $headers .= "\r\n\r\n";
        return sprintf(
            $format,
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            ($reasonPhrase ? ' ' . $reasonPhrase : ''),
            $headers,
            $body
        );
    }   


        private function serializeHeaders(array $headers) : string
    {
        $lines = [];
        foreach ($headers as $header => $values) {
            $normalized = $this->filterHeader($header);
            foreach ($values as $value) {
                $lines[] = sprintf('%s: %s', $normalized, $value);
            }
        }
        return implode("\r\n", $lines);
    } 


    private function filterHeader(string $header) : string
    {
        $filtered = str_replace('-', ' ', $header);
        $filtered = ucwords($filtered);
        return str_replace(' ', '-', $filtered);
    }










}
