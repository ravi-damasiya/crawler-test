<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $hasher, UserRepository $userRepository)
    {
        $this->hasher = $hasher;
        $this->userRepository = $userRepository;
    }

    public function load(ObjectManager $objectManager)
    {
        $usersData = [
            [
                "name" => "admin",
                "password" => "admin_1234",
                "roles" => ['ROLE_ADMIN'],
            ],
            [
                "name" => "moderator",
                "password" => "moderator_1234",
                "roles" => ['ROLE_USER'],
            ]
        ];

        foreach ($usersData as $userData) {
            $user = $this->userRepository->findOneBy(['userName' => $userData['name']]);
            if (!$user) {
                $user = new User();
                $user->setUsername($userData['name']);
            }

            $user->setRoles($userData['roles']);

            $password = $this->hasher->hashPassword($user, $userData['password']);
            $user->setPassword($password);

            $objectManager->persist($user);
        }

        $objectManager->flush();
    }
}
