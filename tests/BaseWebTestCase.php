<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseWebTestCase extends WebTestCase
{
    protected static array $authHeaders = ['PHP_AUTH_USER' => 'test.somda', 'PHP_AUTH_PW' => 'test1234'];

    /**
     * @var KernelBrowser
     */
    protected KernelBrowser $client;

    /**
     * @param array $fixtures
     */
    public function loadRequiredFixtures(array $fixtures): void
    {
        $this->client = static::createClient();
        static::bootKernel();
        $entityManager = static::$kernel->getContainer()->get('doctrine.orm.entity_manager');

        // Load the database structure
        $statement = $entityManager->getConnection()->prepare(
            file_get_contents(__DIR__ . '/../database/empty_database.sql')
        );
        $statement->execute();
        unset($statement);

        $loader = new Loader();
        foreach ($fixtures as $fixture) {
            $loader->addFixture($fixture);
        }

        $purger = new ORMPurger();
        $executor = new ORMExecutor($entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
}
