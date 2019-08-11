<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use AppBundle\Service\Articles\ArticleServiceInterface;
use AppBundle\Service\Comment\CommentServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends Controller
{
    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    /**
     * @var CommentServiceInterface
     */
    private $commentService;

    public function __construct(
        ArticleServiceInterface $articleService,
        CommentServiceInterface $commentService)
    {
        $this->articleService = $articleService;
        $this->commentService = $commentService;
    }

    /**
     * @Route("/comment/create/{id}", name="comment_create", methods={"POST"})
     * @param Request $request
     * @param $id
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function create(Request $request, $id)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->addFlash("comment", "Comment created successfully.");

            $this->commentService->create($comment, $id);
        }

        return $this->redirectToRoute("article_view",
            [
                'id' => $id
            ]);
    }
}
