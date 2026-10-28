<?php

declare(strict_types=1);

namespace App\ProviderSimulator\Controller;

use App\ProviderSimulator\Service\AxaGenerateDummyResponse;
use App\ProviderSimulator\Service\PelayoGenerateDummyResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\ProviderSimulator\Request\AxaRequest;
use App\ProviderSimulator\Request\PelayoRequest;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProviderController extends AbstractController
{
    #[Route('/provider/axa/v1/quotes', name: 'app_provider_axa_quotes', methods: ['POST'])]
    public function axaAPIProvider(
        Request $request,
        AxaGenerateDummyResponse $axaResponse,
        ValidatorInterface $validator,
    ): JsonResponse {
        $axaRequest = new AxaRequest($request->toArray());
        $errors = $validator->validate($axaRequest);
        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => (string) $errors,
            ], 422);
        }
        return new JsonResponse($axaResponse->handle($axaRequest));
    }

    #[Route('/provider/pelayo/v2/offers', name: 'app_provider_pelayo_offers', methods: ['POST'])]
    public function pelayoAPIProvider(
        Request $request,
        PelayoGenerateDummyResponse $pelayoResponse,
        ValidatorInterface $validator,
    ): JsonResponse {
        $pelayoRequest = new PelayoRequest($request->toArray());
        $errors = $validator->validate($pelayoRequest);
        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => (string) $errors,
            ], 422);
        }
        return new JsonResponse($pelayoResponse->handle($pelayoRequest));
    }
}
