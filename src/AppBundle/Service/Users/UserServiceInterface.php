<?php


namespace AppBundle\Service\Users;


use AppBundle\Entity\User;
use Symfony\Component\Form\FormInterface;

interface UserServiceInterface
{
    public function findOneByEmail(string $email): ?User;
    public function save(User $user): bool ;
    public function edit(User $product): bool ;
    public function findOneById(int $id): ?User;
    public function findOne(User $user): ?User;
    public function currentUser(): ?User;
    public function validateLength(FormInterface $form): bool;
    public function validatePasswords(FormInterface $form): bool;
    public function isUniqueEmail(FormInterface $form, $data = null): bool;

}