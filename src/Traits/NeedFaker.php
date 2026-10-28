<?php

declare(strict_types=1);

namespace App\Traits;

use Faker\Generator;
use Faker\Factory;

trait NeedFaker
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('es_ES');
    }
}
