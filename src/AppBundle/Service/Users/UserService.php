<?php


namespace AppBundle\Service\Users;


use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\Encryption\ArgonEncryption;
use AppBundle\Service\Encryption\EncryptionServiceInterface;
use AppBundle\Service\Roles\RoleServiceInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;

class UserService implements UserServiceInterface
{
    private $security;
    private $userRepository;
    private $encryptionService;
    private $roleService;

    public function __construct(Security $security,
                                UserRepository $userRepository,
                                ArgonEncryption $encryptionService,
                                RoleServiceInterface $roleService)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->encryptionService = $encryptionService;
        $this->roleService = $roleService;
    }

    /**
     * @param string $email
     * @return User|null|object
     */
    public function findOneByEmail(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function save(User $user): bool
    {
        $passwordHash =
            $this->encryptionService->hash($user->getPassword());
        $user->setPassword($passwordHash);

        $userRole = $this->roleService->findOneBy("ROLE_USER");
        $user->addRole($userRole);

        return $this->userRepository->insert($user);
    }

    /**
     * @param int $id
     * @return User|null|object
     */
    public function findOneById(int $id): ?User
    {
        return $this->userRepository->findOneBy(['id' => $id]);
    }

    /**
     * @param User $user
     * @return User|null|object
     */
    public function findOne(User $user): ?User
    {
        return $this->userRepository->find($user);
    }

    /**
     * @return User|null|object
     */
    public function currentUser(): ?User
    {
        return $this->security->getUser();
    }

    /**
     * @param FormInterface $form
     * @param array|null $data
     * @return bool
     * @throws \Exception
     */
    public function validateLength(FormInterface $form): bool
    {
        if (strlen($form['email']->getData()) < 4
            || strlen(strlen($form['email']->getData()) > 30)) {
            throw new \Exception("Email must be between 4 and 30 symbols!");
        } else if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $form['email']->getData(), $email)) {
            throw new \Exception("The email {$form['email']->getData()} is not a valid email!");
        } else if (!preg_match("/^[A-z ]+$/", $form['fullName']->getData(), $name)) {
            throw new \Exception("Your name should contain only letters and at least 1 lowercase letter!");
        } else if (strlen($form['fullName']->getData()) < 4
            || strlen($form['fullName']->getData()) > 50) {
            throw new \Exception("Your name must be between 4 and 50 symbols!");
        } else if (isset($form['password']['first'])) {
            if (strlen($form['password']['first']->getData()) < 4) {
                throw new \Exception("Password must be at least 4 symbols long!");
            }
            $password = $form['password']['first']->getData();
            if (!preg_match("/^[a-z0-9]+$/", $password, $match)) {
                throw new \Exception("Password can contain only lowercase letters and digits!");
            }
        }

        return true;
    }

    /**
     * @param FormInterface $form
     * @return bool
     * @throws \Exception
     */
    public function validatePasswords(FormInterface $form): bool
    {
        if ($form['password']['first']->getData() !== $form['password']['second']->getData()) {
            throw new \Exception("Password mismatch!");
        }

        return true;
    }

    /**
     * @param FormInterface $form
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function isUniqueEmail(FormInterface $form, $data = null): bool
    {
        if (null !== $this->findOneByEmail($form['email']->getData() &&
                    $form['email']->getData() !== $data)) {
            throw new \Exception("Email already taken!");
        }

        return true;
    }

    public function edit(User $product): bool
    {
        return $this->userRepository->update($product);
    }
}