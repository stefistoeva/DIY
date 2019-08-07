<?php


namespace AppBundle\Service\Message;


use AppBundle\Entity\Message;
use AppBundle\Repository\MessageRepository;
use AppBundle\Service\Users\UserServiceInterface;
use Doctrine\ORM\ORMException;

class MessageService implements MessageServiceInterface
{
    /**
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(MessageRepository $messageRepository, UserServiceInterface $userService)
    {
        $this->messageRepository = $messageRepository;
        $this->userService = $userService;
    }


    /**
     * @param Message $message
     * @param int $recipientId
     * @return bool
     * @throws ORMException
     */
    public function create(Message $message, int $recipientId): bool
    {
        $sender = $this->userService->currentUser();
        $recipient = $this->userService->findOneById($recipientId);
        $message
            ->setSender($sender)
            ->setRecipient($recipient);

        return $this->messageRepository->insert($message);
    }
}