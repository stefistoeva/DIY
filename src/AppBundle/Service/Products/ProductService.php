<?php


namespace AppBundle\Service\Products;


use AppBundle\Entity\Product;
use AppBundle\Repository\ProductRepository;
use AppBundle\Service\Users\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;

class ProductService implements ProductServiceInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(ProductRepository $productRepository, UserServiceInterface $userService)
    {
        $this->productRepository = $productRepository;
        $this->userService = $userService;
    }

    /**
     * @return ArrayCollection|Product[]
     */
    public function getAll()
    {
        return $this->productRepository->findAll();
    }

    /**
     * @param Product $product
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(Product $product): bool
    {
        $author = $this->userService->currentUser();
        $product->setAuthor($author);

        return $this->productRepository->insert($product);
    }

    /**
     * @param Product $product
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function edit(Product $product): bool
    {
        return $this->productRepository->update($product);
    }

    /**
     * @param Product $product
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function delete(Product $product): bool
    {
        return $this->productRepository->remove($product);
    }

    /**
     * @param int $id
     * @return Product|null|object
     */
    public function getOne(int $id): ?Product
    {
        return $this->productRepository->find($id);
    }
}