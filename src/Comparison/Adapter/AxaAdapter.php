<?php

namespace App\Comparison\Adapter;

use App\Comparison\DTO\OfferResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AxaAdapter
{
    private HttpClientInterface $httpClient;

    public function __construct()
    {
        $this->httpClient = HttpClient::create();
    }

    public function handle(
        string $provider,
        array $providerSettings,
        array $formRequest,
    ): ResponseInterface {
        $response = $this->httpClient->request($providerSettings['verb'], $providerSettings["url"], [
            'user_data' => $provider,
            'timeout' => 10,
            'max_duration' => 10,
            'json' => $this->mapRequest($formRequest)
        ]);

        return $response;
    }

    /** @return list<OfferResponse> */
    public static function transform(ResponseInterface $response): array
    {
        $offers = [];

        foreach ($response->toArray() as $values) {
            $offers[] = new OfferResponse([
                'uuid' => $values['uuid'],
                'provider' => 'axa',
                'name' => $values['name'],
                'amountCents' => $values['price']['amountCents'],
                'currency' => $values['price']['currency'],
                'period' => $values['price']['period'],
                'civilLiability' => $values['civilLiability'],
                'glassCoverage' => $values['glassCoverage'],
                'theftCoverage' => $values['theftCoverage'],
                'fireCoverage' => $values['fireCoverage'],
                'ownDamage' => $values['ownDamage'],
                'roadsideAssistance' => $values['roadsideAssistance'],
                'replacementVehicle' => $values['replacementVehicle'],
                'totalLoss' => $values['totalLoss'],
            ]);
        }

        return $offers;
    }

    private function mapRequest(array $input): array
    {
        return [
            "vehicleUsage" => $input["vehicle_usage"],
            "birthDate" => $input["birth_date"],
            "plateNumber" => $input["plate_number"],
        ];
    }
}
