<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
use AppBundle\Service\Orders\OrderServiceInterface;
use AppBundle\Service\Products\ProductServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    public function __construct(ProductServiceInterface $productService, OrderServiceInterface $orderService)
    {
        $this->productService = $productService;
        $this->orderService = $orderService;
    }

    /**
     * @Route("/product/{id}/order", name="create_order", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function create($id)
    {
        $product = $this->productService->getOneById($id);

        if (null === $product) {
            return $this->redirectToRoute("all_products");
        }

        return $this->render("orders/create.html.twig",
            [
                'form' => $this
                    ->createForm(OrderType::class)
                    ->createView(),
                'product' => $product,
            ]
        );
    }

    /**
     * @Route("/product/{id}/order", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function createProcess(Request $request, int $id)
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        $this->orderService->create($order, $id);
        $this->addFlash("order", "Create order successfully!");

        return $this->redirectToRoute("product_view",
            [
                'id' => $id
            ]);
    }

    /**
     * @Route("/user/allOrder", name="seller_orders", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getAllByAuthor()
    {
        $orders = $this->orderService->getAllByAuthor();

        if (empty($orders)) {
            $this->addFlash("seller", "You doesn't have any orders!");
        }

        return $this->render("orders/sellerOrders.html.twig",
            [
                'orders' => $orders
            ]);
    }}
