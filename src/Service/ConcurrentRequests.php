<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ConcurrentRequests
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function fetch(): \Generator
    {
        $startedAt = hrtime(true);
        $urls = [
            'httpbin' => 'https://httpbin.org/delay/3',
            'httpbingo' => 'https://httpbingo.org/delay/1',
        ];
        $responses = [];

        // Start both requests before reading either response.
        foreach ($urls as $name => $url) {
            $responses[] = $this->client->request('GET', $url, [
                'user_data' => $name,
                'timeout' => 10,
                'max_duration' => 10,
            ]);
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
                'url' => $urls[$name],
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
