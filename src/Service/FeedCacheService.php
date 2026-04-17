<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\PostRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class FeedCacheService
{
    public const TTL = 300; // 5 minutes

    public function __construct(
        private readonly TagAwareCacheInterface $cache,
        private readonly PostRepository         $postRepository,
    ) {}

    /**
     * Retourne le feed depuis le cache ou le calcule et le met en cache.
     *
     * @return array<int, \App\Entity\Post>
     */
    public function getFeedForUser(User $user): array
    {
        return $this->cache->get(
            $this->key($user),
            function (ItemInterface $item) use ($user): array {
                $item->expiresAfter(self::TTL);
                $item->tag([$this->tag($user)]);

                return $this->postRepository->findFeedForUser($user);
            }
        );
    }

    /**
     * Invalide le cache du feed de chaque follower de l'auteur.
     * À appeler après création ou suppression d'un post.
     */
    public function invalidateFollowersOf(User $author): void
    {
        foreach ($author->getFollowers() as $follower) {
            $this->cache->invalidateTags([$this->tag($follower)]);
        }
    }

    private function key(User $user): string
    {
        return 'feed_user_' . $user->getId();
    }

    private function tag(User $user): string
    {
        return 'feed_user_' . $user->getId();
    }
}
