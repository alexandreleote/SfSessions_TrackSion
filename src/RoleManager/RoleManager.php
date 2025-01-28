<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class RoleManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function assignProfessorRole(User $user): void
    {
        $roles = $user->getRoles();

        if (!in_array('ROLE_PROFESSEUR', $roles)) {
            $roles[] = 'ROLE_PROFESSEUR';
            $user->setRoles($roles);

            $this->em->persist($user);
            $this->em->flush();
        }
    }
}
