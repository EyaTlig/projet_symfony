<?php
// src/Security/UserChecker.php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if ($user->getRole() === 'SERVICE_OWNER' && !$user->isValidated()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte Owner est en attente de validation par l’administrateur.'
            );
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAccountStatusException(
                'Votre compte est désactivé.'
            );
        }
    }

    public function checkPostAuth(UserInterface $user): void {}
}
