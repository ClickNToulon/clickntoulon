<?php

namespace App\Domain\Auth;

use DateTime;
use DateTimeZone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use League\OAuth2\Client\Provider\GoogleUser;
use Stevenmaguire\OAuth2\Client\Provider\MicrosoftResourceOwner;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /** Used to upgrade (rehash) the user's password automatically over time. */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findAllQuery(): Query
    {
        return $this
            ->createQueryBuilder('u')
            ->where('u.roles NOT LIKE :role')
            ->setParameter('role','%"'.'ROLE_ADMIN'.'"%')
            ->getQuery();
    }

    public function findOrCreateFromGoogleOauth(
        GoogleUser $googleUser
    ): User|null {
        try {
            $user = $this->createQueryBuilder("u")
                ->where("u.googleID = :googleID")
                ->orWhere("u.email = :email")
                ->setParameters([
                    "email" => $googleUser->getEmail(),
                    "googleID" => $googleUser->getId(),
                ])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }
        if (isset($user) && $user instanceof User) {
            $data = $googleUser->toArray();
            if (
                $user->getGoogleID() === null &&
                $data["email_verified"] === true
            ) {
                try {
                    $user
                        ->setGoogleID($googleUser->getId())
                        ->setIsVerified(1)
                        ->setUpdatedAt(
                            new DateTime(
                                "now",
                                new DateTimeZone("Europe/Paris")
                            )
                        );
                } catch (Exception) {
                }
                $this->_em->persist($user);
                $this->_em->flush();
            }
            return $user;
        }
        $user = (new User())
            ->setName($googleUser->getLastName())
            ->setSurname($googleUser->getFirstName())
            ->setGoogleID($googleUser->getId())
            ->setEmail($googleUser->getEmail())
            ->setIsVerified(1);
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }

    public function findOrCreateFromMicrosoftOauth(
        MicrosoftResourceOwner $microsoftUser
    ): User|null {
        try {
            $user = $this->createQueryBuilder("u")
                ->where("u.microsoftID = :microsoftID")
                ->orWhere("u.email = :email")
                ->setParameters([
                    "email" => $microsoftUser->getEmail(),
                    "microsoftID" => $microsoftUser->getId(),
                ])
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException) {
        }
        if (isset($user) && $user instanceof User) {
            $data = $microsoftUser->toArray();
            dump($data);
            if (
                $user->getMicrosoftID() === null &&
                in_array($user->getEmail(), $data["emails"])
            ) {
                try {
                    $user
                        ->setMicrosoftID($microsoftUser->getId())
                        ->setIsVerified(1)
                        ->setUpdatedAt(
                            new DateTime(
                                "now",
                                new DateTimeZone("Europe/Paris")
                            )
                        );
                } catch (Exception) {
                }
                $this->_em->persist($user);
                $this->_em->flush();
            }
            return $user;
        }
        $user = (new User())
            ->setSurname($microsoftUser->getFirstname())
            ->setName($microsoftUser->getLastname())
            ->setMicrosoftID($microsoftUser->getId())
            ->setEmail($microsoftUser->getEmail())
            ->setPassword(null)
            ->setIsVerified(1);
        $this->_em->persist($user);
        $this->_em->flush();
        return $user;
    }
}
