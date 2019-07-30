<?php

namespace App\Blog\Actions;

use Psr\Container\ContainerInterface;
use App\Blog\Entity\Product;
use App\Blog\Exception\AlreadyPurchasedException;
use App\Blog\Purchase;
use App\Blog\Table\ProductTable;
use Framework\Actions\RouterAwareAction;
use Framework\Auth;
use Framework\Renderer\RenderInterface;
use Framework\Response\RedirectResponse;
use Framework\Routery;
use Framework\Session\FlashService;
use Psr\Http\Message\ServerRequestInterface;
use GuzzleHttp\Psr7\Response;

class PurchaseProcessAction
{
    private $container;
    /**
     * @var ProductTable
     */
    private $productTable;
    /**
     * @var PurchaseProduct
     */
    private $purchaseProduct;
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var Router
     */
    private $router;
    /**
     * @var FlashService
     */
    private $flashService;

    use RouterAwareAction;

    public function __construct(
        ContainerInterface $container,
        ProductTable $productTable,
        Purchase $purchaseProduct,
        Auth $auth,
        Routery $router,
        FlashService $flashService
    ) {
        $this->container = $container;
        $this->productTable = $productTable;
        $this->purchaseProduct = $purchaseProduct;
        $this->auth = $auth;
        $this->router = $container->get('\Framework\Routery');
        $this->flashService = $flashService;
    }

    public function commander(ServerRequestInterface $request)
    {
        /** @var Product $product */
       $params = $request->getParsedBody();
       $price = floatval($params['price']);
        $produits = $params['produits'];
        $pieces = explode(",", $produits);
        $products = $this->productTable->findString($params['produits']);
        $stripeToken = $params['stripeToken'];
        $already = $this->purchaseProduct->getTable()->findForUser($this->auth->getUser());
        

        try {
        $all = $this->purchaseProduct->getTable()->findArray($pieces , $this->auth->getUser()->getId());
        } catch (\Exception $e) {
            $this->flashService->error('oups désolé !!!......... , mais vous avez déjà acheté ce(s) produit(s) !!!!');
            return $this->redirect('bout');
        }

        try {
            foreach($products as $product){
            $this->purchaseProduct->process($product, $this->auth->getUser(), $stripeToken, $price);
            }
            $this->flashService->success('Merci pour votre achat');
            return $this->redirect('bout');
        } catch (\Exception $e) {
            return $this->redirect('panierAction');
        }
        
            
        
    }
}
