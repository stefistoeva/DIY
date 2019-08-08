<?php


namespace AppBundle\Service\Orders;


use AppBundle\Entity\Order;

interface OrderServiceInterface
{
    public function create(Order $order, int $id): bool ;

    public function getAllByUser();
}