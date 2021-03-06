<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Mix;
use Tests\Traits\HotfixesSqlite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use HotfixesSqlite;

    public bool $seed = true;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->hotfixSqlite();

        parent::__construct($name, $data, $dataName);
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Spy mix, we don't need css/js during testing
        $this->spy(Mix::class);
    }
}
