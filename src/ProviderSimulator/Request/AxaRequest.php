<?php

declare(strict_types=1);

namespace App\ProviderSimulator\Request;

use Symfony\Component\Validator\Constraints as Assert;

class AxaRequest
{
    #[Assert\NotBlank(message: 'Campo requerido')]
    #[Assert\Choice(choices: ['private', 'commercial'])]
    public string $vehicleUsage;

    #[Assert\NotBlank(message: 'Campo requerido')]
    #[Assert\Date(message: 'Solo permitido fechas en formato YYYY-mm-dd')]
    public string $birthDate;

    #[Assert\NotBlank(message: 'Campo requerido')]
    #[Assert\Regex(
        pattern: '/\A[0-9]{4}[BCDFGHJKLMNPRSTVWXYZ]{3}\z/',
        message: 'Solo permitidos matriculas con formato: 1234BCD'
    )]
    public string $plateNumber;

    public function __construct(array $input)
    {
        $this->vehicleUsage = $input['vehicleUsage'] ?? '';
        $this->birthDate = $input['birthDate'] ?? '';
        $this->plateNumber = $input['plateNumber'] ?? '';
    }
}
