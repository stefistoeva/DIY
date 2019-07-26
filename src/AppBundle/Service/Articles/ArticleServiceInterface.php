<?php


namespace AppBundle\Service\Articles;


use AppBundle\Entity\Article;

interface ArticleServiceInterface
{
    public function create(Article $article): bool ;
    public function edit(Article $article): bool ;
    public function delete(Article $article): bool ;
}