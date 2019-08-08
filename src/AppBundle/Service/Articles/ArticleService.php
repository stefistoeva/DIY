<?php


namespace AppBundle\Service\Articles;


use AppBundle\Entity\Article;
use AppBundle\Repository\ArticleRepository;
use AppBundle\Service\Users\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMException;

class ArticleService implements ArticleServiceInterface
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(ArticleRepository $articleRepository, UserServiceInterface $userService)
    {
        $this->articleRepository = $articleRepository;
        $this->userService = $userService;
    }

    /**
     * @param Article $article
     * @return bool
     * @throws ORMException
     * @throws ORMException
     */
    public function create(Article $article): bool
    {
        $author = $this->userService->currentUser();
        $article->setAuthor($author);
        $article->setViewCount(0);

        return $this->articleRepository->insert($article);
    }

    /**
     * @param Article $article
     * @return bool
     * @throws ORMException
     */
    public function edit(Article $article): bool
    {
        return $this->articleRepository->update($article);
    }

    /**
     * @param Article $article
     * @return bool
     * @throws ORMException
     */
    public function delete(Article $article): bool
    {
        return $this->articleRepository->remove($article);
    }

    /**
     * @return ArrayCollection|Article[]
     */
    public function getAll()
    {
        return $this->articleRepository->findAll();
    }

    /**
     * @param int $id
     * @return Article|null|object
     */
    public function getOne(int $id): ?Article
    {
        return $this->articleRepository->find($id);
    }
}