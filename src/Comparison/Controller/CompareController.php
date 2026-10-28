<?php

declare(strict_types=1);

namespace App\Comparison\Controller;

use App\Comparison\Service\ConcurrentRequests;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class CompareController extends AbstractController
{
    #[Route('/compare', name: 'app_comparison_compare', methods: ['POST'])]
    public function index(
        Request $request,
        ConcurrentRequests $concurrentRequests,
    ): StreamedResponse {
        return new StreamedResponse(static function () use ($request, $concurrentRequests): void {
            foreach ($concurrentRequests->fetch($request) as $event) {
                echo json_encode($event, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES) . "\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }

                flush();
            }
        }, headers: [
            'Content-Type' => 'application/x-ndjson; charset=utf-8',
            'Cache-Control' => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
