<?php

namespace App\Blog\Actions;

use Framework\Session\SessionInterface;
use App\Blog\Entity\Product;
use App\Blog\Table\ProductTable;
use Framework\Api\Stripe;
use Framework\Renderer\RenderInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Blog\BasketRow;
use Psr\Container\ContainerInterface;

class OrderRecapAction
{
    private $container; 
    /**
     * @var RendererInterface
     */
    private $renderer;

    /**
     * @var Stripe
     */
    private $stripe;
    /**
     * @var BasketTable
     */
    private $basketTable;
    /**
     * @var Basket
     */
    private $basket;

    public function __construct(
        ContainerInterface $container,
        RenderInterface $renderer,
        SessionInterface $session,
        Stripe $stripe

    ) {
        $this->container = $container;
        $this->stripe = $stripe;
        
        $this->session = $session;
        $this->renderer = $container->get('shop');  
    }

    public function recap(ServerRequestInterface $request)
    {
        $params = $request->getParsedBody();

        $params = $request->getParsedBody();

        $stripeToken = $params['stripeToken'];
        $this->stripe->setToken($stripeToken);
        $price = $params['price'];
        $produits = $params['produits'];
        $card = $this->stripe->getCardFromToken($stripeToken);
        $vat = 20.6;
        if (isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {
           $panier = $this->session->get('panier');
        }  
        $basket = array_map(function ($row) {
            $r = new BasketRow();
            $r->setProductId($row['id']);
            $r->setProduct($row['product']);
            $r->setQuantity($row['quantity']);
            //$r->setCategoryId($row['cat_id']);
            //$r->ruws = $this->rows;
            //$r->setCategoryId($row['cat_id']);
            return $r;
        }, $panier);

        //$this->basketTable->hydrateBasket($basket);
        //$price = floor($basket->getPrice() * (($vat + 100) / 100));
        return $this->renderer->render('basketrecap', compact(
            'basket',
            'produits',
            'vat',
            'stripeToken',
            'price',
            'card'
        ));
    }
}
