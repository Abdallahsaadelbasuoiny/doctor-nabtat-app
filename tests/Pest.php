<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
    ->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

function something()
{
    // ..
}
