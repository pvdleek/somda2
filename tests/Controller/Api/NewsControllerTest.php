<?php

namespace App\Tests\Controller\Api;

use App\Tests\BaseWebTestCase;
use App\Tests\DataFixtures\NewsDataFixture;

class NewsControllerTest extends BaseWebTestCase
{
    /**
     *
     */
    protected function setUp(): void
    {
        $this->loadRequiredFixtures([new NewsDataFixture()]);
    }

    /**
     *
     */
    public function testIndexAction(): void
    {
        $this->client->request('GET', '/api/1/news/', [], [], self::$authHeaders);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $response = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertCount(3, $response['data']);

        $this->assertSame('News item 1', $response['data'][0]['title']);
        $this->assertFalse($response['data'][0]['archived']);

        $this->assertSame('News item 3', $response['data'][1]['title']);
        $this->assertFalse($response['data'][1]['archived']);

        $this->assertSame('News item 2', $response['data'][2]['title']);
        $this->assertTrue($response['data'][2]['archived']);
    }
}
