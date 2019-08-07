<?php

namespace AppBundle\Controller;

use AppBundle\Form\MessageType;
use AppBundle\Service\Message\MessageServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;
use AppBundle\Entity\Message;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends Controller
{
    /**
     * @var MessageServiceInterface
     */
    private $messageService;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(
        UserServiceInterface $userService,
        MessageServiceInterface $messageService)
    {
        $this->userService = $userService;
        $this->messageService = $messageService;
    }

    /**
     * @Route("/user/{id}/message", name="user_message", methods={"GET"})
     *
     * @param $id
     * @return Response
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function create($id)
    {
        return $this->render('users/message.html.twig',
            [
                'user' => $this->userService->findOneById($id)
            ]);
    }

    /**
     * @Route("/user/{id}/message", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function createProcess(Request $request, $id)
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $message->setIsRead(0);
        $form->handleRequest($request);
        $this->messageService->create($message, $id);
        $this->addFlash("message", "Message sent successfully!");
        return $this->redirectToRoute("user_message",
            [
                'id' => $id
            ]);
    }

    /**
     * @Route("/user/mailbox", name="user_mailbox", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getAllByUser()
    {
        return $this->render("users/mailbox.html.twig",
            [
                'messages' => $this->messageService->getAllByUser()
            ]);
    }

    /**
     * @Route("/user/mailbox/message/{id}", name="user_mailbox_message", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param $id
     * @return Response
     */
    public function view($id)
    {
        $message = $this->messageService->getOne($id);
        $message->setIsRead(1);
        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();
        return $this->render("messages/view.html.twig",
            [
                'msg' => $message
            ]);
    }

    /**
     * @Route("/user/mailbox/message/{id}", name="user_mailbox_sent_message", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function sentMessageToRecipient(Request $request, int $id)
    {
        $message = new Message();
        $message->setIsRead(0);
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $this->messageService->create($message, $id);
        $this->addFlash("answer", "Message sent successfully!");
        return $this->redirectToRoute("user_message",
            [
                'id' => $this->userService->currentUser()->getId()
            ]);
    }
}
