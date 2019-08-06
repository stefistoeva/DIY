<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @Assert\NotNull()
     *
     * @Assert\Email(
     *     message="The email '{{ value }}' is not a valid email.",
     *     checkMX=false
     * )
     *
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @Assert\NotNull()
     *
     * @Assert\Length(
     *     min=4,
     *     minMessage="Password must be at least 4 symbols long."
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[a-z0-9]+&/",
     *     match=true,
     *     message="Password can contain only lowercase letters and digits."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     *
     * @Assert\Length(
     *     min = 4,
     *     max = 50,
     *     minMessage="Your name must be at least {{ limit }} characters long",
     *     maxMessage="Your name must be longer than {{ limit }} characters"
     * )
     *
     * @Assert\Regex(
     *     pattern="/^[A-z]+$/",
     *     match=true,
     *     message="Your name should contain only letters."
     * )
     *
     * @var string
     *
     * @ORM\Column(name="fullName", type="string", length=255)
     */
    private $fullName;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Article", mappedBy="author")
     */
    private $articles;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role")
     *
     * @ORM\JoinTable(name="users_roles",
     *     joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="role_id", referencedColumnName="id")}
     *     )
     */
    private $roles;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Order.php", mappedBy="buyer")
     */
    private $order;

    /**
     * @var ArrayCollection|Comment[]
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="author")
     */
    private $comments;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Product", mappedBy="author")
     *
     * @ORM\JoinColumn(name="authorId", referencedColumnName="id")
     */
    private $products;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="sender")
     */
    private $senderMessage;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="recipient")
     */
    private $recipientMessages;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->articles = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->senderMessage = new ArrayCollection();
        $this->recipientMessages = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];

        /**
         * @var Role $role
         */
        foreach ($this->roles as $role) {
            $stringRoles[] = $role->getRole();
        }

        return $stringRoles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @param Role $roles
     * @return User
     */
    public function addRole(Role $roles)
    {
        $this->roles[] = $roles;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getArticles(): ArrayCollection
    {
        return $this->articles;
    }

    /**
     * @param Article $article
     * @return User
     */
    public function addPost(Article $article)
    {
        $this->articles[] = $article;

        return $this;
    }

    /**
     * @param Article $article
     * @return bool
     */
    public function isAuthor(Article $article)
    {
        return $article->getAuthor()->getId() === $this->getId();
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product $product
     * @return User
     */
    public function addProduct(Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comments
     * @return User
     */
    public function setComments(Comment $comments)
    {
        $this->comments[] = $comments;
        return $this;
    }

    /**
     * @return ArrayCollection|Order[]
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return User
     */
    public function setOrder(Order $order)
    {
        $this->order[] = $order;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSenderMessage(): ArrayCollection
    {
        return $this->senderMessage;
    }

    /**
     * @param ArrayCollection $senderMessage
     */
    public function setSenderMessage(ArrayCollection $senderMessage): void
    {
        $this->senderMessage = $senderMessage;
    }

    /**
     * @return ArrayCollection
     */
    public function getRecipientMessages(): ArrayCollection
    {
        return $this->recipientMessages;
    }

    /**
     * @param ArrayCollection $recipientMessages
     */
    public function setRecipientMessages(ArrayCollection $recipientMessages): void
    {
        $this->recipientMessages = $recipientMessages;
    }
}

