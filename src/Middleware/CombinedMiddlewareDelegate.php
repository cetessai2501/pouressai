<?php

namespace Framework\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Container\ContainerInterface;
use GuzzleHttp\Psr7\Response;
use DebugBar\JavascriptRenderer as DebugBarRenderer;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Stream;
use DebugBar\StandardDebugBar;
use DebugBar\DataCollector\AggregatedCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\PDOCollector;
use Barryvdh\Debugbar\LaravelDebugbar;

class CombinedMiddlewareDelegate implements RequestHandlerInterface
{
    private $container;
 
    public $middlewares = [];

    private $index = 0;

    private $delegate;

    public $debugbar; 

    public const FORCE_KEY = 'X-Enable-Debug-Bar';

    private $debugBarRenderer;
    private $responseFactory;

    public function __construct(ContainerInterface $container, array $middlewares, RequestHandlerInterface $delegate, DebugBarRenderer $debugbarRenderer, StandardDebugBar  $debugbar )
    {
         
          $this->middlewares = $middlewares;
          $this->container = $container;
          $this->delegate = $delegate;
          $this->debugbar = $this->container->get('debugbar');
          $this->debugBarRenderer = $this->debugbar->getJavascriptRenderer()
                             ->setBaseUrl('/Resources')
                             ->setEnableJqueryNoConflict(false)
                             ->addControl('mess', array( "widget" => "PhpDebugBar.Widgets.MessagesWidget", "map" => "mess", "default" => "[]"));
          
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler = null): ResponseInterface
    {
        
        if ($staticFile = $this->getStaticFile($request->getUri())) {
            return $staticFile;
        }
        $enabled = false;

        $middleware = $this->getMiddleware();
//var_dump($middleware);


        $forceHeaderValue = $request->getHeaderLine(self::FORCE_KEY);
        $forceCookieValue = $request->getCookieParams()[self::FORCE_KEY] ?? '';
        $forceAttibuteValue = $request->getAttribute(self::FORCE_KEY, '');
        $isForceEnable = in_array('true', [$forceHeaderValue, $forceCookieValue, $forceAttibuteValue], true);
        $isForceDisable = in_array('false', [$forceHeaderValue, $forceCookieValue, $forceAttibuteValue], true);

        if (is_null($middleware)) {


            return $this->delegate->process($request); 
        } elseif ($middleware instanceof MiddlewareInterface) {

            return $middleware->process($request, $this);        //test
        
 //test
        } elseif (is_callable($middleware)) {
            //$controller = $request->getAttribute('Framework\Router\Route')->getCallback();

        
             
            //call_user_func_array(__NAMESPACE__ .'\Controller::aliasAction',['about']);
            //call_user_func_array(__NAMESPACE__ .'\Controller::aliasAction',['about']);
            $response = call_user_func_array($middleware, [$request,  [$this, 'process']]);

if (is_string($response)) {
                $respi = new Response(200, [], $response);

                return $respi;       //test
            }
        if ($enabled === false) {    
           
            return $response;
         }else{
//var_dump($this->debugbar->getCollector('pdo')->getConnections()['default']->getExecutedStatements());
             $this->debugbar['messages']->addMessage('hello');
             
             //$this->debugbar->addCollector(new \DebugBar\DataCollector\ConfigCollector($data));   
//$myLogger = new \RedBeanPHP\Logger\RDefault;
             //$this->debugbar->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($pdo)); 







//$debugbar->addCollector(new \Filisko\DebugBar\DataCollector\RedBeanCollector($container->get(\RedBeanPHP\Logger::class)));

           return $this->attachDebugBarToResponse($response);
           //return $response;
          
            
        }
        //return $this->prepareHtmlResponseWithDebugBar($response);

        }  

    }

    private function prepareHtmlResponseWithDebugBar(Response $response): Response
    {

        
        $bodi = new Stream(fopen('php://temp', 'r+'));

 
        $head = $this->debugBarRenderer->renderHead();
        $body = $this->debugBarRenderer->render();

        $outResponseBody = $this->serializeResponse($response);

        $template = '<html><head>%s</head><body><h1>DebugBar</h1><p>Response:</p><pre>%s</pre>%s</body></html>';
        $escapedOutResponseBody = htmlspecialchars($outResponseBody);
        $result = sprintf($template, $head, $escapedOutResponseBody, $body);
        //$body = new Stream(fopen('php://temp', 'r+'));
        $bodi->write($outResponseBody);

        return (new Response())
            ->withStatus(200)
            
            ->withBody($bodi);
        //$stream = new Stream($result);
        //return new Response(200, [], $outResponseBody );
        
    }



    private function serializeResponse(Response $response) : string
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


    private function attachDebugBarToResponse(Response $response): Response
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


    private function getStaticFile(UriInterface $uri)
    {
        $path = $this->extractPath($uri);


        
        
    }

        private function extractPath(UriInterface $uri): string
    {
        // Slim3 compatibility
            if ($uri instanceof UriInterface) {
            $basePath = $uri->getPath();
            if (!empty($basePath)) {
                return $basePath;
            }
        }
        return $uri->getPath();
    }

    public function isHtmlResponse($response): bool
    {
        if($response instanceof ResponseInterface){
        return true;
        } 
        return false; 

    } 


        private function isRedirect(Response $response): bool
    {
        $statusCode = $response->getStatusCode();
        return ($statusCode >= 300 || $statusCode < 400) && $response->getHeaderLine('Location') !== '';
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

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
           
           
var_dump($request);
die();
           return $middleware->process($request);
         //die();

        
    }

    public function getContainer()
    {
             return $this->container;
    }

    /**
     *
     */
    public function getMiddleware()
    {
        if (array_key_exists($this->index, $this->middlewares)) {
            if (is_string($this->middlewares[$this->index])) {
                $middleware = $this->container->get($this->middlewares[$this->index]);
            } else {
                $middleware = $this->middlewares[$this->index];
            }
                 $this->index++;
                return $middleware;
        }
                return null;
    }
}
