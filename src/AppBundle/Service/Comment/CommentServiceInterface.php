<?php


namespace AppBundle\Service\Comment;


use AppBundle\Entity\Comment;

interface CommentServiceInterface
{
    public function create(Comment $comment, int $articleId): bool ;

    /**
     * @param int $articleId
     * @return Comment[]
     */
    public function getAllByArticle(int $articleId);

    public function getOne(): ?Comment;
}