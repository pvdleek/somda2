<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewsControllerTest extends WebTestCase
{
    /**
     *
     */
    public function testIndexAction(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/1/news/', [], [], ['PHP_AUTH_USER' => 'user', 'PHP_AUTH_PW' => 'password']);
        var_dump($client->getResponse());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
