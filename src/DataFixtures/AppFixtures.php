<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'email' => 'admin@symfoconnect.local',
                'username' => 'admin',
                'roles' => ['ROLE_ADMIN'],
            ],
            [
                'email' => 'alice@symfoconnect.local',
                'username' => 'alice',
                'roles' => [],
            ],
            [
                'email' => 'bob@symfoconnect.local',
                'username' => 'bob',
                'roles' => [],
            ],
        ];

        $users = [];

        foreach ($usersData as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setUsername($userData['username']);
            $user->setRoles($userData['roles']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
            $users[$userData['username']] = $user;
        }

        $postsData = [
            [
                'author' => 'admin',
                'description' => 'Bienvenue sur Symfoconnect. Ceci est le premier post de démonstration.',
                'image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=1200&q=80',
                'location' => 'Paris, France',
                'createdAt' => '2026-01-10 09:00:00',
            ],
            [
                'author' => 'alice',
                'description' => 'Déjeuner en terrasse après une matinée bien remplie.',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1200&q=80',
                'location' => 'Lyon, France',
                'createdAt' => '2026-02-14 12:30:00',
            ],
            [
                'author' => 'alice',
                'description' => 'Nouvelle randonnée, la lumière était incroyable au sommet.',
                'image' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1200&q=80',
                'location' => 'Annecy, France',
                'createdAt' => '2026-03-22 17:15:00',
            ],
            [
                'author' => 'bob',
                'description' => 'Soirée entre amis autour d\'un café et de quelques idées de projets.',
                'image' => 'https://images.unsplash.com/photo-1497215842964-222b430dc094?auto=format&fit=crop&w=1200&q=80',
                'location' => 'Bordeaux, France',
                'createdAt' => '2026-04-10 20:45:00',
            ],
        ];

        foreach ($postsData as $postData) {
            $post = new Post();
            $post->setDescription($postData['description']);
            $post->setImage($postData['image']);
            $post->setLocation($postData['location']);
            $post->setUser($users[$postData['author']]);
            $post->setCreatedAt(new DateTimeImmutable($postData['createdAt']));

            $manager->persist($post);
        }

        $manager->flush();
    }
}