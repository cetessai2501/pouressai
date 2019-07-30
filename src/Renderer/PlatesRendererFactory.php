<?php
namespace Framework\Renderer;

use League\Plates\Engine;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

class PlatesRendererFactory
{
    
    


    public function __invoke(ContainerInterface $container): PlatesRenderer
    {
        $viewPath = $container->get('views.path');
        //var_dump($this->settings);
        //$renderer = new \Framework\Renderer\PlatesRenderer('/home/sophie/monp/src/Blog/views');
        $templates = $container->get('home');
        //$loader = new \Twig_Loader_Filesystem($viewPath);
        //$twig = new \Twig_Environment($loader);
        //if ($container->has('twig.extensions')) {
            //foreach ($container->get('twig.extensions') as $extension) {
                //$twig->addExtension($extension);
            //}
        //}
        return new PlatesRenderer($templates);
    }
}
