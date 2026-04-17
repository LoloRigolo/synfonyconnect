<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ApiPostsTest extends WebTestCase
{
    public function testGetPostsReturnsValidJson(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/posts', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');

        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('member', $data, 'La réponse JSON doit contenir une clé "member"');
        $this->assertArrayHasKey('totalItems', $data, 'La réponse JSON doit contenir une clé "totalItems"');
        $this->assertIsInt($data['totalItems']);
    }

    public function testGetSinglePostReturns404ForUnknownId(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/posts/999999', [], [], [
            'HTTP_ACCEPT' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(404);
    }
}
