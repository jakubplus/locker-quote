<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Tests\Framework\BaseWebTestCase;

final class QuoteControllerTest extends BaseWebTestCase
{
    public function testPostQuoteReturnsLocker(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/quote',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['length' => 60, 'width' => 35, 'height' => 10], JSON_THROW_ON_ERROR)
        );

        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);

        self::assertTrue($data['result']['fits']);
        self::assertSame('B', $data['result']['locker']['code']);
    }

    public function testPostQuoteValidatesInput(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/quote',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode(['length' => 0, 'width' => 10, 'height' => 10], JSON_THROW_ON_ERROR)
        );

        self::assertSame(422, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('error', $data);
    }

    public function testGetHelperWorks(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/quote?length=60&width=35&height=10');
        self::assertResponseIsSuccessful();
        $data = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($data['result']['fits']);
    }
}
