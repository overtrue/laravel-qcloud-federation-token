<?php

namespace Tests;

use Illuminate\Support\Facades\Event;

class FeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    public function test_basic_features()
    {
        $this->assertTrue(true);
    }
}
