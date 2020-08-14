<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophet;

class BaseTestCase extends TestCase
{
    /**
     * @var Prophet
     */
    protected Prophet $prophet;

    /**
     *
     */
    protected function setUp(): void
    {
        $this->prophet = new Prophet;
    }

    /**
     *
     */
    protected function tearDown(): void
    {
        $this->prophet->checkPredictions();
    }
}
