<?php


namespace AppBundle\Service\Users;


use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Service\Encryption\ArgonEncryption;
use AppBundle\Service\Encryption\EncryptionServiceInterface;
use AppBundle\Service\Roles\RoleServiceInterface;
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
}