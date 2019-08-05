<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Form\ArticleType;
use AppBundle\Service\Articles\ArticleServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends Controller
{
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    /**
     * ArticleController constructor.
     * @param ArticleServiceInterface $articleService
     */
    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

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
        $this->fileUpload($form, $article);

        $this->articleService->create($article);

        $this->addFlash("info", "Create article successfully!");
        return $this->redirectToRoute("all_articles");
    }

    /**
     * @Route("/article/edit/{id}", name="article_edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function edit($id)
    {
        $article = $this->articleService->getOne($id);

        if (null === $article) {
            return $this->redirectToRoute("all_articles");
        }

        if (!$this->isAuthorOrAdmin($article)) {
            return $this->redirectToRoute("all_articles");
        }

        return $this->render("articles/edit.html.twig",
            [
                'form' => $this->createForm(ArticleType::class)
                    ->createView(),
                'article' => $article
            ]);
    }

    /**
     * @Route("/article/edit/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editProcess(Request $request, $id)
    {
        $article = $this->articleService->getOne($id);
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $this->fileUpload($form, $article);
        $this->articleService->edit($article);

        return $this->redirectToRoute("all_articles");
    }

    /**
     * @Route("/article/delete/{id}", name="article_delete", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function delete(int $id)
    {
        $article = $this->articleService->getOne($id);

        if (null === $article) {
            return $this->redirectToRoute("all_articles");
        }

        if (!$this->isAuthorOrAdmin($article)) {
            return $this->redirectToRoute("all_articles");
        }

        return $this->render("articles/delete.html.twig",
            [
                'form' => $this->createForm(ArticleType::class),
                'article' => $article
            ]);
    }

    /**
     * @Route("/article/delete/{id}", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function deleteProcess(Request $request, int $id)
    {
        $article = $this->articleService->getOne($id);

        $form = $this->createForm(ArticleType::class, $article);

        $form->remove('image');

        $form->handleRequest($request);
        $this->articleService->delete($article);
        return $this->redirectToRoute("all_articles");
    }

    /**
     * @Route("/article/{id}", name="article_view")
     * @param $id
     * @return Response
     */
    public function view($id)
    {
        $article = $this->articleService->getOne($id);

        if (null === $article) {
            return $this->redirectToRoute("all_articles");
        }

        $article->setViewCount($article->getViewCount() + 1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        $comments = $this
            ->getDoctrine()
            ->getRepository(Message::class)
            ->findBy(['article' => $article], ['dateAdded' => 'DESC']);

        return $this->render("articles/view.html.twig",
            [
                'article' => $article,
                'comments' => $comments
            ]);
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

    /**
     * @param FormInterface $form
     * @param Article $article
     */
    private function fileUpload(FormInterface $form, Article $article)
    {
        /** @var UploadedFile $file */
        $file = $form['image']->getData();
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        if ($file) {
            $file->move(
                $this->getParameter('articles_directory'),
                $fileName
            );

            $article->setImage($fileName);
        }
    }
}
