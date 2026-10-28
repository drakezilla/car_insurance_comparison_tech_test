<?php

declare(strict_types=1);

namespace App\ProviderSimulator\Service;

use App\ProviderSimulator\Request\PelayoRequest;
use App\ProviderSimulator\Traits\NeedFaker;

class PelayoGenerateDummyResponse
{
    use NeedFaker;

    public function handle(PelayoRequest $request): array
    {
        return $this->constructOutput($request);
    }

    private function constructOutput(PelayoRequest $request): array
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
                "amountCents" => rand($minPrice, $maxPrice),
                "currency" => "EUR",
                "insuraPayperiod" => $this->faker->randomElement(["monthly", "annual"]),
                "thirdPartyLiability" => (bool) rand(0, 1),
                "windowProtection" => (bool) rand(0, 1),
                "theftProtection" => (bool) rand(0, 1),
                "legalAssistanceAndClaims" => (bool) rand(0, 1),
                "vehicleDamageCoverage" => (bool) rand(0, 1),
                "breakdownAssistance" => (bool) rand(0, 1),
                "courtesyCar" => (bool) rand(0, 1),
                "writeOffCoverage" => (bool) rand(0, 1),
                "driverInjuryProtection" => (bool) rand(0, 1),
                "animalCollisionCoverage" => (bool) rand(0, 1),
            ];
        }

        return $quotes;
    }

    private function riskyClient(PelayoRequest $request): bool
    {
        $birthDate = new \DateTimeImmutable($request->clientBirthDate);
        $age = $birthDate->diff(new \DateTimeImmutable('today'))->y;

        return $age >= 70;
    }
}
