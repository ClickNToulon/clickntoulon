<?php

namespace App\Domain\Auth;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\{PasswordAuthenticatedUserInterface, UserInterface};
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    protected ?int $id;

    #[ORM\Column(type: Types::STRING, length: 45)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 45)]
    #[Assert\NotBlank]
    private string $surname;

    #[ORM\Column(type: Types::JSON)]
    private array $roles;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $password;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: true)]
    private ?string $phone;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $address;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    private ?string $postalCode;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $city;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private DateTime $updatedAt;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isBanned = false;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $googleID;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $microsoftID;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->createdAt = new DateTime('now', new DateTimeZone("Europe/Paris"));
        $this->updatedAt = new DateTime('now', new DateTimeZone("Europe/Paris"));
        $this->roles = ['ROLE_USER'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

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

    public function isBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getGoogleID(): ?string
    {
        return $this->googleID;
    }

    public function setGoogleID(?string $googleID): self
    {
        $this->googleID = $googleID;
        return $this;
    }

    public function getMicrosoftID(): ?string
    {
        return $this->microsoftID;
    }

    public function setMicrosoftID(?string $microsoftID): self
    {
        $this->microsoftID = $microsoftID;
        return $this;
    }
}