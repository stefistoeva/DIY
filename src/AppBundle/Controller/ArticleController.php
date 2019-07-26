<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use AppBundle\Form\ArticleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * @Route("/article/create", name="article_create", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function create()
    {
        return $this->render("articles/create.html.twig",
            ['form' => $this
                ->createForm(ArticleType::class)
                ->createView()]);
    }

    /**
     * @Route("/article/create", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     */
    public function createProcess(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $article->setAuthor($this->getUser());
        $article->setViewCount(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        $this->addFlash("info", "Create article successfully!");
        return $this->redirectToRoute("all_articles");
    }

    /**
     * @Route("/article/edit/{id}", name="article_edit")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function edit(Request $request, $id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (null === $article) {
            return $this->redirectToRoute("all_articles");
        }

        if (!$this->isAuthorOrAdmin($article)) {
            return $this->redirectToRoute("all_articles");
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($article);
            $em->flush();

            return $this->redirectToRoute("all_articles");
        }

        return $this->render("articles/edit.html.twig",
            [
                'form' => $form->createView(),
                'article' => $article
            ]);
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function delete(Request $request, int $id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (null === $article) {
            return $this->redirectToRoute("all_articles");
        }

        if (!$this->isAuthorOrAdmin($article)) {
            return $this->redirectToRoute("all_articles");
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            return $this->redirectToRoute("all_articles");
        }

        return $this->render("articles/delete.html.twig",
            [
                'form' => $form->createView(),
                'article' => $article
            ]);
    }

    /**
     * @Route("/article/{id}", name="article_view")
     * @param $id
     * @return Response
     */
    public function view($id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        if (null === $article) {
            return $this->redirectToRoute("all_articles");
        }

        $article->setViewCount($article->getViewCount() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return $this->render("articles/view.html.twig", ['article' => $article]);
    }

    /**
     * @Route("/articles", name="all_articles")
     *
     * @return Response
     */
    public function viewAllArticles()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy([],
                [
                    'viewCount' => 'DESC',
                    'dateAdded' => 'DESC'
                ]);

        return $this->render('articles/articles.html.twig',
            ['articles' => $articles]);
    }

    /**
     * @param Article $article
     * @return bool
     */
    private function isAuthorOrAdmin(Article $article)
    {
        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();
        if (!$currentUser->isAuthor($article) && !$currentUser->isAdmin()) {
            return false;
        }

        return true;
    }

    /**
     * @Route("/articles/myArticles", name="my_articles")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function getAllArticlesByUser()
    {
        $articles = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(['author' => $this->getUser()], ['dateAdded' => "DESC"]);

        return $this->render(
            "articles/myArticles.html.twig",
            [
                'articles' => $articles
            ]
        );
    }
}
