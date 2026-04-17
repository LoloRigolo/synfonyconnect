<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PostFormTest extends WebTestCase
{
    use DatabaseTestTrait;

    public function testAuthenticatedUserCanAccessPostForm(): void
    {
        $client = static::createClient();
        $user   = $this->createTestUser();

        $client->loginUser($user);
        $client->request('GET', '/post/nouveau');

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertSelectorExists('form.post-form');
    }

    protected function tearDown(): void
    {
        $this->removeTestUser();
        parent::tearDown();
    }
}
