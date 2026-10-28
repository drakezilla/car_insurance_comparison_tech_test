<?php

declare(strict_types=1);

namespace App\Comparison\DTO;

final class OfferResponse
{
    public string $uuid;
    public string $provider;
    public string $name;
    public int $amountCents;
    public string $currency;
    public string $period;

    public ?bool $civilLiability;
    public ?bool $glassCoverage;
    public ?bool $theftCoverage;
    public ?bool $fireCoverage;
    public ?bool $ownDamage;
    public ?bool $roadsideAssistance;
    public ?bool $replacementVehicle;
    public ?bool $totalLoss;
    public ?bool $legalAssistanceAndClaims;
    public ?bool $driverInjuryProtection;
    public ?bool $animalCollisionCoverage;

    public function __construct(array $input)
    {
        $this->fill($input);
    }

    private function fill(array $input): void
    {
        $this->uuid = $input['uuid'];
        $this->provider = $input['provider'];
        $this->name = $input['name'];
        $this->amountCents = $input['amountCents'];
        $this->currency = $input['currency'];
        $this->period = $input['period'];

        $this->civilLiability = $input['civilLiability'] ?? null;
        $this->glassCoverage = $input['glassCoverage'] ?? null;
        $this->theftCoverage = $input['theftCoverage'] ?? null;
        $this->fireCoverage = $input['fireCoverage'] ?? null;
        $this->ownDamage = $input['ownDamage'] ?? null;
        $this->roadsideAssistance = $input['roadsideAssistance'] ?? null;
        $this->replacementVehicle = $input['replacementVehicle'] ?? null;
        $this->totalLoss = $input['totalLoss'] ?? null;
        $this->legalAssistanceAndClaims = $input['legalAssistanceAndClaims'] ?? null;
        $this->driverInjuryProtection = $input['driverInjuryProtection'] ?? null;
        $this->animalCollisionCoverage = $input['animalCollisionCoverage'] ?? null;
    }
}