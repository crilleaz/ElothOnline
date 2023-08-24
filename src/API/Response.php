<?php
declare(strict_types=1);

namespace Game\API;

use Psr\Http\Message\ResponseInterface;

class Response extends \GuzzleHttp\Psr7\Response
{
    public static function json(array $body = []): self
    {
        return new Response(200, ['Content-Type' => 'application/json'], json_encode($body));
    }

    public static function terminateWith(ResponseInterface $response): void
    {
        if (headers_sent()) {
            throw new \RuntimeException('Headers were already sent. The response could not be emitted!');
        }

        $statusLine = sprintf('HTTP/%s %s %s', $response->getProtocolVersion(), $response->getStatusCode(), $response->getReasonPhrase());
        header($statusLine);

        foreach ($response->getHeaders() as $name => $values) {
            $responseHeader = sprintf('%s: %s', $name, $response->getHeaderLine($name));
            header($responseHeader, false);
        }

        echo $response->getBody();
        exit();
    }
}
