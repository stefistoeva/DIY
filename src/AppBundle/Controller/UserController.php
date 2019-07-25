<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $passwordHash =
                $this->get('security.password_encoder')
                    ->encodePassword($user, $user->getPassword());

            $roleUser = $this->getDoctrine()->getRepository(Role::class)
                ->findOneBy(['name' => 'ROLE_USER']);

            $user->addRole($roleUser);

            $user->setPassword($passwordHash);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("security_login");
        }
        return $this->render('users/register.html.twig',
            ['form' => $form->createView()]);
    }

    /**
     * @Route("/profile", name="user_profile")
     */
    public function profile()
    {
        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);

        $currentUser = $userRepository->find($this->getUser());

        return $this->render("users/profile.html.twig",
            ['user' => $currentUser]);
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
