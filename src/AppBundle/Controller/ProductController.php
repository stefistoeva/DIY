<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use AppBundle\Service\Products\ProductServiceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;

class ProductController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * ProductController constructor.
     * @param ProductServiceInterface $productService
     */
    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/product/add", name="product_add", methods={"GET"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            return $this->redirectToRoute("all_products");
        }

        return $this->render('products/add.html.twig',
            [
                'form' => $this
                    ->createForm(ProductType::class)
                    ->createView()
            ]);
    }

    /**
     * @Route("/product/add", methods={"POST"})
     *
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function createProcess(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $this->fileUpload($form, $product);

        $this->productService->create($product);
        $this->addFlash("info", "Add gift successfully!");

        return $this->redirectToRoute("all_products");
    }

    /**
     * @Route("/product/edit/{id}", name="product_edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        $product = $this->productService->getOne($id);

        if (null === $product || !$this->isAdmin()) {
            return $this->redirectToRoute("all_products");
        }

        return $this->render("products/edit.html.twig",
            [
                'form' => $this->createForm(ProductType::class)
                    ->createView(),
                'product' => $product
            ]);
    }

    /**
     * @Route("/product/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editProcess(Request $request, $id)
    {
        $product = $this->productService->getOne($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $this->fileUpload($form, $product);
        $this->productService->edit($product);

        return $this->redirectToRoute("product_view", [$id]);
    }


    /**
     * @Route("/product/delete/{id}", name="product_delete", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function delete(int $id)
    {
        $product = $this->productService->getOne($id);

        if (null === $product || !$this->isAdmin()) {
            return $this->redirectToRoute("all_products");
        }

        return $this->render("products/delete.html.twig",
            [
                'form' => $this->createForm(ProductType::class),
                'product' => $product
            ]);
    }

    /**
     * @Route("/product/delete/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $product = $this->productService->getOne($id);

        $form = $this->createForm(ProductType::class, $product);

        $form->remove('image');

        $form->handleRequest($request);
        $this->productService->delete($product);
        return $this->redirectToRoute("all_products");
    }


    /**
     * @Route("/product/{id}", name="product_view")
     * @param $id
     * @return Response
     */
    public function view($id)
    {
        $product = $this->productService->getOne($id);

        if (null === $product) {
            return $this->redirectToRoute("all_products");
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->render("products/view.html.twig",
            [
                'product' => $product
            ]);
    }


    /**
     * @Route("/products", name="all_products")
     *
     * @return Response
     */
    public function viewAllProducts()
    {
        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findBy([],
                [
                    'dateAdded' => 'DESC'
                ]);

        return $this->render('products/products.html.twig',
            [
                'products' => $products
            ]);
    }

    /**
     * @Route("/products/myProducts", name="my_products", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function getAllArticlesByUser()
    {
        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(['author' => $this->getUser()], ['dateAdded' => "DESC"]);

        if (!$this->isAdmin()) {
            return $this->redirectToRoute("all_products");
        }

        return $this->render(
            "products/myProducts.html.twig",
            [
                'products' => $products
            ]
        );
    }

    /**
     * @return bool
     */
    private function isAdmin()
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if (!$currentUser->isAdmin()) {
            return false;
        }
        return true;
    }

    /**
     * @param FormInterface $form
     * @param Product $product
     */
    private function fileUpload(FormInterface $form, Product $product)
    {
        /** @var UploadedFile $file */
        $file = $form['image']->getData();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        if ($file) {
            $file->move(
                $this->getParameter('products_directory'),
                $fileName
            );

            $product->setImage($fileName);
        }
    }
}
