<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends Controller
{
    /**
     * @Route("/products", name="all_products")
     *
     * @return Response
     */
    public function viewAllArticles()
    {
//        $articles = $this
//            ->getDoctrine()
//            ->getRepository(Article::class)
//            ->findBy([],
//                [
//                    'viewCount' => 'DESC',
//                    'dateAdded' => 'DESC'
//                ]);

        return $this->render('products/products.html.twig');
    }
}
