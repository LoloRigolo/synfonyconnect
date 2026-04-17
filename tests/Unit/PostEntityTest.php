<?php

namespace App\Tests\Unit;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class PostEntityTest extends TestCase
{
    private function makeUser(string $username): User
    {
        $user = new User();
        $user->setEmail($username . '@test.local');
        $user->setUsername($username);
        $user->setPassword('hashed');

        return $user;
    }

    public function testNewPostHasZeroLikes(): void
    {
        $post = new Post();

        $this->assertSame(0, $post->getLikesCount());
    }

    public function testAddLikeIncrementsCount(): void
    {
        $post = new Post();
        $user = $this->makeUser('alice');

        $post->addLike($user);

        $this->assertSame(1, $post->getLikesCount());
        $this->assertTrue($post->isLikedBy($user));
    }

    public function testAddLikeIsIdempotent(): void
    {
        $post = new Post();
        $user = $this->makeUser('bob');

        $post->addLike($user);
        $post->addLike($user); // double appel

        $this->assertSame(1, $post->getLikesCount(), 'Un double like ne doit pas dupliquer le compteur');
    }

    public function testRemoveLikeDecrementsCount(): void
    {
        $post = new Post();
        $user = $this->makeUser('alice');

        $post->addLike($user);
        $post->removeLike($user);

        $this->assertSame(0, $post->getLikesCount());
        $this->assertFalse($post->isLikedBy($user));
    }

    public function testCreatedAtIsSetOnConstruction(): void
    {
        $before = new \DateTimeImmutable();
        $post   = new Post();
        $after  = new \DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $post->getCreatedAt());
        $this->assertLessThanOrEqual($after, $post->getCreatedAt());
    }
}
