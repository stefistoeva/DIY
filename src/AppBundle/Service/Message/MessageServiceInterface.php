<?php


namespace AppBundle\Service\Message;


use AppBundle\Entity\Message;

interface MessageServiceInterface
{
    public function create(Message $message, int $recipientId): bool ;
}