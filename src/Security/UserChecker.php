<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author ClickNToulon <developpeurs@clickntoulon.fr>
 */
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        if ($user->getIsBanned() === true) {
            // Prévenir l'utilisateur qu'il est banni.
            throw new CustomUserMessageAuthenticationException("You are banned !");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }
        if ($user->getIsVerified() === false) {
            // Prévenir l'utilisateur qu'il est banni.
            throw new CustomUserMessageAuthenticationException("Account not verified. Please verify your email address.");
        }
    }
}