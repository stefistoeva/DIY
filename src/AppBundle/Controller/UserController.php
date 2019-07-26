<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
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

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
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
        $this->userService->save($user);
        $this->addFlash("info", "Register successfully!");
        return $this->redirectToRoute("security_login");
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile()
    {
        return $this->render("users/profile.html.twig",
            ['user' => $this->userService->currentUser()]);
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
