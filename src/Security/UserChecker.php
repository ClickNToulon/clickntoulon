<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if ($user->getBanned() === true) {
            throw new CustomUserMessageAuthenticationException("Vous êtes banni !");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
    }
}