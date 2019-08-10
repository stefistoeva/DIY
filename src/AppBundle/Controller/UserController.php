<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\Message\MessageServiceInterface;
use AppBundle\Service\Orders\OrderServiceInterface;
use AppBundle\Service\Products\ProductServiceInterface;
use AppBundle\Service\Users\UserServiceInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var MessageServiceInterface
     */
    private $messageService;

    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    public function __construct(
        UserServiceInterface $userService,
        MessageServiceInterface $messageService,
        OrderServiceInterface $orderService)
    {
        $this->userService = $userService;
        $this->messageService = $messageService;
        $this->orderService = $orderService;
    }

    /**
     * @Route("/register", name="user_register", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        return $this->render('users/register.html.twig',
            ['form' => $this->createForm(UserType::class)->createView()]);
    }

    /**
     * @Route("/register", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if (null !== $this->userService->findOneByEmail($form->getData()->getEmail())){
         $this->addFlash('same','Email already exists!');
        return $this->render('users/register.html.twig',
            ['user'=>$user,'form'=>$this->createForm(UserType::class)->createView()]);
         }

        $this->userService->save($user);
        $this->addFlash("info", "Register successfully!");
        return $this->redirectToRoute("security_login");
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile()
    {
        if (empty($this->orderService->getAllByUser())) {
            $this->addFlash("no_order", "You doesn't have any orders!");
        }

        return $this->render("users/profile.html.twig",
            [
                'user' => $this->userService->currentUser(),
                'msg' => $this->messageService->getAllUnseenByUser(),
                'orders' => $this->orderService->getAllByUser(),
            ]);
    }

    /**
     * @Route("/logout", name="security_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception("Logout failed!");
    }
}
