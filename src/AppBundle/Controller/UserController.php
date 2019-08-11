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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

        try {
            $this->userService->validateLength($form);
            $this->userService->validatePasswords($form);
            $this->userService->isUniqueEmail($form);
        } catch (\Exception $ex) {
            $this->addFlash("defect", $ex->getMessage());
            return $this->redirectToRoute('user_register');
        }

        if ($form->isSubmitted()) {
            $this->userService->save($user);
            $this->addFlash("info", "Register successfully!");
            return $this->redirectToRoute("security_login");
        }

        return $this->redirectToRoute("user_register");
    }

    /**
     * @Route("/edit", name="user_edit", methods={"GET"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @return Response
     */
    public function edit()
    {
        $currentUser = $this->userService->currentUser();

        return $this->render('users/edit.html.twig',
            [
                'form' => $this->createForm(UserType::class)->createView(),
                'user' => $currentUser
            ]);
    }

    /**
     * @Route("/edit", methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @param $id
     * @return Response
     */
    public function editProcess(Request $request)
    {
        $user = $this->userService->currentUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove('password');
        $form->handleRequest($request);

        try {
            $this->userService->validateLength($form);
            $this->userService->isUniqueEmail($form, $user->getEmail());
        } catch (\Exception $ex) {
            $this->addFlash("mistake", $ex->getMessage());
            return $this->redirectToRoute('user_edit');
        }

        $this->addFlash("edit_profile", "Edit profile successfully!");
        $this->userService->edit($user);

        return $this->redirectToRoute("user_profile");
    }

    /**
     * @Route("/profile", name="user_profile")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
