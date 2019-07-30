<?php
namespace Framework\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use GuzzleHttp\Psr7\Response;
use Framework\App;
use Psr\Http\Message\ResponseInterface;

class AppMiddleware extends App
{

    public function run(ServerRequestInterface $request): ResponseInterface
    {
       
 foreach ($this->modules as $module) {
            $routes = $this->getContainer()->get($module)->getRoutes();

            $name =  $this->getContainer()->get($module)->getName();


            $collector = $this->router->getRouter();

            foreach($routes as $route){

              
               $this->routes[$route->getMethod()][] = $route;

               
            }

            $this->getContainer()->get($module);

            //var_dump($this->routes);
            if(!empty($this->routes)){

            $this->router->setCollection($this->routes);
            $this->router->setBasePath('http://localhost:8000');

            foreach($this->routes[$_SERVER['REQUEST_METHOD']] as $route){




             if($route->match($_SERVER['REQUEST_URI'], $request)){
                $pieces = explode("/", $request->getRequestTarget());  
if(!$route->getMiddleware() === false){


if(isset($pieces[3])){
                //$slug = $pieces[2];
                $id =  $pieces[3];

                $params = ['id' => intval($id)];
                $route->setParams($params);
 $paramis = $route->getParams(); 
$request = array_reduce(array_keys($paramis), function ($request, $key) use ($paramis) {
            return $request->withAttribute($key, $paramis[$key]);
        }, $request);
}
$request = $request->withAttribute(get_class($route), $route);
try {
            return $route->getMiddleware()->process($request, $this);
        } catch (ForbiddenException $exception) {
           
            return (new Response())
            ->withStatus(200)
            ->withHeader('location', '/login');
        } catch (\TypeError $error) {
            if (strpos($error->getMessage(), \Framework\Auth\User::class) !== false) {
                return $this->redirectLogin($request);
            }
            throw $error;
        }
//$this->process($request, $this);
//return $route->getMiddleware()->process($request, $this);
//var_dump($route->getMiddleware());
}

//$this->pipe($route->getMiddleware());

                if(isset($pieces[2]) && isset($pieces[3])){
                $slug = $pieces[2];
                $id =  $pieces[3];

                $params = ['id' => intval($id), 'slug' => $slug];
                $route->setParams($params);
                $paramis = $route->getParams(); 

                $this->router->setMatched($route);
        $request = array_reduce(array_keys($paramis), function ($request, $key) use ($paramis) {
            return $request->withAttribute($key, $paramis[$key]);
        }, $request);
        }
        $request = $request->withAttribute(get_class($route), $route);

                //return  call_user_func_array([$route->getCallBack(), 'handle'], [$request]);
                
                //return $this->process($request, $this);
                
             }
           
          } 
            } 
            //$this->getContainer()->get('\Framework\Routery');
        }

        return $this->process($request, $this);
         



    }


}
