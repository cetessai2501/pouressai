<?php
namespace App\Blog\Actions;

use Psr\Container\ContainerInterface;
use App\Auth\DatabaseAuth;
use Framework\Renderer\RenderInterface;
use Framework\Session\SessionInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Framework\Routery;
use App\Blog\Table\ProductTable;
use Framework\Actions\RouterAwareAction;
use Framework\Session\FlashService;
use App\Blog\BasketRow;

class BasketAction
{

private $container;
private $renderer;
private $router;
private $auth;
private $table;
public $rows = [];

    /**
     * @var SessionInterface
     */
private $session;

use RouterAwareAction;

public function __construct(
        ContainerInterface $container,
        RenderInterface $renderer,
        Routery $router,
        DatabaseAuth $auth,
        SessionInterface $session,
        ProductTable $table 
    ) {
        $this->container = $container;
        $this->items      = array();
        $this->itoms      = array();
        $this->renderer = $container->get('shop');
        $this->auth = $auth;
        $this->router = $container->get('\Framework\Routery');
        $this->session = $session;
        $this->table = $table;
        if (($this->session->get('panier') !== null) && count($this->session->get('panier')) > 0) {
        $rows = $this->session->get('panier');
        $this->rows = array_map(function ($row) {
            $r = new BasketRow();
            $r->setProductId($row['id']);
            $r->setProduct($row['product']);
            $r->setQuantity($row['quantity']);
            //$r->setCategoryId($row['cat_id']);
            //$r->ruws = $this->rows;
            //$r->setCategoryId($row['cat_id']);
            return $r;
        }, $rows); 
        



        }

    }

/**
 * pandelete
 *
 * @param Request $request
 * @return void
 */
public function pandelete(Request $request)
    {

$id = intval($request->getParsedBody()['produit']);

if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {
unset($_SESSION['panier'][$id]);

}


return $this->redirect('panierAction'); 

    }

public function panierAction(Request $request)
    {
        
        
        
        if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {
        //unset($_SESSION['panier']); 
        
        $products = $this->table->findArray(array_keys($this->session->get('panier')));
        $prods =  array_map(function ($row) {
            return $row->id;
        }, $products);
        $monessai =  implode ("," , $prods );
        
        $panier = $this->session->get('panier');
        $price =  array_reduce($panier, function ($total, $row) {
            return $row['quantity'] * $row['price'] + $total;
        }, 0);
        return $this->renderer->render('panier', [ 'produitsids' => $monessai, 'price' => $price, 'products' => $products, 'rows' => $this->rows, 'panier' => $this->session->get('panier')  ,'router' => $this->router]); 
        }else{
        return $this->redirect('bout'); 

        }

        
       //$products = $this->table->findArray(array_keys($this->session->get('panier')));
                    

    }

 public function ajouterAction(Request $request)
    {
        //var_dump($request->getParsedBody()['product']);
        
        $id = intval($request->getParsedBody()['product']);
        $product = $this->table->find($id);
        $this->itoms = $product;
        if (!$this->session->get('panier')) $_SESSION['panier'] = array();
        //if (!$this->session->get('panier')) $this->session->set('panier', ['sessionid' => 1]['id'  => intval($request->getParsedBody()['product']), 'quantity' => $request->getParsedBody()['qte']]);
        $_SESSION['panier'][$product->id] = array('product' => $product, 'price' => $product->price, 'id' => $product->id, 'name' => $product->name, 'quantity' => $request->getParsedBody()['qte']);    
        //$session->set('panier', $panier); 
        //return $this->redirect($this->generateUrl('ecommerce_panier'));
        //if (!$session->has('qte')) $session->set('qte', array()); 
        
        
        
        
        
        
        return $this->redirect('panierAction');
    }

    public function change(Request $request)
    {
            $qte = $request->getParsedBody()['quantity'];
            $product = $this->table->find(intval($request->getAttribute('id')));
            if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {
            $_SESSION['panier'][$product->id] = array('product' => $product, 'price' => $product->price, 'id' => $product->id, 'name' => $product->name, 'quantity' => $qte);  

            } 
            return $this->redirect('panierAction');
            
    }
    






}
