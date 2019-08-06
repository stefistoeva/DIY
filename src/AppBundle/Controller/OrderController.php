<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Form\OrderType;
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

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/order/create", name="create_order", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $articleId = $request->query->get('id');
        $product = $this->productService->getOne($articleId);

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
     * @Route("/order/create", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function createProcess(Request $request)
    {
        var_dump($request);exit;
        $product = $this->productService->getOne($id);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        $order
            ->setBuyer($this->getUser())
            ->setProduct($product);

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $product->setIsDeleted(1);

        $this->addFlash("info", "Create order successfully!");

        return $this->redirectToRoute("user_profile",
            [
//                'id' => $product->getId()
            ]);
    }

//    /**
//     * @Route("/order/create", name="create_order", methods={"POST"})
//     * @param Request $request
//     * @param $id
//     * @return Response
//     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
//     */
//    public function create(Request $request, $id)
//    {
//        $product = $this->productService->getOne($id);
//        $order = new Order();
//        $form = $this->createForm(OrderType::class, $order);
//        $form->handleRequest($request);
//
//        $order
//            ->setBuyer($this->getUser())
//            ->setProduct($product);
//
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($order);
//        $em->flush();
//
//        $product->setIsDeleted(1);
//
//        return $this->redirectToRoute("user_profile",
//            [
////                'id' => $product->getId()
//            ]);
//    }

}
