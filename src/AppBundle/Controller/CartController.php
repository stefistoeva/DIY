<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShoppingCart;
use AppBundle\Service\Products\ProductServiceInterface;
use AppBundle\Service\ShoppingCart\ShoppingCartServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @var ShoppingCartServiceInterface
     */
    private $shoppingCartService;

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * ProductController constructor.
     * @param ShoppingCartServiceInterface $shoppingCartService
     * @param ProductServiceInterface $productService
     */
    public function __construct(ShoppingCartServiceInterface $shoppingCartService,
ProductServiceInterface $productService)
    {
        $this->shoppingCartService = $shoppingCartService;
        $this->productService = $productService;
    }

    /**
     * @Route("/cart/add", name="add_to_cart")
     *
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $articleId = $request->query->get('id');
        $product = $this->productService->getOne($articleId);

        $cart = $this->shoppingCartService->add($product);

        return $this->render('orders/create.html.twig');
    }
}
