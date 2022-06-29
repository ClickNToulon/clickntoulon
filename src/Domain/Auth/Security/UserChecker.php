<?php

namespace App\Domain\Auth\Security;

use App\Domain\Auth\Exception\UserNotFoundException;
use App\Domain\Auth\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    /**
     * Vérifie que l'utilisateur a le droit de se connecter.
     */
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if ($user->isBanned() === true) {
            // Prévenir l'utilisateur qu'il est banni.
            throw new CustomUserMessageAuthenticationException("You are banned !");
        }
    }

    /**
     * Vérifie que l'utilisateur connecté a le droit de continuer.
     */
    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }
        if ($user->isVerified() === false) {
            // Prévenir l'utilisateur qu'il est banni.
            throw new CustomUserMessageAuthenticationException("Account not verified. Please verify your email address.");
        }
    }
}