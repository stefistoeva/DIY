<?php


namespace AppBundle\Service\Message;


use AppBundle\Entity\Message;
use Doctrine\Common\Collections\ArrayCollection;

interface MessageServiceInterface
{
    public function create(Message $message, int $senderId): bool ;

    public function getAllByUser();

    public function getOne(int $id): ?Message;

    public function getAllUnseenByUser();
}