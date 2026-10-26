<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ConcurrentRequests;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ConcurrentRequestsController extends AbstractController
{
    #[Route('/concurrent/requests', name: 'app_concurrent_requests', methods: ['GET'])]
    public function index(ConcurrentRequests $requests): StreamedResponse
    {
        return new StreamedResponse(static function () use ($requests): void {
            foreach ($requests->fetch() as $event) {
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
