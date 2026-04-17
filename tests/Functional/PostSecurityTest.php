<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PostSecurityTest extends WebTestCase
{
    public function testNewPostRedirectsToLoginWhenUnauthenticated(): void
    {
        $client = static::createClient();

        $client->request('GET', '/post/nouveau');

        $this->assertResponseRedirects('/login');
    }
}
