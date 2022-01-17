<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\{PasswordAuthenticatedUserInterface, UserInterface};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=45)
     * @Assert\NotBlank()
     */
    private string $surname;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private ?string $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private ?string $phone;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $address;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private ?string $postalCode;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $city;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isBanned = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $googleID;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $dateTimeZoneFrance = new DateTimeZone("Europe/Paris");
        $this->createdAt = new DateTime('now', $dateTimeZoneFrance);
        $this->updatedAt = new DateTime('now', $dateTimeZoneFrance);
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getUsername(): string
    {
        return $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {return null;}

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {}

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getGoogleID(): ?string
    {
        return $this->googleID;
    }

    /**
     * @param string|null $googleID
     */
    public function setGoogleID(?string $googleID): self
    {
        $this->googleID = $googleID;

        return $this;
    }
}