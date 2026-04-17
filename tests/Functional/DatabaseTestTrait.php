<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Helpers partagés entre les tests fonctionnels nécessitant la base de données.
 */
trait DatabaseTestTrait
{
    private function createTestUser(string $username = 'testuser', string $email = 'test@symfoconnect.test'): User
    {
        $container = static::getContainer();
        $em        = $container->get('doctrine')->getManager();

        // Réutilise l'utilisateur s'il existe déjà
        $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            return $existing;
        }

        /** @var UserPasswordHasherInterface $hasher */
        $hasher = $container->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPassword($hasher->hashPassword($user, 'password'));

        $em->persist($user);
        $em->flush();

        return $user;
    }

    private function removeTestUser(string $email = 'test@symfoconnect.test'): void
    {
        $em   = static::getContainer()->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);

        if ($user) {
            $em->remove($user);
            $em->flush();
        }
    }
}
