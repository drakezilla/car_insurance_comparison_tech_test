<?php

namespace App\Comparison\Adapter;

use App\Comparison\DTO\OfferResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PelayoAdapter
{
    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    public function handle(
        string $provider,
        array $data,
        array $formInput,
    ): ResponseInterface {
        $response = $this->httpClient->request($data['verb'], $data["url"], [
                'user_data' => $provider,
                'timeout' => 10,
                'max_duration' => 10,
                'json' => $this->mapRequest($formInput)
        ]);

        return $response;
    }

    public static function transform(ResponseInterface $response): array
    {
        $offers = [];

        foreach ($response->toArray() as $values) {
            $offers[] = new OfferResponse([
                'uuid' => $values['uuid'],
                'provider' => 'pelayo',
                'name' => $values['name'],
                'amountCents' => $values['priceCents'],
                'currency' => $values['currency'],
                'period' => $values['insuraPayperiod'],
                'civilLiability' => $values['thirdPartyLiability'],
                'glassCoverage' => $values['windowProtection'],
                'theftCoverage' => $values['theftProtection'],
                'ownDamage' => $values['vehicleDamageCoverage'],
                'roadsideAssistance' => $values['breakdownAssistance'],
                'replacementVehicle' => $values['courtesyCar'],
                'totalLoss' => $values['writeOffCoverage'],
                'legalAssistanceAndClaims' => $values['legalAssistanceAndClaims'],
                'driverInjuryProtection' => $values['driverInjuryProtection'],
                'animalCollisionCoverage' => $values['animalCollisionCoverage'],
            ]);
        }

        return $offers;
    }

    private function mapRequest(array $input): array
    {
        return [
            "usage" => $input["vehicle_usage"],
            "clientBirthDate" => $input["birth_date"],
            "plate" => $input["plate_number"],
        ];
    }
}
