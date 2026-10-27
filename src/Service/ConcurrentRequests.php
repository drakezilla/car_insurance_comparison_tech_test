<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ConcurrentRequests
{
    public function __construct(
        private HttpClientInterface $client,
        #[Autowire('%app.insurance_providers%')]
        private array $providers,
    ) {
    }

    public function fetch(): \Generator
    {
        $startedAt = hrtime(true);

        foreach ($this->providers as $provider => $data) {
            $adapter = new $data["adapter"]();
            try {
                $responses[] = $adapter->handle($provider, $data);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        // Process responses in completion order, not request order.
        foreach ($this->client->stream($responses) as $response => $chunk) {
            $name = $response->getInfo('user_data');

            try {
                if (!$chunk->isLast()) {
                    continue;
                }

                $status = $response->getStatusCode();
                $result = ['status' => $status, 'error' => $status >= 400 ? 'HTTP error' : null];
            } catch (TransportExceptionInterface $exception) {
                $response->cancel();
                $result = ['status' => null, 'error' => $exception->getMessage()];
            }

            yield [
                'type' => 'result',
                'service' => $name,
                'url' => $this->providers[$name]['url'],
                'completed_after_seconds' => round((hrtime(true) - $startedAt) / 1_000_000_000, 3),
                ...$result,
            ];
        }

        yield [
            'type' => 'completed',
            'total_seconds' => round((hrtime(true) - $startedAt) / 1_000_000_000, 3),
        ];
    }
}
