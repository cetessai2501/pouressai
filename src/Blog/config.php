<?php

use App\Blog\BlogModule;
use function \Di\object;
use function \Di\get;

return [
    'admin.prefix' => '/admin',
    'admin.widgets' => \DI\add([
        get(\App\Blog\BlogWidget::class)
    ]),
    'blog.path' => '/home/sophie/monp/src/Blog/views',
    'dispatcher' => function (\Psr\Container\ContainerInterface $container) {
             return \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r){
    $r->addRoute('GET', '/users', 'get_all_users_handler');

       });
     }, 
    BlogModule::class => object()->constructorParameter('prefix', get('blog.prefix'))->constructorParameter('path', \DI\get('blog.path'))
    

    
];
