<?php


namespace AppBundle\Service\Articles;


use AppBundle\Entity\Article;

class ArticleService implements ArticleServiceInterface
{

    public function create(Article $article): bool
    {
        return true;
    }

    public function edit(Article $article): bool
    {
        return true;
    }

    public function delete(Article $article): bool
    {
        return true;
    }
}