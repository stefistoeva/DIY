<?php


namespace AppBundle\Service\Products;


use AppBundle\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;

interface ProductServiceInterface
{
    /**
     * @return ArrayCollection|Product[]
     */
    public function getAll();
    public function create(Product $product): bool ;
    public function edit(Product $product): bool ;
    public function delete(Product $product): bool ;
    public function getOneById(int $id): ?Product;

    /**
     * @param $sellerUser
     * @return ArrayCollection|Product[]
     */
    public function getAllByAuthor($sellerUser);
}