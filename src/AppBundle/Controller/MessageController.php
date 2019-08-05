<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Entity\Message;
use AppBundle\Form\MessageType;
use AppBundle\Service\Articles\ArticleServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    public function __construct(ArticleServiceInterface $articleService)
    {
        $this->articleService = $articleService;
    }

    /**
     * @Route("/comment/create/{id}", name="message_create", methods={"POST"})
     * @param Request $request
     * @param $id
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function create(Request $request, $id)
    {
        $article = $this->articleService->getOne($id);
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $message
            ->setAuthor($this->getUser())
            ->setArticle($article);

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute("article_view",
            [
                'id' => $article->getId()
            ]);
    }
}
