<?php

declare(strict_types=1);

namespace App\ProviderSimulator\Service;

use App\ProviderSimulator\Request\AxaRequest;
use App\ProviderSimulator\TraitsNeedFaker;

class AxaGenerateDummyResponse
{
    use NeedFaker;

    public function handle(AxaRequest $request): array
    {
        return $this->constructOutput($request);
    }

    private function constructOutput(AxaRequest $request): array
    {
        $quotes = [];
        $countQuotes = rand(0, 3);

        $isRisky = $this->riskyClient($request);
        $minPrice = $isRisky ? 15000 : 10000;
        $maxPrice = $isRisky ? 25000 : 15000;

        for ($i = 0; $i < $countQuotes; $i++) {
            $name = "Cobertura " . $this->faker->word();
            $quotes[] = [
                "uuid" => $this->faker->uuid,
                "name" => $name,
                "price" => [
                    "amountCents" => rand($minPrice, $maxPrice),
                    "currency" => "EUR",
                    "period" => $this->faker->randomElement(["monthly", "annual"]),
                ],
                "civilLiability" => (bool) rand(0, 1),
                "glassCoverage" => (bool) rand(0, 1),
                "theftCoverage" => (bool) rand(0, 1),
                "fireCoverage" => (bool) rand(0, 1),
                "ownDamage" => (bool) rand(0, 1),
                "roadsideAssistance" => (bool) rand(0, 1),
                "replacementVehicle" => (bool) rand(0, 1),
                "totalLoss" => (bool) rand(0, 1),
            ];
        }

        return $quotes;
    }

    private function riskyClient(AxaRequest $request): bool
    {
        $birthDate = new \DateTimeImmutable($request->birthDate);
        $age = $birthDate->diff(new \DateTimeImmutable('today'))->y;

        return $age >= 65 || $request->vehicleUsage === 'commercial';
    }
}
