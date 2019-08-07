<?php


namespace AppBundle\Service\Orders;


use AppBundle\Entity\Order;
use AppBundle\Repository\OrderRepository;
use AppBundle\Service\Products\ProductServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;
use Doctrine\ORM\ORMException;

class OrderService implements OrderServiceInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        OrderRepository $orderRepository,
        ProductServiceInterface $productService,
        UserServiceInterface $userService)
    {
        $this->orderRepository = $orderRepository;
        $this->productService = $productService;
        $this->userService = $userService;
    }

    /**
     * @param Order $order
     * @param int $id
     * @return bool
     * @throws ORMException
     */
    public function create(Order $order, int $id): bool
    {
        $product = $this->productService->getOneById($id);

        $order
            ->setCustomer($this->userService->currentUser())
            ->setProduct($product);

        $product->setIsDeleted(1);
        $this->productService->edit($product);

        return $this->orderRepository->insert($order);
    }
}