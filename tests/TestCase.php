<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Wrap transactions on both the default and "shared" connections.
     *
     * @var array<int, string>
     */
    protected array $connectionsToTransact = ['sqlite', 'shared'];
}
