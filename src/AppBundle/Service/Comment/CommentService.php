<?php


namespace AppBundle\Service\Comment;


use AppBundle\Entity\Comment;
use AppBundle\Repository\CommentRepository;
use AppBundle\Service\Articles\ArticleServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;

class CommentService implements CommentServiceInterface
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var CommentRepository
     */
    private $commentRepository;

    /**
     * @var ArticleServiceInterface
     */
    private $articleService;

    public function __construct(
        CommentRepository $commentRepository,
        UserServiceInterface $userService,
        ArticleServiceInterface $articleService)
    {
        $this->commentRepository = $commentRepository;
        $this->userService = $userService;
        $this->articleService = $articleService;
    }

    /**
     * @param int $articleId
     * @return Comment[]
     */
    public function getAllByArticle(int $articleId)
    {
        $article = $this->articleService->getOne($articleId);

        return $this
            ->commentRepository
            ->findBy(
                [
                    'article' => $article
                ],
                [
                    'dateAdded' => 'DESC'
                ]);
    }

    public function getOne(): ?Comment
    {
        // TODO: Implement getOne() method.
    }

    /**
     * @param Comment $comment
     * @param int $articleId
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     */
    public function create(Comment $comment, int $articleId): bool
    {
        $comment
            ->setAuthor($this->userService->currentUser())
            ->setArticle($this->articleService->getOne($articleId));

        return $this->commentRepository->insert($comment);
    }
}